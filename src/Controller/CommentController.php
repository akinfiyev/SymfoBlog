<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\Comment\AddCommentType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends AbstractController
{
    public function listCommentsAction(Request $request, Article $article, PaginatorInterface $paginator)
    {
        $comment = new Comment();
        $form = $this->createForm(AddCommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser())
                ->setArticle($article);
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
        }

        $query = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findAllByArticleId($article->getId());
        $comments = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('comment/list.html.twig', [
            'comments' => $comments,
            'form' => $form->createView()
        ]);
    }
}
