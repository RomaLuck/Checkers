<?php

namespace Src\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends BaseController
{
    public function index(Request $request): Response
    {
        return $this->render('/404.php');
    }
}