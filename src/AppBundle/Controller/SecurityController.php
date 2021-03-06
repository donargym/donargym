<?php

namespace AppBundle\Controller;

use AppBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;

class SecurityController extends BaseController
{

    /**
     * @Route("/login", name="login_route")
     */
    public function loginAction(Request $request)
    {
        $this->wedstrijdLinkItems = $this->getwedstrijdLinkItems();
        $this->groepItems = $this->wedstrijdLinkItems[0];
        $this->header = $this->getHeader();
        $this->calendarItems = $this->getCalendarItems();
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'security/login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error,
                'calendarItems' => $this->calendarItems,
                'header'        => $this->header,
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