<?php

declare(strict_types=1);

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OauthController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google')]
    public function connectGoogle(ClientRegistry $clientRegistry): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_game_list');
        }
        return $clientRegistry
            ->getClient('google')
            ->redirect([]);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectGoogleCheck(): Response
    {
        // The logic is handled by AppOauthAuthenticator
    }

    #[Route('/connect/facebook', name: 'connect_facebook')]
    public function connectFacebook(ClientRegistry $clientRegistry): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_game_list');
        }
        return $clientRegistry
            ->getClient('facebook')
            ->redirect([]);
    }

    #[Route('/connect/facebook/check', name: 'connect_facebook_check')]
    public function connectFacebookCheck(): Response
    {
        // The logic is handled by AppOauthAuthenticator
    }

    #[Route('/connect/github', name: 'connect_github')]
    public function connectGithub(ClientRegistry $clientRegistry): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_game_list');
        }
        return $clientRegistry
            ->getClient('github')
            ->redirect([
                'user', 'user:email', 'repo',
            ]);
    }

    #[Route('/connect/github/check', name: 'connect_github_check')]
    public function connectGithubCheck(): Response
    {
        // The logic is handled by AppOauthAuthenticator
    }

    #[Route('/connect/microsoft', name: 'connect_microsoft')]
    public function connectMicrosoft(ClientRegistry $clientRegistry): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_game_list');
        }
        return $clientRegistry
            ->getClient('microsoft')
            ->redirect(
                ['wl.basic', 'wl.signin'],
                ['state' => 'OPTIONAL_CUSTOM_CONFIGURED_STATE']
            );
    }

    #[Route('/connect/microsoft/check', name: 'connect_microsoft_check')]
    public function connectMicrosoftCheck(): Response
    {
        // The logic is handled by AppOauthAuthenticator
    }
}
