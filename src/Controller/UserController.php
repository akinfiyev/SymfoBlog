<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\PasswordEnteringEvent;
use App\EventListener\PasswordListener;
use App\Form\User\UserRegisterPostType;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserRegisterPostType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);
            return $this->redirectToRoute('default');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
