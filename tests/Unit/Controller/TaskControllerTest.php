<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;

use App\Controller\TaskController;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskControllerTest extends KernelTestCase
{
    private $entityManager;
    private $taskController;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get(EntityManagerInterface::class);
        $this->taskController = new TaskController();
    }

    public function testListAction(): void
    {
        $response = $this->taskController->listAction($this->entityManager);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        // Add more assertions as needed
    }

    public function testCreateAction(): void
    {
        $request = new Request();
        $response = $this->taskController->createAction($this->entityManager, $request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        // Add more assertions as needed
    }

    public function testEditTaskAction(): void
    {
        $task = new Task();
        $request = new Request();
        $response = $this->taskController->editTaskAction($task, $this->entityManager, $request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        // Add more assertions as needed
    }

    public function testToggleTaskAction(): void
    {
        $task = new Task();
        $response = $this->taskController->toggleTaskAction($task, $this->entityManager);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        // Add more assertions as needed
    }

    public function testDeleteTaskAction(): void
    {
        $task = new Task();
        $response = $this->taskController->deleteTaskAction($task, $this->entityManager);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        // Add more assertions as needed
    }

    public function testDoneTasksAction(): void
    {
        $response = $this->taskController->doneTasksAction($this->entityManager);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        // Add more assertions as needed
    }

    public function testUserTasksAction(): void
    {
        $user = new User();
        $response = $this->taskController->userTasksAction($user, $this->entityManager);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        // Add more assertions as needed
    }
}
