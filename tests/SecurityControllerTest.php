<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/registration');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler->selectButton('Sign in');
        $form = $crawler->selectButton('Sign in')->form();
        $form['login[email]'] = 'test';
        $form['login[password]'] = 'test';
        $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals(true, $client->getResponse()->isRedirect("http://" . $client->getRequest()->server->get('SERVER_NAME') . "/"));
        $this->assertTrue($client->getResponse()->isRedirect());
    }
}
