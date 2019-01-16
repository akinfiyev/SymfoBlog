<?php

namespace App\Services;

use App\Entity\Article;
use App\Entity\Tag;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArticleService
{
    public function parseTags(string $plainTags, Article $article)
    {
        $plainTags = trim($plainTags);
        $plainTags = explode(", ", $plainTags);
        $plainTags = array_unique($plainTags);

        $tags = [];
        foreach ($plainTags as $plainTag) {
            if (trim($plainTag) == '') {
                continue;
            }
            $tag = new Tag();
            $tag->setName($plainTag)
                ->setArticle($article);
            $tags[] = $tag;
        }

        return $tags;
    }

    /**
     * Returned saved at article thumbnail
     *
     * @param Article $article
     * @return string|null
     *
     */
    public function articlePreEdit(Article $article)
    {
        if (count($article->getTags())) {
            $article->setPlainTags(implode(", ", $article->getTags()->toArray()));
        }
        if ($article->getThumbnail() != null && $article->getThumbnail() != '') {
            return $article->getThumbnail();
        } else {
            return '';
        }
    }

    /**
     * Update article thumbnail at the edit action
     *
     * @param Article $article
     */
    public function articleThumbnailEdit(Article $article, $savedThumbnail, UploaderService $uploaderService)
    {
        if (empty($article->getThumbnail()) && !empty($savedThumbnail)) {
            $article->setThumbnail($savedThumbnail);
        } else {
            if (!empty($article->getThumbnail())) {
                $thumbnail = $uploaderService->upload(new UploadedFile($article->getThumbnail(), 'thumbnail'));
                $article->setThumbnail($thumbnail);
            }
        }
    }

    /**
     * Check if tag exist to avoid duplication
     *
     * @param Tag $tag
     * @return bool
     */
    public function checkIfTagExist(Tag $tag, ObjectManager $em)
    {
        $result = $em->getRepository(Tag::class)
            ->findOneBy([
                'name' => $tag->getName(),
                'article' => $tag->getArticle()->getId(),
            ]);

        return $result != null ? true : false;
    }
}