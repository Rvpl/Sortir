<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testLoginPage():void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
    }
    public function testLoginPage1()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

    public function testRegistrationWrongLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form= $crawler->selectbutton('Submit')->form([
            'Pseudo'=>'jesaispasTrop',
            'Password'=>'mauvaisMDP'
        ]);
        $client ->submit($form);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testRegistrationGoodLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form= $crawler->selectbutton('Sign in ')->form([
            'pseudo'=>'bonjour',
            'password'=>'111111'
        ]);
        $client ->submit($form);
        $this->assertResponseRedirects('/');
    }
}
