<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserPageTest extends WebTestCase
{
    public function testUserPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('table');
        $this->assertSelectorExists('thead');
        $this->assertSelectorExists('th:contains("Nom d\'utilisateur")');
    }

    public function testCreateUserPage(): void
    {
        // We create a new user
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'test',
            'user[email]' => 'test@test.com',
            'user[password][first]' => '123Azerty',
            'user[password][second]' => '123Azerty',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/login');

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('.alert.alert-success', 'L\'utilisateur a bien été ajouté.');
    }

    public function testEditUserPage(): void
    {
        // We connect as a user
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'test',
            '_password' => '123Azerty',
        ]);

        $client->submit($form);
        $client->followRedirect();

        // We edit the user
        $userId = static::getContainer()->get(UserRepository::class)->findOneByEmail('test@test.com')->getId();
        $crawler = $client->request('GET', '/users/' . $userId . '/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'test1',
            'user[email]' => 'test1@example.com',
            'user[password][first]' => '124Azerty',
            'user[password][second]' => '124Azerty'
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/login');

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('.alert.alert-success', 'L\'utilisateur a bien été modifié.');
    }

    public function testNotAuthorizedEditUserPage(): void
    {
        // We connect as a user
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'test1',
            '_password' => '124Azerty',
        ]);

        $client->submit($form);
        $client->followRedirect();

        // We try to edit another user
        $userId = static::getContainer()->get(UserRepository::class)->findOneByEmail('user0@example.com')->getId();
        $crawler = $client->request('GET', '/users/' . $userId . '/edit');

        $this->assertResponseRedirects('/');

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous n\'avez pas les droits pour accéder à cette page.');
    }

    public function testNotConnectedEditUserPage(): void
    {
        // We try to edit a user
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/2/edit');

        $this->assertResponseRedirects('/');

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous devez être connecté pour accéder à cette page.');
    }

    public function testNotAuthorizedDeleteUserPage(): void
    {
        // We connect as a user
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'test1',
            '_password' => '124Azerty',
        ]);

        $client->submit($form);
        $client->followRedirect();

        // We try to delete another user
        $userId = static::getContainer()->get(UserRepository::class)->findOneByEmail('user0@example.com')->getId();

        $crawler = $client->request('GET', '/users/' . $userId . '/delete');

        $this->assertResponseRedirects('/');

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous n\'avez pas les droits pour supprimer un utilisateur.');
    }

    public function testDeleteUserPage(): void
    {
        // We connect as an admin
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'user0',
            '_password' => 'password',
        ]);
        $client->submit($form);

        $client->followRedirect();

        $userId = static::getContainer()->get(UserRepository::class)->findOneByEmail('test1@example.com')->getId();

        // We delete the user
        $crawler = $client->request('GET', '/users/' . $userId . '/delete');


        $this->assertResponseRedirects('/admin');

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('.alert.alert-success', 'L\'utilisateur a bien été supprimé.');
    }
}
