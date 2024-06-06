<?php

namespace Src\Controller;

use Src\Entity\User;
use Src\Form\LoginForm;
use Src\Helpers\EntityManagerFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validation;

class SecurityController extends BaseController
{
    public function index(Request $request): Response
    {
        return $this->render('/login.view.php', [
            'session' => $request->getSession()
        ]);
    }

    public function login(Request $request): Response
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();

        $form = new LoginForm($request);

        $session = $request->getSession();
        $flashes = $session->getFlashBag();

        $errors = $validator->validate($form);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $flashes->add($error->getPropertyPath(), $error->getMessage());
            }

            return new RedirectResponse('/login');
        }

        $entityManager = EntityManagerFactory::create();
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $form->getUsername()]);
        if ($user === null || !password_verify($form->getPassword(), $user->getPassword())) {
            $flashes->add('warning', 'Password is wrong or user not found');
            return new RedirectResponse('/login');
        }

        $session->set('user', $user->getId());

        return new RedirectResponse('/');
    }

    public function logout(Request $request): Response
    {
        $session = $request->getSession();
        $session->clear();
    }
}