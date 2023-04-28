<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    public function testHomePage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'https://127.0.0.1:8000/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'ToDo&Co');

        // We check if the link to create an account exists
        $link = $crawler->selectLink('Créer un compte')->link();
        $this->assertEquals(1, $crawler->filter('a:contains("Créer un compte")')->count());
        $client->click($link);
        $this->assertResponseIsSuccessful();

        // We check if the link to login exists
        $link = $crawler->selectLink('Connexion')->link();
        $this->assertEquals(1, $crawler->filter('a:contains("Connexion")')->count());
        $client->click($link);
        $this->assertResponseIsSuccessful();
    }
}
