<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Entity\User;
use App\Entity\UserLike;
use App\Exception\JsonHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class LikeController extends AbstractController
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
     * @Route("/api/like/{article}", methods={"GET"}, name="api_like")
     */
    public function likeAction(Request $request, Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $apiToken = $request->headers->get('x-api-key');

        /** @var User $user */
        $user = $em->getRepository(User::class)
            ->findOneBy(['apiToken' => $apiToken]);
        if (!$user)
            throw new JsonHttpException(400, 'Authentication error');

        /** @var UserLike $like */
        $like = $em->getRepository(UserLike::class)
            ->findOneBy([
                'user' => $user,
                'article' => $article->getId()
            ]);
        if (!$like) {
            $like = new UserLike();
            $like->setUser($user)
                ->setArticle($article);
            $em->persist($like);
        } else {
            $em->remove($like);
        }
        $em->flush();

        return $this->json($like);
    }
}