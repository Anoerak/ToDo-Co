<?php

namespace App\Tests\Unit;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    public function getUserEntity(): User
    {
        return (new User())
            ->setUsername('Test')
            ->setPassword('123Azerty')
            ->setEmail('test@test.com')
            ->setRoles(['ROLE_USER'])
            ->addTask(static::getContainer()->get('doctrine.orm.entity_manager')->find(Task::class, 1));
    }

    public function testUserEntityIsValid(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $user = $this->getUserEntity();

        $userError = $container->get('validator')->validate($user);

        $this->assertCount(0, $userError);
    }

    public function testGetUser(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $user = $this->getUserEntity();

        // We get the user Id
        $userId = $user->getId();

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(null, $userId);
        $this->assertEquals('Test', $user->getUsername());
        $this->assertEquals('123Azerty', $user->getPassword());
        $this->assertEquals('test@test.com', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertEquals('Task 0', $user->getTasks()[0]->getTitle());
        $this->assertEquals('test@test.com', $user->getUserIdentifier());

        $userError = $container->get('validator')->validate($user);

        $this->assertCount(0, $userError);
    }

    public function testAddTask(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $user = $this->getUserEntity();
        $task = static::getContainer()->get('doctrine.orm.entity_manager')->find(Task::class, 1);

        $user->addTask($task);

        $userError = $container->get('validator')->validate($user);

        $this->assertCount(0, $userError);
    }

    public function testRemoveTask(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $user = $this->getUserEntity();
        $task = static::getContainer()->get('doctrine.orm.entity_manager')->find(Task::class, 1);

        $user->addTask($task);
        $user->removeTask($task);

        $userError = $container->get('validator')->validate($user);

        $this->assertCount(0, $userError);
    }

    public function testInvalidName(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $user = $this->getUserEntity();
        $user->setUsername('');

        $userError = $container->get('validator')->validate($user);

        $this->assertCount(2, $userError);
    }

    public function testInvalidPassword(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $user = $this->getUserEntity();
        $user->setPassword('');

        $userError = $container->get('validator')->validate($user);

        $this->assertCount(1, $userError);
    }

    public function testUsernameAlreadyExist(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $user = $this->getUserEntity();
        $user->setUsername('user0');

        $userError = $container->get('validator')->validate($user);

        $this->assertCount(1, $userError);
    }

    public function testEmailAlreadyExist(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $user = $this->getUserEntity();
        $user->setEmail('user0@example.com');

        $userError = $container->get('validator')->validate($user);

        $this->assertCount(1, $userError);
    }

    public function testInvalidEmail(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $user = $this->getUserEntity();
        $user->setEmail('test');

        $userError = $container->get('validator')->validate($user);

        $this->assertCount(1, $userError);
    }
}
