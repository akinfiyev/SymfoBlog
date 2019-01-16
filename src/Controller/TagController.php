<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Services\TagService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    /**
     * @Route("/tag/{name}/show", name="tag_article")
     */
    public function tagArticleAction(Request $request, Tag $tag, PaginatorInterface $paginator)
    {
        $query = $this->getDoctrine()
            ->getRepository(Tag::class)
            ->createQueryBuilder('tag')
            ->where('tag.name = \'' . $tag->getName() . '\'')
            ->orderBy('tag.article', 'DESC')
            ->getQuery();
        $tags = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('tag/show_articles.html.twig', [
            'tags' => $tags,
        ]);
    }

    public function allTagsAction(TagService $tagService) {
        $tags = $this->getDoctrine()
            ->getRepository(Tag::class)
            ->findAll();
        $tags = $tagService->createTagNamesArray($tags);

        return $this->render('base/sidebar/tag/tags.html.twig', [
            'tags' => $tags
        ]);
    }
}
