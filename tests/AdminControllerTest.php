<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testRoutes()
    {
        $client = static::createClient();

        $client->request('GET', '/admin');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->request('GET', '/admin/articles');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->request('GET', '/admin/articles/add');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->request('POST', '/admin/articles/add');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->request('GET', '/admin/articles/{id}/edit');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->request('POST', '/admin/articles/{id}/edit');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->request('GET', '/admin/articles/{id}/delete');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->request('GET', '/admin/users');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->request('GET', '/admin/users');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->request('GET', '/admin/users/{id}/edit');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->request('POST', '/admin/users/{id}/edit');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->request('GET', '/admin/comments');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->request('GET', '/admin/comments/{id}/delete');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
