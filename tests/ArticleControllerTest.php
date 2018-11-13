<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleControllerTest extends WebTestCase
{
    public function testRoutes()
    {
        $client = static::createClient();
        $client->request('GET', '/article');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->request('GET', '/article/post');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
