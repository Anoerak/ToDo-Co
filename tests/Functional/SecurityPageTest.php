<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityPageTest extends WebTestCase
{
    public function testSecurityPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('button');
    }

    public function testLoginUser(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'user1',
            '_password' => 'password',
        ]);
        $client->submit($form);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testLoginUserError(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'user1',
            '_password' => 'password2',
        ]);
        $client->submit($form);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testLoginCheck(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login_check');

        $this->assertResponseStatusCodeSame(500);
    }

    public function testAdminDashboard(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('user0@example.com');

        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Admin Dashboard');
    }

    public function testLogout(): void
    {
        // We log in
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('user0@example.com');

        $client->loginUser($testUser);

        // We log out
        $crawler = $client->request('GET', '/logout');
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('.alert.alert-success', 'Superbe !');
    }
}
