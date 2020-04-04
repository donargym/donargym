<?php

namespace App\Infrastructure\SymfonyController;

use App\Security\Domain\PasswordGenerator;
use App\Shared\Domain\EmailAddress;
use App\Shared\Domain\EmailTemplateType;
use App\Shared\Infrastructure\SymfonyMailer\SymfonyMailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class SecurityController extends BaseController
{
    private AuthenticationUtils $authenticationUtils;
    private Environment $twig;
    private RouterInterface $router;
    private SymfonyMailer $mailer;

    public function __construct(
        AuthenticationUtils $authenticationUtils,
        Environment $twig,
        RouterInterface $router,
        SymfonyMailer $mailer
    )
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->twig                = $twig;
        $this->router              = $router;
        $this->mailer              = $mailer;
    }

    /**
     * @Route("/login", name="loginRoute")
     */
    public function login()
    {
        $error        = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return new Response(
            $this->twig->render(
                'security/login.html.twig',
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
    public function preLogout()
    {
        unset($_SESSION['username']);

        return new RedirectResponse($this->router->generate('logout'));
    }

    /**
     * @Route("/inloggen/", name="getInloggenPage", methods={"GET"})
     *
     * @IsGranted("ROLE_INGELOGD")
     */
    public function getInloggenPageAction()
    {
        $user  = $this->getUser();
        $roles = $user->getRoles();
        switch ($roles[0]) {
            case 'ROLE_ADMIN':
                return $this->redirectToRoute('getAdminIndexPage');
                break;
            case 'ROLE_TRAINER':
            case 'ROLE_ASSISTENT':
            case 'ROLE_TURNSTER':
                return $this->redirectToRoute('getSelectieIndexPage');
                break;
            default:
                return $this->render(
                    'error/pageNotFound.html.twig',
                    array()
                );
        }
    }

    /**
     * @Route("/inloggen/new_pass/", name="getNewPassPage", methods={"GET", "POST"})
     */
    public function getNewPassPageAction(Request $request, EncoderFactoryInterface $encoderFactory)
    {
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
            'security/newPass.html.twig',
            array(
                'error' => $error,
            )
        );
    }
}
