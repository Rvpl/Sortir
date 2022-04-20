<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LieuControllerTest extends WebTestCase
{
    public function testLieuPage():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/lieu');
        $this->assertResponseIsSuccessful();
    }

    public function testLieuPage1()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/lieu');
        $this->assertSelectorTextContains('th', 'Id');
    }
}
