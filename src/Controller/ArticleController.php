<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\Article\AddArticleType;
use App\Services\ArticleService;
use App\Services\UploaderService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/articles", name="articles")
     */
    public function listAction(Request $request, PaginatorInterface $paginator)
    {
        $query = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAllApprovedArticles();
        $articles = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('article/list.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/articles/add", name="articles_add")
     */
    public function addAction(Request $request, UploaderService $uploaderService, ArticleService $articleService)
    {
        $article = new Article();

        $form = $this->createForm(AddArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $article->setAuthor($this->getUser())
                ->setIsApproved(false);
            if (!empty($article->getThumbnail())) {
                $thumbnail = $uploaderService->upload(new UploadedFile($article->getThumbnail(), 'thumbnail'));
                $article->setThumbnail($thumbnail);
            }
            if (!empty($article->getPlainTags())) {
                $tags = $articleService->parseTags($article->getPlainTags(), $article);
                if ($tags != null) {
                    foreach ($tags as $tag) {
                        $em->persist($tag);
                    }
                }
            }
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('articles');
        }

        return $this->render('article/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/articles/{id}/show", name="articles_show")
     */
    public function showAction(Request $request, Article $article)
    {
        return $this->render('article/show.html.twig', [
            'request' => $request,
            'article' => $article
        ]);
    }
}
