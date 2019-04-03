<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 13.01.19
 * Time: 17:53
 */

namespace App\Controller\Api;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Exception\JsonHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Security;

class CommentController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("/api/comment/{article}/add", methods={"POST"}, name="api_comment_add")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns created comment object"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid api token"
     * )
     * @SWG\Parameter(
     *     name="article",
     *     in="path",
     *     type="integer",
     *     description="Article ID which in comment will be add"
     * )
     * @SWG\Parameter(
     *     name="comment",
     *     in="body",
     *     type="json",
     *     description="Comment object used for create comment",
     *     @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="text", type="string"),
     *         )
     * )
     * @SWG\Tag(name="Comment API")
     *
     * @Security(name="ApiAuth")
     */
    public function addCommentAction(Request $request, Article $article)
    {
        if (!$content = $request->getContent())
            throw new JsonHttpException(400, 'Bad Request');

        $em = $this->getDoctrine()->getManager();
        $apiToken = $request->headers->get('x-api-key');

        /** @var User $user */
        $user = $em->getRepository(User::class)
            ->findOneBy(['apiToken' => $apiToken]);
        if (!$user)
            throw new JsonHttpException(400, 'Authentication error');

        /* @var Comment $comment */
        $comment = $this->serializer->deserialize($request->getContent(), Comment::class, 'json');
        $comment->setAuthor($user)
            ->setArticle($article)
            ->setCreatedAt(new \DateTime())
            ->setIsDeleted(false);

        $errors = $this->validator->validate($comment);
        if (count($errors))
            throw new JsonHttpException(400, (string) $errors->get(0)->getPropertyPath() . ': ' . (string) $errors->get(0)->getMessage());

        $this->getDoctrine()->getManager()->persist($comment);
        $this->getDoctrine()->getManager()->flush();

        return $this->json($comment);
    }
}