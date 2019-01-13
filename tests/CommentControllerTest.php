<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentControllerTest extends WebTestCase
{
    public function testRoutes()
    {
        $client = static::createClient();
        $client->request('GET', '/comment');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
