<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Security\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function loginAction(AuthenticationUtils $authenticationUtils)
    {
        $user = new User();
        $user->setEmail($authenticationUtils->getLastUsername());

        $error = $authenticationUtils->getLastAuthenticationError();

        $form = $this->createForm(LoginType::class, $user, [
            'action' => $this->generateUrl('login_check')
        ]);

        return $this->render('base/sidebar/security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }
}
