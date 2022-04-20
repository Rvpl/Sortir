<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VilleControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ville');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h5', 'Filter les villes');
    }
}
