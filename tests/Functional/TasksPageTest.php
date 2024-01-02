<?php

namespace App\Tests\Functional;

use App\Entity\Task;
use App\Entity\User;
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

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous devez être connecté pour créer une tâche.');
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

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous devez être connecté pour créer une tâche.');
    }

    public function testAttributeAllTasksWithoutAuthorToAnonymousUser(): void
    {
        $client = $this->connectAsUser('user0@example.com');

        $crawler = $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();

        // We select the button with the id "attribute-task-without-user"
        $link = $crawler->filter('#attribute-task-without-user')->link();
        // We click on the button
        $crawler = $client->click($link);

        // We navigate to the admin page and the tasks list again
        $crawler = $client->request('GET', '/admin');
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

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous devez être connecté pour modifier une tâche.');
    }

    public function testUnauthorizedEditTaskPage(): void
    {
        $client = $this->connectAsUser('user3@example.com');

        $taskId = static::getContainer()->get(TaskRepository::class)->findOneByTitle('Test')->getId();

        $client->request('GET', '/tasks/' . $taskId . '/edit');

        $this->assertResponseRedirects('/tasks');

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous ne pouvez pas modifier une tâche qui ne vous appartient pas.');
    }

    public function testEditTaskPage(): void
    {
        $client = $this->connectAsUser('user0@example.com');

        $taskId = static::getContainer()->get(TaskRepository::class)->findOneByTitle('Test')->getId();

        $crawler = $client->request('GET', '/tasks/' . $taskId . '/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Modified Test',
            'task[content]' => 'Modified Test',
            'task[author]' => '2'
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

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous devez être connecté pour clôturer une tâche.');
    }

    public function testUnauthorizedToggleTaskPage(): void
    {
        $client = $this->connectAsUser('user1@example.com');

        $taskId = static::getContainer()->get(TaskRepository::class)->findOneByTitle('Task 7')->getId();

        $client->request('GET', '/tasks/' . $taskId . '/toggle');

        $this->assertResponseRedirects('/tasks');

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous ne pouvez pas clôturer une tâche qui ne vous appartient pas.');
    }

    public function testToggleTaskPage(): void
    {
        // We log in as user1
        $client = $this->connectAsUser('user1@example.com');

        $taskId = static::getContainer()->get(TaskRepository::class)->findOneByTitle('Modified Test')->getId();
        $taskName = static::getContainer()->get(TaskRepository::class)->findOneByTitle('Modified Test')->getTitle();

        $client->request('GET', '/tasks/' . $taskId . '/toggle');

        $this->assertResponseRedirects('/tasks');

        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-success');
        $this->assertSelectorTextContains('.alert.alert-success', 'La tâche ' . $taskName . ' a bien été marquée comme terminée.');
    }

    public function testUnauthenticatedDeleteTaskPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/1/delete');

        $this->assertResponseRedirects('/login');

        $client->followRedirect();

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

        $client->followRedirect();

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

        $client->followRedirect();

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
        $this->assertSelectorTextContains('h2', 'Tâches de ' . $username);
    }

    public function testAnonymousTasksAction(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        // Connect as Admin with user0
        $adminUser = $entityManager->getRepository(User::class)->findOneByEmail('user0@example.com');

        // Create an anonymous task
        $task = new Task();
        $task->setTitle('Anonymous Task');
        $task->setContent('This is an anonymous task');
        $entityManager->persist($task);
        $entityManager->flush();

        // Simulate an authenticated user with ROLE_ADMIN
        $client->loginUser($adminUser);

        // Send a GET request to the anonymousTasksAction
        $client->request('GET', '/tasks/anonymous');

        // Assert that the response is a redirect to the admin page
        $this->assertResponseRedirects('/admin');

        // Follow the redirect
        $client->followRedirect();

        // Assert that the task has been assigned to the anonymous user
        $updatedTask = $entityManager->getRepository(Task::class)->find($task->getId());
        $this->assertEquals('anonymous', $updatedTask->getAuthor()->getUsername());
    }

    public function testAnonymousTasksActionWithAnonymousUser(): void
    {
        $client = static::createClient();

        // Send a GET request to the anonymousTasksAction
        $client->request('GET', '/tasks/anonymous');

        // Assert that the response is a redirect to the login page
        $this->assertResponseRedirects('/login');

        // Follow the redirect
        $client->followRedirect();

        // Assert that the task has not been assigned to the anonymous user
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $task = $entityManager->getRepository(Task::class)->findOneByTitle('Anonymous Task');

        // Assert that the user has been notified
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous devez être connecté pour effectuer cette opération.');
    }

    public function testAnonymousTasksActionWithLowPrivileges(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        // Connect as User with user1
        $user = $entityManager->getRepository(User::class)->findOneByEmail('user1@example.com');

        // Create an anonymous task
        $task = new Task();
        $task->setTitle('Anonymous Task');
        $task->setContent('This is an anonymous task');
        $entityManager->persist($task);
        $entityManager->flush();

        // Simulate an authenticated user with ROLE_USER
        $client->loginUser($user);

        // Send a GET request to the anonymousTasksAction
        $client->request('GET', '/tasks/anonymous');

        // Assert that the response is a redirect to the tasks page
        $this->assertResponseRedirects('/tasks');

        // Follow the redirect
        $client->followRedirect();

        // Assert that the task has not been assigned to the anonymous user
        $updatedTask = $entityManager->getRepository(Task::class)->find($task->getId());
        // We assert that the task has not been assigned to the anonymous user
        $this->assertNull($updatedTask->getAuthor());

        // Assert that the user has been notified
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Vous ne possédez pas les droits nécessaires pour cette action.');
    }
}
