<?php

namespace App\Tests\Functional;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TasksPageTest extends WebTestCase
{

    public function connectAsUser($email)
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $user = static::getContainer()->get(UserRepository::class)->findOneByEmail($email);

        $client->loginUser($user);

        return $client;
    }

    public function testUnauthenticatedTaskPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tasks');

        $this->assertResponseRedirects('/login');
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous devez être connecté pour accéder à cette page.');
    }

    public function testTaskPage(): void
    {
        $this->connectAsUser('user1@example.com')->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();
    }

    public function testUnauthenticatedCreateTaskPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/create');

        $this->assertResponseRedirects('/login');
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous devez être connecté pour créer une tâche.');
    }

    public function testCreateTaskPage(): void
    {
        $client = $this->connectAsUser('user1@example.com');

        $crawler = $client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Test',
            'task[content]' => 'Test',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/tasks');

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('.alert.alert-success', 'La tâche a bien été ajoutée.');
    }

    public function testUnauthenticatedEditTaskPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/1/edit');

        $this->assertResponseRedirects('/login');
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous devez être connecté pour modifier une tâche.');
    }

    public function testUnauthorizedEditTaskPage(): void
    {
        $client = $this->connectAsUser('user3@example.com');

        $taskId = static::getContainer()->get(TaskRepository::class)->findOneByTitle('Test')->getId();

        $client->request('GET', '/tasks/' . $taskId . '/edit');

        $this->assertResponseRedirects('/tasks');
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous ne pouvez pas modifier une tâche qui ne vous appartient pas.');
    }

    public function testEditTaskPage(): void
    {
        $client = $this->connectAsUser('user1@example.com');

        $taskId = static::getContainer()->get(TaskRepository::class)->findOneByTitle('Test')->getId();

        $crawler = $client->request('GET', '/tasks/' . $taskId . '/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Modified Test',
            'task[content]' => 'Modified Test',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/tasks');

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('.alert.alert-success', 'La tâche a bien été modifiée.');
    }

    public function testUnauthenticatedToggleTaskPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/1/toggle');

        $this->assertResponseRedirects('/login');
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous devez être connecté pour clôturer une tâche.');
    }

    public function testToggleTaskPage(): void
    {
        $client = $this->connectAsUser('user1@example.com');

        $taskId = static::getContainer()->get(TaskRepository::class)->findOneByTitle('Modified Test')->getId();

        $client->request('GET', '/tasks/' . $taskId . '/toggle');

        $this->assertResponseRedirects('/tasks');

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('.alert.alert-success', 'La tâche ' . $taskId . ' a bien été marquée comme terminée.');
    }

    public function testUnauthenticatedDeleteTaskPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/1/delete');

        $this->assertResponseRedirects('/login');
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous devez être connecté pour supprimer une tâche.');
    }

    public function testUnauthorizedDeleteTaskPage(): void
    {
        $client = $this->connectAsUser('user3@example.com');

        $taskId = static::getContainer()->get(TaskRepository::class)->findOneByTitle('Modified Test')->getId();

        $client->request('GET', '/tasks/' . $taskId . '/delete');

        $this->assertResponseRedirects('/tasks');

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous ne pouvez pas supprimer une tâche qui ne vous appartient pas.');
    }

    public function testDeleteTaskPage(): void
    {
        $client = $this->connectAsUser('user1@example.com');

        $taskId = static::getContainer()->get(TaskRepository::class)->findOneByTitle('Modified Test')->getId();

        $client->request('GET', '/tasks/' . $taskId . '/delete');

        $this->assertResponseRedirects('/tasks');

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('.alert.alert-success', 'La tâche a bien été supprimée.');
    }

    public function testUnauthenticatedAccessDoneTasksList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/done');

        $this->assertResponseRedirects('/login');
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous devez être connecté accéder aux tâches clôturées.');
    }

    public function testAccessDoneTasksList(): void
    {
        $client = $this->connectAsUser('user1@example.com');

        $client->request('GET', '/tasks/done');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Tâches clôturées');
    }

    public function testUnauthenticatedUserTasksList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/user/1');

        $this->assertResponseRedirects('/login');
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous devez être connecté pour accéder votre liste des tâches.');
    }

    public function testUnauthorizedAccessToUserTasksList(): void
    {
        $client = $this->connectAsUser('user3@example.com');

        $otherUserId = static::getContainer()->get(UserRepository::class)->findOneByEmail('user1@example.com')->getId();

        $client->request('GET', '/tasks/user/' . $otherUserId);

        $this->assertResponseRedirects('/tasks');

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous ne pouvez pas accéder à la liste des tâches d\'un autre utilisateur.');
    }

    public function testUserTasksList(): void
    {
        $client = $this->connectAsUser('user1@example.com');

        $userId = static::getContainer()->get(UserRepository::class)->findOneByEmail('user1@example.com')->getId();
        $username = static::getContainer()->get(UserRepository::class)->findOneByEmail('user1@example.com')->getUsername();

        $crawler = $client->request('GET', '/tasks/user/' . $userId);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Liste des tâches de ' . $username);
    }
}
