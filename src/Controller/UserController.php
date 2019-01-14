<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\UserRegisterType;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/registration", name="user_registration")
     */
    public function registrationAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserRegisterType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile", name="user_profile")
     */
    public function showUserProfileAction(Request $request)
    {

    }

    public function showUserProfileSidebarAction(Request $request)
    {
        return $this->render('base/sidebar/user/profile.html.twig', []);
    }
}
