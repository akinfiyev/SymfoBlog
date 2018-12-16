<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminPanelController extends AbstractController
{
    /**
     * @Route("/admin/", name="admin")
     */
    public function indexAction()
    {
        return $this->render('admin_panel/index.html.twig');
    }

    /**
     * @Route("/admin/articles/approval/", name="articles_approval")
     */
    public function articleApprovalAction(Request $request, ContainerInterface $container)
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findBy(['isApproved' => false]);

        $paginator = $container->get('knp_paginator');
        $articles = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );

        return $this->render('admin_panel/articles_approval.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/admin/articles/approval/approve", name="article_approve")
     */
    public function articleApproveAction(Request $request)
    {
        $post_id = $request->get('post_id');
        if ($post_id !== null) {
            $em = $this->getDoctrine()->getManager();

            $article = $em->getRepository(Article::class)
                ->find($post_id);
            if (!$article) {
                throw $this->createNotFoundException('The article does not exist.');
            }

            $article->setIsApproved(true);
            $em->merge($article);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        } else {
            throw $this->createNotFoundException('The article does not exist.');
        }
    }

    /**
     * @Route("/admin/users/", name="users_list")
     */
    public function userListingAction(Request $request, ContainerInterface $container)
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        $paginator = $container->get('knp_paginator');
        $users = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );

        return $this->render('admin_panel/users_list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/users/make_blogger/", name="make_user_blogger")
     */
    public function makeUserBloggerAction(Request $request)
    {
        $user_id = $request->get('user_id');
        if ($user_id !== null) {
            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository(User::class)
                ->find($user_id);
            if (!$user) {
                throw $this->createNotFoundException('The user does not exist.');
            }

            if (in_array('ROLE_BLOGGER', $user->getRoles())) {
                $roles = $user->getRoles();
                if (($key = array_search('ROLE_BLOGGER', $roles)) !== false) {
                    unset($roles[$key]);
                }
                $user->setRoles($roles);
            } else {
                $roles = $user->getRoles();
                $roles[] = 'ROLE_BLOGGER';
                $user->setRoles($roles);
            }
            $em->merge($user);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        } else {
            throw $this->createNotFoundException('The user does not exist.');
        }
    }

    /**
     * @Route("/admin/users/ban/", name="ban_user")
     */
    public function banUserAction(Request $request)
    {
        $user_id = $request->get('user_id');
        if ($user_id !== null) {
            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository(User::class)
                ->find($user_id);
            if (!$user) {
                throw $this->createNotFoundException('The user does not exist.');
            }

            if (in_array('ROLE_BANNED', $user->getRoles())) {
                $user->setRoles(['ROLE_USER']);
            } else {
                $user->setRoles(['ROLE_BANNED']);
            }
            $em->merge($user);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        } else {
            throw $this->createNotFoundException('The user does not exist.');
        }
    }
}
