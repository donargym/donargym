<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Controller;

use App\Security\Domain\PasswordGenerator;
use App\Shared\Domain\EmailAddress;
use App\Shared\Domain\EmailTemplateType;
use App\Shared\Domain\Security\UserStorage;
use App\Shared\Infrastructure\SymfonyMailer\SymfonyMailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

final class SecurityController
{
    private AuthenticationUtils   $authenticationUtils;
    private Environment           $twig;
    private RouterInterface       $router;
    private SymfonyMailer         $mailer;
    private UserStorage           $userStorage;

    public function __construct(
        AuthenticationUtils $authenticationUtils,
        Environment $twig,
        RouterInterface $router,
        SymfonyMailer $mailer,
        UserStorage $userStorage
    ) {
        $this->authenticationUtils = $authenticationUtils;
        $this->twig                = $twig;
        $this->router              = $router;
        $this->mailer              = $mailer;
        $this->userStorage         = $userStorage;
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
     * @Route("/login-check", name="loginCheck")
     */
    public function loginCheck()
    {
    }

    /**
     * @Route("/pre-logout", name="preLogout")
     */
    public function preLogout(): Response
    {
        unset($_SESSION['username']);

        return new RedirectResponse($this->router->generate('logout'));
    }

    /**
     * @Route("/inloggen/", name="redirectToCorrectLoginPage", methods={"GET"})
     *
     * @IsGranted("ROLE_INGELOGD")
     */
    public function redirectToCorrectLoginPage(): Response
    {
        $user  = $this->userStorage->getUser();
        $roles = $user->getRoles();
        switch ($roles[0]) {
            case 'ROLE_ADMIN':
                return new RedirectResponse($this->router->generate('publicPictures'));
                break;
            case 'ROLE_TRAINER':
            case 'ROLE_ASSISTENT':
            case 'ROLE_TURNSTER':
                return new RedirectResponse($this->router->generate('getSelectieIndexPage'));
                break;
            default:
                throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/inloggen/new_pass/", name="getNewCredentials", methods={"GET", "POST"})
     */
    public function getNewCredentials(Request $request, EncoderFactoryInterface $encoderFactory)
    {
        return new RedirectResponse($this->router->generate('loginRoute'));
        $error = "";
        if ($request->getMethod() == 'POST') {
            $email = $request->request->get('email');
            $em    = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT user
                    FROM App:User user
                    WHERE user.username = :email
                    OR user.email2 = :email
                    OR user.email3 = :email
                    '
            )
                ->setParameter('email', $email);
            $user  = $query->setMaxResults(1)->getOneOrNullResult();
            if (!$user) {
                $error = 'Dit Emailadres komt niet voor in de database';
            } else {
                $password = PasswordGenerator::generatePassword();
                $encoder  = $encoderFactory
                    ->getEncoder($user);
                $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
                $em->flush();
                $subject          = 'Inloggegevens website Donar';
                $templateLocation = 'mails/new_password.txt.twig';
                $parameters       = [
                    'email1'   => $user->getUsername(),
                    'email2'   => $user->getEmail2(),
                    'email3'   => $user->getEmail3(),
                    'password' => $password,
                ];
                $this->mailer->sendEmail(
                    $subject,
                    EmailAddress::fromString($user->getUsername()),
                    $templateLocation,
                    EmailTemplateType::TEXT(),
                    $parameters
                );
                if ($user->getEmail2()) {
                    $this->mailer->sendEmail(
                        $subject,
                        EmailAddress::fromString($user->getEmail2()),
                        $templateLocation,
                        EmailTemplateType::TEXT(),
                        $parameters
                    );
                }
                if ($user->getEmail3()) {
                    $this->mailer->sendEmail(
                        $subject,
                        EmailAddress::fromString($user->getEmail3()),
                        $templateLocation,
                        EmailTemplateType::TEXT(),
                        $parameters
                    );
                }
                $error = 'Een nieuw wachtwoord is gemaild';
            }
        }

        return $this->render(
            '@Shared/security/request_new_password.html.twig',
            [
                'error' => $error,
            ]
        );
    }
}
