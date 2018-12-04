<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\UserLike;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    /**
     * @Route("/like", name="like")
     */
    public function index(Request $request)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            $id = $request->get('post_id');
            if ($id !== null) {
                $article = $this->getDoctrine()
                    ->getRepository(Article::class)
                    ->find($id);

                if (!$article) {
                    return new Response('Error: no article with id ' . $id . '.');
                }

                $user = $this->get('security.token_storage')->getToken()->getUser();

                $like = new UserLike();
                $like->setUserId($user)
                    ->setArticleId($article);

                $em = $this->getDoctrine()->getManager();
                $em->persist($like);
                $em->flush();

                return $this->redirect($request
                    ->headers
                    ->get('referer'));
            } else {
                return new Response('Error: post_id is null.');
            }
        } else {
            return $this->redirectToRoute('app_login');
        }
    }
}
