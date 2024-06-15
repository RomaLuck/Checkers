<?php

namespace Src\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationForm
{
    #[Assert\NotBlank(message: "Username is required")]
    #[Assert\Length(
        min: 8,
        max: 50,
        minMessage: "Your username must be at least {{ limit }} characters long",
        maxMessage: "Your username cannot be longer than {{ limit }} characters"
    )]
    public string $username;

    #[Assert\NotBlank(message: "Password is required")]
    #[Assert\Length(
        min: 8,
        max: 4096,
        minMessage: "Your password must be at least {{ limit }} characters long",
        maxMessage: "Your password cannot be longer than {{ limit }} characters"
    )]
    public string $password;

    #[Assert\NotBlank(message: "Password is required")]
    #[Assert\EqualTo(propertyPath: 'password', message: "Passwords must match")]
    public string $repeatPassword;

    public function __construct(Request $request)
    {
        $this->username = $request->request->getString('username');
        $this->password = $request->request->getString('password');
        $this->repeatPassword = $request->request->getString('repeat-password');
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRepeatPassword(): string
    {
        return $this->repeatPassword;
    }
}