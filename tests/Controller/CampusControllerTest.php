<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CampusControllerTest extends WebTestCase
{
    public function testCampusPage():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/campus');
        $this->assertResponseIsSuccessful();

    }

    public function testCampusPage1()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/campus');
        $this->assertSelectorTextContains('h5', 'Filter les campus');
    }

    public function testCampusPageAccess()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/campus');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
