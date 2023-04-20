<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {

        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // Get the last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'controller_name' => 'SecurityController',
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/login_check', name: 'app_login_check')]
    public function loginCheckAction(): void
    {
        // This code is never executed
    }

    #[Route('/logout', name: 'app_logout')]
    public function logoutAction(): void
    {
        // This code is never executed
    }
}