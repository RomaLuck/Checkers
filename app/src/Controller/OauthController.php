<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OauthController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google')]
    public function connectGoogle(ClientRegistry $clientRegistry): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_game_list');
        }
        return $clientRegistry
            ->getClient('google')
            ->redirect([], [
                'profile', 'email'
            ]);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectGoogleCheck(): Response
    {

    }

    #[Route('/connect/facebook', name: 'connect_facebook')]
    public function connectFacebook(ClientRegistry $clientRegistry): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_game_list');
        }
        return $clientRegistry
            ->getClient('facebook')
            ->redirect([], [
                'profile', 'email'
            ]);
    }

    #[Route('/connect/facebook/check', name: 'connect_facebook_check')]
    public function connectFacebookCheck(): Response
    {

    }
}
