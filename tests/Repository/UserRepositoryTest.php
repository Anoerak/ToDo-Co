<?php

namespace App\Repository;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserRepositoryTest extends KernelTestCase
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
		$user = new User();
		$user->setUsername('test')
			->setEmail('test@email.com')
			->setPassword('123Azerty')
			->setRoles(['ROLE_USER']);

		$this->entityManager->getRepository(User::class)->save($user, true);

		$this->assertNotNull($user->getId());
	}

	public function testInstanceOfUser(): void
	{
		$user = new User();

		$this->expectException(UnsupportedUserException::class);
		$this->expectExceptionMessage(sprintf('Instances of "%s" are not supported.', \get_class($user)));

		$userRepository = $this->entityManager->getRepository(User::class);
		$userRepository->unsupportedUserCheck($user);
	}

	public function testUpgradePasswordFunction(): void
	{
		$user = $this->entityManager->getRepository(User::class)->findOneByEmail('test@email.com');

		// $oldPassword = $user->getPassword();
		$oldPassword = '123Azerty';

		$newPassword = '124Password';

		$this->entityManager->getRepository(User::class)->upgradePassword($user, $newPassword);

		$this->assertTrue($oldPassword !== $user->getPassword());
	}

	public function testRemoveFunction(): void
	{
		$user = $this->entityManager->getRepository(User::class)->findOneByEmail('test@email.com');

		$this->entityManager->getRepository(User::class)->remove($user, true);

		$this->assertNull($this->entityManager->getRepository(User::class)->findOneByEmail('test@email.com'));
	}


	public function tearDown(): void
	{
		parent::tearDown();

		// doing this is recommended to avoid memory leaks
		$this->entityManager->close();
		$this->entityManager = null;
	}
}
