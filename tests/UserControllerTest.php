<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testRegistration()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/registration');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler->selectButton('Submit');
        $form = $crawler->selectButton('Register')->form();
        $form['user_register_post[username]'] = time().'test';
        $form['user_register_post[email]'] = time().'test@gmail.com';
        $form['user_register_post[plainPassword][first]'] = '1234';
        $form['user_register_post[plainPassword][second]'] = '1234';
        $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect());
    }
}
