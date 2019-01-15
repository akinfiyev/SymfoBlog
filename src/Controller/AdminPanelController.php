<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\User\EditProfileType;
use App\Services\UploaderService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
            ->createQueryBuilder('user')
            ->where('user.hasRequestBloggerRole = true')
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
        return $this->render('admin_panel/articles/list.html.twig', [
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
            ->createQueryBuilder('article')
            ->where('article.isApproved = false')
            ->orderBy('article.id', 'DESC')
            ->getQuery();
        $articles = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('admin_panel/articles/list.html.twig', [
            'articles' => $articles
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
}
