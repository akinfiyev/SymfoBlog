<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\Comment\AddCommentPostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/post", name="post")
     */
    public function index(Request $request, ContainerInterface $container)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $user = $this->getUser();

        if ($id !== null) {
            $article = $em->getRepository(Article::class)
                ->find($id);
            if (!$article) {
                throw $this->createNotFoundException('The article does not exist.');
            }

            $comments = $article->getComments();
            $paginator = $container->get('knp_paginator');
            $comments = $paginator->paginate(
                $comments,
                $request->query->getInt('page', 1),
                $request->query->getInt('limit', 5)
            );

            $comment = new Comment();
            $form = $this->createForm(AddCommentPostType::class, $comment);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($user == null) {
                    return $this->redirectToRoute('app_login');
                }
                $comment->setAuthor($user)
                    ->setArticle($article)
                    ->setCreatedAt(new \DateTime());
                $em->persist($comment);
                $em->flush();

                return $this->redirect($request
                    ->headers
                    ->get('referer'));
            }

            return $this->render('post/index.html.twig', [
                'article' => $article,
                'form' => $form->createView(),
                'comments' => $comments,
                'tags' => $article->getTags(),
            ]);
        } else {
            throw $this->createNotFoundException('The article does not exist.');
        }
    }
}
