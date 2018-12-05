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
            $referer = $this->redirect($request
                ->headers
                ->get('referer'));

            $id = $request->get('post_id');
            if ($id !== null) {
                $em = $this->getDoctrine()->getManager();

                $article = $em->getRepository(Article::class)
                    ->find($id);
                if (!$article) {
                    return $referer;
                }

                $user = $this->get('security.token_storage')->getToken()->getUser();

                $like = $em->getRepository(UserLike::class)
                    ->findOneBy(['user_id' => $user, 'article_id' => $article]);
                if (!$like) {
                    $like = new UserLike();
                    $like->setUserId($user)
                        ->setArticleId($article);
                    $em->persist($like);
                    $em->flush();

                    return $referer;
                } else {
                    $em->remove($like);
                    $em->flush();

                    return $referer;
                }
            } else {
                return $referer;
            }
        } else {
            return $this->redirectToRoute('app_login');
        }
    }
}
