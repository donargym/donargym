<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Controller;

use App\Shared\Domain\Security\PasswordToken;
use App\Shared\Domain\Security\UserStorage;
use App\Shared\Domain\SystemClock;
use App\Shared\Infrastructure\DoctrineDbal\DbalUserCredentialRepository;
use App\Shared\Infrastructure\SymfonyMailer\SymfonyMailer;
use App\Shared\Infrastructure\SymfonyMailer\SymfonyPasswordConfirmationMailer;
use App\Shared\Infrastructure\SymfonyMailer\SymfonyResetPasswordMailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

final class SecurityController
{
    private AuthenticationUtils                 $authenticationUtils;
    private Environment                         $twig;
    private RouterInterface                     $router;
    private SymfonyMailer                       $mailer;
    private UserStorage                         $userStorage;
    private SessionInterface                    $session;
    private CsrfTokenManagerInterface           $csrfTokenManager;
    private DbalUserCredentialRepository        $userCredentialRepository;
    private SystemClock                         $clock;
    private SymfonyResetPasswordMailer          $resetPasswordMailer;
    private SymfonyPasswordConfirmationMailer   $passwordConfirmationMailer;
    private UserPasswordEncoderInterface        $userPasswordEncoder;

    public function __construct(
        AuthenticationUtils $authenticationUtils,
        Environment $twig,
        RouterInterface $router,
        SymfonyMailer $mailer,
        UserStorage $userStorage,
        SessionInterface $session,
        CsrfTokenManagerInterface $csrfTokenManager,
        DbalUserCredentialRepository $userRepository,
        SystemClock $clock,
        SymfonyResetPasswordMailer $resetPasswordMailer,
        SymfonyPasswordConfirmationMailer $passwordConfirmationMailer,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->authenticationUtils        = $authenticationUtils;
        $this->twig                       = $twig;
        $this->router                     = $router;
        $this->mailer                     = $mailer;
        $this->userStorage                = $userStorage;
        $this->session                    = $session;
        $this->csrfTokenManager           = $csrfTokenManager;
        $this->userCredentialRepository   = $userRepository;
        $this->clock                      = $clock;
        $this->resetPasswordMailer        = $resetPasswordMailer;
        $this->passwordConfirmationMailer = $passwordConfirmationMailer;
        $this->userPasswordEncoder        = $userPasswordEncoder;
    }

    /**
     * @Route("/login", name="loginRoute")
     */
    public function login(): Response
    {
        $error        = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return new Response(
            $this->twig->render(
                '@Shared/security/login.html.twig',
                [
                    'last_username' => $lastUsername,
                    'error'         => $error,
                ]
            )
        );
    }

    /**
     * @Route("/inloggen/", name="redirectToCorrectLoginPage", methods={"GET"})
     *
     * @IsGranted("ROLE_USER")
     */
    public function redirectToCorrectLoginPage(): Response
    {
        $user  = $this->userStorage->getUser();
        $roles = $user->getRoles();
        switch ($roles[0]) {
            case 'ROLE_ADMIN':
                return new RedirectResponse($this->router->generate('publicPictures'));
                break;
            case 'ROLE_COMPETITION_GROUP':
                return new RedirectResponse($this->router->generate('competitionGroupLoginIndex'));
                break;
            default:
                throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/inloggen/new_pass/", name="getNewCredentials", methods={"GET", "POST"})
     */
    public function getNewCredentials(Request $request)
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            try {
                $this->checkFormData($request);
            } catch (\LogicException $exception) {
                $this->session->getFlashBag()->add('error', $exception->getMessage());

                return new RedirectResponse($this->router->generate('getNewCredentials'));
            }
            $user = $this->userCredentialRepository->loadUserByUsername($request->request->get('email'));
            if (!$user) {
                $this->session->getFlashBag()->add('error', 'Emailadres kon niet worden gevonden');

                return new RedirectResponse($this->router->generate('getNewCredentials'));
            }
            $user->generateSetPasswordToken($this->clock);
            $this->userCredentialRepository->update($user);
            $this->resetPasswordMailer->notify($user);
            $this->session->getFlashBag()->add('success', 'Een mail om je wachtwoord opnieuw in te stellen is verstuurd');

            return new RedirectResponse($this->router->generate('loginRoute'));
        }

        return new Response($this->twig->render('@Shared/security/reset_password.html.twig'));
    }

    /**
     * @Route("/inloggen/set-password/{passwordToken}", name="setPassword", methods={"GET", "POST"})
     */
    public function setPassword(Request $request, string $passwordToken)
    {
        $user = $this->userCredentialRepository->findByPasswordToken(
            PasswordToken::fromString($passwordToken),
            $this->clock
        );
        if (!$user) {
            $this->session->getFlashBag()->add('error', 'Ongeldige url');

            return new RedirectResponse($this->router->generate('getNewCredentials'));
        }
        if ($request->isMethod(Request::METHOD_POST)) {
            try {
                $this->checkFormData($request);
            } catch (\LogicException $exception) {
                $this->session->getFlashBag()->add('error', $exception->getMessage());

                return new RedirectResponse($request->headers->get('referer'));
            }
            if ($request->request->get('password_again') !== $request->request->get('password')) {
                $this->session->getFlashBag()->add('error', 'De opgegeven wachtwoorden zijn niet gelijk');

                return new RedirectResponse($request->headers->get('referer'));
            }
            $user->setPassword($request->request->get('password'), $this->userPasswordEncoder);
            $this->userCredentialRepository->update($user);
            $this->passwordConfirmationMailer->notify($user);
            $this->session->getFlashBag()->add('success', 'Een nieuw wachtwoord is succesvol ingesteld');

            return new RedirectResponse($this->router->generate('loginRoute'));
        }

        return new Response($this->twig->render('@Shared/security/set_password.html.twig'));
    }

    private function checkFormData(Request $request): void
    {
        $token = new CsrfToken('new-password', $request->request->get('_csrf_token'));
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new \LogicException('Er is iets mis gegaan, probeer het opnieuw');
        }
    }
}
