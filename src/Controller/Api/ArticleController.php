<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Exception\JsonHttpException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Swagger\Annotations as SWG;

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
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns article object array"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Page not found"
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="path",
     *     type="integer",
     *     description="Articles page"
     * )
     * @SWG\Tag(name="Article API")
     */
    public function showArticlesAction(Request $request, PaginatorInterface $paginator, string $page)
    {
        $query = $this->getDoctrine()
            ->getRepository(Article::class)
            ->createQueryBuilder('article')
            ->where('article.isApproved = true')
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
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns article object"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Article not found"
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="Article ID"
     * )
     * @SWG\Tag(name="Article API")
     */
    public function showArticleByIdAction(Article $article)
    {
        return $this->json(['article' => $article]);
    }
}
