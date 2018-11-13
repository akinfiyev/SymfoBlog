<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\Article\ArticlePostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function indexAction()
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

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
