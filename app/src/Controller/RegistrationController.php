<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security                    $security,
        EntityManagerInterface      $entityManager,
        ValidatorInterface          $validator
    ): Response
    {
        if ($request->isMethod('POST')) {
            $submittedToken = $request->getPayload()->get('_csrf_token');
            if (!$this->isCsrfTokenValid('register', $submittedToken)) {
                $this->addFlash('error', 'Token is invalid');
                return $this->redirectToRoute('app_register');
            }

            $user = new User();
            $user->setUsername($request->request->get('username'));
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $request->request->get('password')
                )
            );

            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('danger', $error->getMessage());
                }

                return $this->redirectToRoute('app_register');
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('registration/register.html.twig');
    }
}
