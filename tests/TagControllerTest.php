<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TagControllerTest extends WebTestCase
{
    public function testRoutes()
    {
        $client = static::createClient();
        $client->request('GET', '/tag/{name}/show');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
