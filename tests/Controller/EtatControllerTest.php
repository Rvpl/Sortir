<?php

namespace App\Tests\Controller;

use App\Repository\EtatRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EtatControllerTest extends WebTestCase
{
    public function testEtatController():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/etat');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('td', 'créée');
    }

    public function testEtatController1(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/etat');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('td', 'créée');
    }
}
