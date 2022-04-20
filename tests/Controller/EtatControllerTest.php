<?php

namespace App\Tests\Controller;

use App\Repository\EtatRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EtatControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/https://127.0.0.1:8000/etat/');
        $etatRepository = static::getContainer()->get(EtatRepository::class);
        $testUser = $etatRepository->findOneByLibelle('créée');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('td', 'créée');
    }
}
