<?php

namespace Src\Controller;

use Symfony\Component\HttpFoundation\Response;

class SecurityController extends BaseController
{
    public function index(): Response
    {
        return $this->render('/login.view.php');
    }

    public function login(): Response
    {
        //
    }

    public function logout(): Response
    {
        //
    }
}