<?php

namespace Src\Controller;

use Src\Form\RegistrationForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validation;

class RegistrationController extends BaseController
{
    public function index(Request $request): Response
    {
        return $this->render('/register.view.php', [
            'session' => $request->getSession()
        ]);
    }

    public function register(Request $request): Response
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();

        $form = new RegistrationForm($request);

        $session = $request->getSession();
        $flashes = $session->getFlashBag();

        $errors = $validator->validate($form);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $flashes->add($error->getPropertyPath(), $error->getMessage());
            }

            return new RedirectResponse('/registration');
        }

        return new RedirectResponse('/login');
    }
}