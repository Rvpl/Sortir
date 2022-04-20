<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParticipantControllerTest extends WebTestCase
{
    public function testPageParticipant():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/participant');
        $this->assertResponseIsSuccessful();
    }

    public function testPageParticipant1()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/participant');
        $this->assertSelectorTextContains('th', 'Id');
    }
}
