<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\UserLike;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserLikeController extends AbstractController
{
    /**
     * @Route("/like/{id}", name="like_article")
     */
    public function likeArticleAction(Request $request, Article $article)
    {
        if ($this->getUser() != null) {
            $em = $this->getDoctrine()->getManager();
            $like = $em->getRepository(UserLike::class)
                ->findOneBy([
                    'user' => $this->getUser(),
                    'article' => $article->getId()
                ]);
            if (!$like) {
                $like = new UserLike();
                $like->setUser($this->getUser())
                    ->setArticle($article);
                $em->persist($like);
                $em->flush();

                return new JsonResponse(count($article->getArticleLikes()));
            } else {
                $em->remove($like);
                $em->flush();

                return new JsonResponse(count($article->getArticleLikes()));
            }
        } else {
            return $this->redirectToRoute('user_registration');
        }
    }

    /**
     * @Route("/like2/{id}", name="like_article2")
     */
    public function likeArticleAction2(Request $request, Article $article)
    {
        if($request->query->get('some_var_name')){
            //make something curious, get some unbelieveable data
            $arrData = ['output' => 'here the result which will appear in div'];
            return new JsonResponse($arrData);
        }

        return $this->render('like.html.twig');
    }
}
