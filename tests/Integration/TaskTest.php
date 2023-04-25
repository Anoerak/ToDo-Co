<?php

namespace App\Tests\Unit;

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

        $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class, 1);

        $task = $this->getTaskEntity();
        $task->setAuthor($user);

        $taskError = $container->get('validator')->validate($task);

        $this->assertCount(0, $taskError);
    }

    public function testInvalidName(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class, 1);

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

        $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class, 1);

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
