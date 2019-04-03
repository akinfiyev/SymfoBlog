<?php

namespace App\Services;

class TagService
{
    /**
     * @param array $tags
     * @return array
     */
    public function createTagNamesArray(array $tags)
    {
        $tagNames = [];
        foreach ($tags as $tag) {
            if (!in_array($tag->getName(), $tagNames)) {
                $tagNames[] = $tag->getName();
            }
        }

        return $tagNames;
    }
}
