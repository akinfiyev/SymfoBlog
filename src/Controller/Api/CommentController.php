<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 13.01.19
 * Time: 17:53
 */

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

class CommentController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getCommentsAction()
    {

    }

    public function addCommentAction()
    {

    }
}