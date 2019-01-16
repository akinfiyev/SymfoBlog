<?php

namespace App\EntityListener;

use App\Entity\Article;
use Doctrine\ORM\Mapping\PreFlush;

class ArticleListener
{
    /** @PreFlush */
    public function preFlushHandler(Article $article)
    {
        if ($article->getIsDeleted() == null)
            $article->setIsDeleted(false);
    }
}
