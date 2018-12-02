<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\Article\ArticlePostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function indexAction(Request $request, ContainerInterface $container)
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        $paginator = $container->get('knp_paginator');
        $articles = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );

        return $this->render('article/index.html.twig', [
            'articles'=>$articles
        ]);
    }

    /**
     * @Route("/article/post", name="post_article")
     */
    public function postAction(Request $request)
    {
        $article = new Article();

        $form = $this->createForm(ArticlePostType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('default');
        }

        return $this->render('article/post.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
