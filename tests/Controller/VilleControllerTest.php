<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VilleControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/https://127.0.0.1:8000/ville/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h5', 'Filter les villes');
    }
}
