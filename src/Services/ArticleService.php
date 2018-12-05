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
        $tagsInput = explode(", ", $tagsInput);
        $tags = [];
        foreach ($tagsInput as $tagName) {
            $tag = new Tag();
            $tag->setName($tagName)
                ->setArticle($article);
            $tags[] = $tag;
        }

        return $tags;
    }
}