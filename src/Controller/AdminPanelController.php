<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\Article\AddArticleType;
use App\Form\User\EditProfileType;
use App\Services\ArticleService;
use App\Services\UploaderService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminPanelController extends AbstractController
{
    /**
     * @Route("/admin/", name="admin_panel")
     */
    public function indexAction()
    {
        return $this->render('admin_panel/index.html.twig');
    }

    /**
     * @Route("/admin/users/", name="admin_panel_users")
     */
    public function usersListAction(Request $request, PaginatorInterface $paginator)
    {
        $query = $this->getDoctrine()
            ->getRepository(User::class)
            ->createQueryBuilder('user')
            ->orderBy('user.id', 'ASC')
            ->getQuery();
        $users = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('admin_panel/user/list.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/users/blogger_requests", name="admin_panel_users_blogger_requests")
     */
    public function usersListBloggerRequestsAction(Request $request, PaginatorInterface $paginator)
    {
        $query = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAllWithBloggerRequest();
        $users = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('admin_panel/user/list.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/users/{id}/edit", methods={"GET", "POST"}, name="admin_panel_users_edit")
     */
    public function usersEditAction(Request $request, User $user, UploaderService $uploaderService)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($user);
        $avatar = $user->getAvatar();

        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (empty($user->getAvatar())) {
                $user->setAvatar($avatar);
            } else {
                $avatar = $uploaderService->upload(new UploadedFile($user->getAvatar(), 'avatar'));
                $user->setAvatar($avatar);
            }
            $em->flush();

            return $this->render('admin_panel/user/edit_profile.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'message' => 'success'
            ]);
        }

        return $this->render('admin_panel/user/edit_profile.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/users/{id}/make_blogger", name="admin_panel_users_make_blogger")
     */
    public function usersMakeBloggerAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        if (in_array('ROLE_BLOGGER', $roles = $user->getRoles())) {
            if (($key = array_search('ROLE_BLOGGER', $roles)) !== false) {
                unset($roles[$key]);
            }
            $user->setRoles($roles);
        } else {
            $roles[] = 'ROLE_BLOGGER';
            $user->setHasRequestBloggerRole(null);
            $user->setRoles($roles);
        }
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/admin/users/{id}/ban/", name="admin_panel_users_ban")
     */
    public function usersBanAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        if (in_array('ROLE_BANNED', $user->getRoles())) {
            $user->setRoles(['ROLE_USER']);
        } else {
            $user->setRoles(['ROLE_BANNED']);
        }
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/admin/articles/", name="admin_panel_articles")
     */
    public function articlesListAction(Request $request, PaginatorInterface $paginator)
    {
        $query = $this->getDoctrine()
            ->getRepository(Article::class)
            ->createQueryBuilder('article')
            ->orderBy('article.id', 'DESC')
            ->getQuery();
        $articles = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('admin_panel/article/list.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/admin/articles/unapproved", name="admin_panel_articles_unapproved")
     */
    public function articlesListUnapprovedAction(Request $request, PaginatorInterface $paginator)
    {
        $query = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAllUnapprovedArticles();
        $articles = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('admin_panel/article/list.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/admin/articles/add", methods={"GET","POST"}, name="admin_panel_articles_add")
     */
    public function articleAddAction(Request $request, UploaderService $uploaderService, ArticleService $articleService)
    {
        $article = new Article();

        $form = $this->createForm(AddArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $article->setAuthor($this->getUser())
                ->setIsApproved(false);
            if (!empty($article->getThumbnail())) {
                $thumbnail = $uploaderService->upload(new UploadedFile($article->getThumbnail(), 'thumbnail'));
                $article->setThumbnail($thumbnail);
            }
            if (!empty($article->getPlainTags())) {
                $tags = $articleService->parseTags($article->getPlainTags(), $article);
                if ($tags != null) {
                    foreach ($tags as $tag) {
                        $em->persist($tag);
                    }
                }
            }
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('admin_panel_articles');
        }

        return $this->render('admin_panel/article/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/articles/{id}/edit", name="admin_panel_articles_edit")
     */
    public function articleEditAction(Request $request, Article $article, UploaderService $uploaderService, ArticleService $articleService)
    {
        $savedThumbnail = $articleService->articlePreEdit($article);

        $form = $this->createForm(AddArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $articleService->articleThumbnailEdit($article, $savedThumbnail, $uploaderService);
            if (!empty($article->getPlainTags())) {
                $tags = $articleService->parseTags($article->getPlainTags(), $article);
                if ($tags != null) {
                    foreach ($tags as $tag) {
                        if (!$articleService->checkIfTagExist($tag, $em))
                            $em->persist($tag);
                    }
                }
            }
            $em->persist($article);
            $em->flush();

            return $this->render('admin_panel/article/edit.html.twig', [
                'article' => $article,
                'form' => $form->createView(),
                'message' => 'success'
            ]);
        }

        return $this->render('admin_panel/article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/articles/{id}/approve", name="admin_panel_articles_approve")
     */
    public function articleApproveAction(Request $request, Article $article)
    {
        $article->setIsApproved(true);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/admin/articles/{id}/delete", name="admin_panel_articles_delete")
     */
    public function articleDeleteAction(Request $request, Article $article)
    {
        $this->getDoctrine()->getManager()->remove($article);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/admin/comments", name="admin_panel_comments")
     */
    public function commentsListAction(Request $request, PaginatorInterface $paginator)
    {
        $query = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->createQueryBuilder('comment')
            ->orderBy('comment.id', 'DESC')
            ->getQuery();
        $comments = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('admin_panel/comment/list.html.twig', [
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/admin/comments/{id}/delete", name="admin_panel_comments_delete")
     */
    public function commentsDeleteAction(Request $request, Comment $comment)
    {
        $this->getDoctrine()->getManager()->remove($comment);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
