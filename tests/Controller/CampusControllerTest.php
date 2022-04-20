<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CampusControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/campus');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h5', 'Filter les campus');
    }
}
