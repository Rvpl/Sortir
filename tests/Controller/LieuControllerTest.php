<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LieuControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/lieu');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('th', 'Id');
    }
}
