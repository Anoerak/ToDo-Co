<?php

namespace App\Tests\Unit\Fixtures;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use App\Entity\Task;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FixtureTest extends KernelTestCase
{
    protected $databaseTool;
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        // We grab the ToDoAndCo_test database
        $this->databaseTool = $kernel->getContainer()->get('doctrine')->getConnection();

        // We grab the entity manager
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        // We delete all the data in the database
        $this->databaseTool->executeQuery('SET FOREIGN_KEY_CHECKS = 0');
        $this->databaseTool->executeQuery('TRUNCATE TABLE task');
        $this->databaseTool->executeQuery('TRUNCATE TABLE user');
        $this->databaseTool->executeQuery('SET FOREIGN_KEY_CHECKS = 1');

        // We close the connection to avoid having a connection error during the tests
        $this->databaseTool->close();
    }

    public function testLoadFixtures(): void
    {
        // We load the fixtures
        $fixtures = new AppFixtures($this->createMock(UserPasswordHasherInterface::class));
        $fixtures->load($this->entityManager);


        $users = $this->entityManager->getRepository(User::class)->findAll();
        $this->assertCount(10, $users);

        $tasks = $this->entityManager->getRepository(Task::class)->findAll();
        $this->assertCount(10, $tasks);
    }
}
