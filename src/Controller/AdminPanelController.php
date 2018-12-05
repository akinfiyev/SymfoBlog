<?php

namespace App\Controller;

use App\Entity\Article;
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
            throw $this->createNotFoundException('The article does not exist.');;
        }
    }
}
