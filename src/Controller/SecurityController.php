<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends BaseController
{
    /**
     * @Route("/login", name="login_route")
     */
    public function loginAction(AuthenticationUtils $authenticationUtils)
    {
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems         = $this->wedstrijdLinkItems[0];
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'security/login.html.twig',
            array(
                // last username entered by the user
                'last_username'      => $lastUsername,
                'error'              => $error,
                'wedstrijdLinkItems' => $this->groepItems,
            )
        );
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
    }

    /**
     * @Route("/pre_logout", name="pre_logout")
     */
    public function preLogout()
    {
        unset($_SESSION['username']);
        return $this->redirectToRoute('logout');
    }
}
