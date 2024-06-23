<?php

declare(strict_types=1);

namespace App\Security;

enum OauthClientDetector: string
{
    case GOOGLE = 'connect_google_check';
    case FACEBOOK = 'connect_facebook_check';
    case GITHUB = 'connect_github_check';
    case LINKEDIN = 'connect_linkedin_check';

    public function detectClientName(): string
    {
        return match ($this) {
            self::GOOGLE => 'google',
            self::FACEBOOK => 'facebook',
            self::GITHUB => 'github',
            self::LINKEDIN => 'linkedin',
        };
    }
}