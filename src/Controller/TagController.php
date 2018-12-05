<?php

namespace App\Controller;

use App\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{
    /**
     * @Route("/tag", name="tag")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $name = $request->get('name');

        if ($name !== null) {
            $tags = $em->getRepository(Tag::class)
                ->findBy(['name' => $name]);
        } else {
            throw $this->createNotFoundException('The tag does not exist.');
        }

        return $this->render('tag/index.html.twig', [
            'tag_name' => $name,
            'tags' => $tags,
        ]);
    }
}
