<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegistrationPage():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();
    }
    public function testRegistrationPage1()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $this->assertSelectorTextContains('div', 'Pseudo');
    }
}
