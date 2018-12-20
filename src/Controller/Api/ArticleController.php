<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Entity\User;
use App\Form\Article\ArticlePostType;
use App\Services\ArticleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @Route("api/article/{id}", name="api_get_article")
     */
    public function showArticleAction(Article $article)
    {
        return $this->json(['article' => $article]);
    }

    /**
     * @Route("api/articles", name="api_get_articles")
     */
    public function showArticlesAction()
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        return $this->json(['articles' => $articles]);
    }

    /**
     * @Route("api/article_add/", methods={"POST"}, name="api_add_article")
     */
    public function createArticleAction(Request $request, ValidatorInterface $validator)
    {
        $data = json_decode($request->getContent(), true);
        $article = new Article();
        $article->setText($data['article']['text']);
        $article->setTitle($data['article']['title']);
        $article->setCreatedAt(new \DateTime($data['article']['createdAt']));
        $article->setIsApproved($data['article']['isApproved']);

        $em = $this->getDoctrine()->getManager();
        $article->setAuthor($em->getRepository(User::class)
            ->find($data['article']['author_id']));

        $errors = $validator->validate($article);
        if (count($errors) > 0) {
            $errorsString = (string)$errors;
            return new Response("Article haven't been saved: " . $errorsString);
        }

        $em->persist($article);
        $em->flush();

        return new Response("Article have been saved successfully!");
    }
}
