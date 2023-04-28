<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
	private $entityManager;

	protected function setUp(): void
	{
		$kernel = self::bootKernel();

		$this->entityManager = $kernel->getContainer()
			->get('doctrine')
			->getManager();
	}

	public function testSaveFunction(): void
	{
		$task = new Task();
		$task->setTitle('Test task');
		$task->setContent('Test content');
		$task->setCreatedAt(new \DateTimeImmutable());
		$task->setAuthor($this->entityManager->getRepository(User::class)->findOneByEmail('user1@example.com'));

		$this->entityManager->getRepository(Task::class)->save($task, true);

		$this->assertNotNull($task->getId());
	}

	public function testRemoveFunction(): void
	{
		$task = $this->entityManager->getRepository(Task::class)->findOneByTitle('Test task');

		$this->entityManager->getRepository(Task::class)->remove($task, true);

		$this->assertNull($this->entityManager->getRepository(Task::class)->findOneByTitle('Test task'));
	}

	public function tearDown(): void
	{
		parent::tearDown();

		// doing this is recommended to avoid memory leaks
		$this->entityManager->close();
		$this->entityManager = null;
	}
}
