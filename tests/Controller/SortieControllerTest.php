<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SortieControllerTest extends WebTestCase
{
    public function testPageSortie():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }
    public function testPageSortie1()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertSelectorTextContains('div', 'Campus');
    }


}
