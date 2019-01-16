<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Exception\JsonHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    private $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("/api/user/registration", methods={"POST"}, name="api_user_registration")
     * @throws \Exception
     */
    public function registrationUserAction(Request $request)
    {
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }

        /* @var User $user */
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setRoles(['ROLE_USER'])
            ->setApiToken(Uuid::uuid4());

        $errors = $this->validator->validate($user);
        if (count($errors)) {
            throw new JsonHttpException(400, (string) $errors->get(0)->getPropertyPath() . ': ' . (string) $errors->get(0)->getMessage());
        }

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        return $this->json($user);
    }

    /**
     * @Route("/api/user/login", methods={"POST"}, name="api_user_login")
     * @throws \Exception
     */
    public function loginUserAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if (!$content = $request->getContent())
            throw new JsonHttpException(400, 'Bad Request');

        $data = json_decode($request->getContent(), true);
        if (!isset($data['email']) or !isset($data['plainPassword']))
            throw new JsonHttpException(400, 'Bad Request');

        $user = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class)
            ->findOneBy(['email' => $data['email']]);
        if (!$user)
            throw new JsonHttpException(400, 'User not found');

        if($passwordEncoder->isPasswordValid($user,$data['plainPassword'])){
            $user->setApiToken(Uuid::uuid4());
            $this->getDoctrine()->getManager()->flush();

            return ($this->json($user));
        };

        throw new JsonHttpException(400, 'Incorrect password');
    }

    /**
     * @Route("/api/user/profile", methods={"POST"}, name="api_user_profile")
     * @throws \Exception
     */
    public function showProfileAction(Request $request)
    {
        if (!$content = $request->getContent())
            throw new JsonHttpException(400, 'Bad Request');

        $data = json_decode($request->getContent(), true);
        if (!isset($data['api_token']))
            throw new JsonHttpException(400, 'Bad Request');

        $user = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class)
            ->findOneBy(['apiToken' => $data['api_token']]);
        if (!$user)
            throw new JsonHttpException(400, 'User not found');

        return ($this->json($user));
    }
}