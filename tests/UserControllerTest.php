<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testRegisterPage()
    {
        $client = static::createClient();
        $client->request('GET', '/register');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/register');
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Registration form")')->count()
        );
    }
}
