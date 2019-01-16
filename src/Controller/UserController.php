<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\EditProfileType;
use App\Form\User\UserRegisterType;
use App\Services\UploaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;

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
            $user->setRoles(['ROLE_USER'])
                ->setApiToken($uuid4 = Uuid::uuid4());
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('default');
        }

        return $this->render('user/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/edit", methods={"GET", "POST"}, name="user_profile_edit")
     */
    public function editUserProfileAction(Request $request, UploaderService $uploaderService)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->getUser());
        $avatar = $user->getAvatar();

        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (empty($user->getAvatar())) {
                $user->setAvatar($avatar);
            } else {
                $avatar = $uploaderService->upload(new UploadedFile($user->getAvatar(), 'avatar'));
                $user->setAvatar($avatar);
            }
            $em->flush();

            return $this->render('user/edit_profile.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'message' => 'success'
            ]);
        }

        return $this->render('user/edit_profile.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/profile/edit/role_blogger_request", name="user_role_blogger_request")
     */
    public function roleBloggerRequestAction(Request $request)
    {
        $this->getUser()->setHasRequestBloggerRole(true);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($request
            ->headers
            ->get('referer'));
    }

    public function showUserProfileSidebarAction()
    {
        return $this->render('base/sidebar/user/profile.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}
