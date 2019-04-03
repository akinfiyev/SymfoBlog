<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Exception\JsonHttpException;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Swagger\Annotations as SWG;

class UserController extends AbstractController
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
     * @Route("/api/user/registration", methods={"POST"}, name="api_user_registration")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns user profile"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid request"
     * )
     * @SWG\Parameter(
     *     name="user",
     *     in="body",
     *     type="json",
     *     description="User object used for user registration",
     *     @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="username", type="string"),
     *            @SWG\Property(property="email", type="string"),
     *            @SWG\Property(property="plainPassword", type="string"),
     *         )
     * )
     * @SWG\Tag(name="User API")
     */
    public function registrationUserAction(Request $request)
    {
        if (!$content = $request->getContent())
            throw new JsonHttpException(400, 'Bad Request');

        /* @var User $user */
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setRoles(['ROLE_USER'])
            ->setApiToken(Uuid::uuid4());

        $errors = $this->validator->validate($user);
        if (count($errors))
            throw new JsonHttpException(400, (string) $errors->get(0)->getPropertyPath() . ': ' . (string) $errors->get(0)->getMessage());

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        return $this->json($user);
    }

    /**
     * @Route("/api/user/login", methods={"POST"}, name="api_user_login")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns user profile"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid request or credentials"
     * )
     * @SWG\Parameter(
     *     name="user",
     *     in="body",
     *     type="json",
     *     description="User object used for user authentication",
     *     @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="email", type="string"),
     *            @SWG\Property(property="plainPassword", type="string"),
     *         )
     * )
     * @SWG\Tag(name="User API")
     */
    public function loginUserAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if (!$content = $request->getContent())
            throw new JsonHttpException(400, 'Bad Request');

        /* @var User $user */
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $plainPassword = $user->getPlainPassword();
        if (empty($plainPassword) or empty($user->getEmail()))
            throw new JsonHttpException(400, 'Bad Request');

        $user = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class)
            ->findOneBy(['email' => $user->getEmail()]);
        if (!$user)
            throw new JsonHttpException(400, 'Authentication error');

        if($passwordEncoder->isPasswordValid($user,$plainPassword)){
            $user->setApiToken(Uuid::uuid4());
            $this->getDoctrine()->getManager()->flush();

            return ($this->json($user));
        };

        throw new JsonHttpException(400, 'Incorrect password');
    }

    /**
     * @Route("/api/user/profile", methods={"GET"}, name="api_user_profile")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns user profile"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Invalid api token"
     * )
     * @SWG\Tag(name="User API")
     *
     * @Security(name="ApiAuth")
     */
    public function showProfileAction(Request $request)
    {
        $apiToken = $request->headers->get('x-api-key');

        /* @var User $user */
        $user = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class)
            ->findOneBy(['apiToken' => $apiToken]);
        if (!$user)
            throw new JsonHttpException(400, 'Authentication error');

        return ($this->json($user));
    }
}