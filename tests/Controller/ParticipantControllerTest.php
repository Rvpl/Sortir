<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParticipantControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/participant');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('th', 'Id');
    }
}
