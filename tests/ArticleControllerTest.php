<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleControllerTest extends WebTestCase
{
    public function testRoutes()
    {
        $client = static::createClient();

        $client->request('GET', '/articles');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', '/articles/add');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->request('POST', '/articles/add');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->request('GET', '/articles/{id}/show');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', '/articles/{id}/edit');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->request('POST', '/articles/{id}/edit');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
