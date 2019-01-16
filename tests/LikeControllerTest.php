<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LikeControllerTest extends WebTestCase
{
    public function testRoutes()
    {
        $client = static::createClient();
        $client->request('GET', '/like/{id}');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
