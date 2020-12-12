<?php

namespace App\Controller;


use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
            if ($error) {
                $this->addFlash(
                    'error',
                    'podano błędne dane logowania'
                );
            }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
        ));
    }

    /**
     * @Route("/logout", name="app_logout", methods="GET")
     * @throws Exception
     */
    public function logout()
    {

    }
    /**
     * @Route("/logout_message", name="logout_message")
     */
    public function logoutMessage()
    {
        $this->addFlash(
            'notice',
            'wylogowano!'
        );
        return $this->redirect('/');
    }
}
