<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    public function getTaskEntity(): Task
    {
        return (new Task())
            ->setTitle('Test')
            ->setContent('Content for a unit test');
    }

    public function testTaskEntityIsValid(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class, 2);

        $task = $this->getTaskEntity();
        $task->setAuthor($user);

        $taskError = $container->get('validator')->validate($task);

        $this->assertCount(0, $taskError);
    }

    public function testGetTask(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $task = $this->getTaskEntity();
        $task->setAuthor(static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class, 2))
            ->setIsDone(false)
            ->setCreatedAt(new \DateTimeImmutable('2023-01-01 00:00:00'));

        // We get the task Id
        $taskId = $task->getId();

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals(null, $taskId);
        $this->assertEquals('Test', $task->getTitle());
        $this->assertEquals('Content for a unit test', $task->getContent());
        $this->assertEquals('user1', $task->getAuthor()->getUsername());
        $this->assertEquals(false, $task->isIsDone());
        $this->assertEquals(new \DateTimeImmutable('2023-01-01 00:00:00'), $task->getCreatedAt());

        $taskError = $container->get('validator')->validate($task);

        $this->assertCount(0, $taskError);
    }

    public function testInvalidName(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class, 2);

        $task = $this->getTaskEntity();
        $task->setTitle('')
            ->setAuthor($user);

        $taskError = $container->get('validator')->validate($task);

        $this->assertCount(2, $taskError);
    }

    public function testInvalidContent(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class, 2);

        $task = $this->getTaskEntity();
        $task->setContent('')
            ->setAuthor($user);

        $taskError = $container->get('validator')->validate($task);

        $this->assertCount(1, $taskError);
    }

    public function testInvalidAuthor(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class, 11);

        $task = $this->getTaskEntity();
        $task->setAuthor($user);

        $taskError = $container->get('validator')->validate($task);

        $this->assertCount(0, $taskError);
    }
}
