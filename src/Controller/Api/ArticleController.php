<?php

namespace App\Controller\Api;

use App\Entity\Article;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ArticleController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route("/api/articles/{page}", methods={"GET"}, name="api_articles")
     */
    public function showArticlesAction(Request $request, PaginatorInterface $paginator, string $page)
    {
        $query = $this->getDoctrine()
            ->getRepository(Article::class)
            ->createQueryBuilder('article')
            ->where('article.isApproved = true')
            ->andWhere('article.isDeleted = false')
            ->orderBy('article.id', 'DESC')
            ->getQuery();
        $articles = $paginator->paginate(
            $query,
            $request->query->getInt('page', $page),
            5
        );

        return $this->json($articles);
    }

    /**
     * @Route("/api/articles/{id}/show", methods={"GET"}, name="api_articles_show")
     */
    public function showArticleByIdAction(Article $article)
    {
        return $this->json(['article' => $article]);
    }
}
