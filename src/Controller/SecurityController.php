<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function loginAction(Request $request): Response
    {
        $authentificationUtils = $request->attributes->get('security.authentication_utils');

        // Get the login error if there is one
        $error = $authentificationUtils->getLastAuthenticationError();
        // Get the last username entered by the user
        $lastUsername = $authentificationUtils->getLastUsername();

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