<?php

namespace App\Controller\Api;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("api/articles", name="api_articles")
     */
    public function showArticlesAction()
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        return $this->json(['articles' => $articles]);
    }

    /**
     * @Route("api/articles/tag/{tagName}/show", name="api_articles_show")
     */
    public function showArticlesByTagAction(String $tagName)
    {

    }

    /**
     * @Route("api/articles/{id}/show", name="api_articles_show")
     */
    public function showArticleByIdAction(Article $article)
    {
        return $this->json(['article' => $article]);
    }
}
