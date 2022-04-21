<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegistrationPage():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();
    }
    public function testRegistrationPage1()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $this->assertSelectorTextContains('div', 'Trouver un logo');
    }

    public function testRegistration()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $crawler->selectbutton('Enregistrer ')->form([
            'pseudo' =>'jesaispasTrop',
            'nom' =>'jesaispasTrop',
            'prenom' =>'jesaispasTrop',
            'telephone' =>'0606060606',
            'email' =>'jesaispasTrop@jesaispasTrop',
            'plainPassword' =>'111111',
            'campus' =>'Panam',
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div', 'Trouver un logo');
    }
}
