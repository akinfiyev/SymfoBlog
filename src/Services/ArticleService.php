<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 05.12.18
 * Time: 22:37
 */

namespace App\Services;


use App\Entity\Article;
use App\Entity\Tag;

class ArticleService
{
    public function generateTags(?string $tagsInput, Article $article)
    {
        $tagsInput = trim($tagsInput);
        $tagsInput = explode(", ", $tagsInput);
        $tagsInput = array_unique($tagsInput);
        $tags = [];
        foreach ($tagsInput as $tagName) {
            $tagName = trim($tagName);
            if ($tagName == '') continue;

            $tag = new Tag();
            $tag->setName($tagName)
                ->setArticle($article);
            $tags[] = $tag;
        }

        return $tags;
    }
}