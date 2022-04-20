<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VilleControllerTest extends WebTestCase
{
    public function testSomething():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ville');
        $this->assertResponseIsSuccessful();
    }

    public function testCampusPage1()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ville');
        $this->assertSelectorTextContains('h5', 'Filter les villes');
    }

    public function testCampusPageAccess()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ville');
        $this->assertResponseStatusCodeSame(\Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);
    }
}
