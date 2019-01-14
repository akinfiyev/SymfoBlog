<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 14.01.19
 * Time: 18:26
 */

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

class SecurityController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function loginAction()
    {

    }
}