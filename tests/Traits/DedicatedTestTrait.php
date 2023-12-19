<?php

namespace App\Tests\Traits;

use App\Repository\UserRepository;

trait DedicatedTestTrait
{
	public function connectAs(string $email): void
	{
		$client = static::createClient();
		$client->request('GET', '/login');

		$user = static::getContainer()->get(UserRepository::class)->findOneByEmail($email);

		$client->loginUser($user);

		return $client;
	}

	public function connectAsAdmin(): void
	{
		$client = static::createClient();
		$client->request('GET', '/login');

		$user = static::getContainer()->get(UserRepository::class)->findOneByEmail('user0@example.com');

		$client->loginUser($user);

		return $client;
	}

	public function connectAsUser(): void
	{
		$client = static::createClient();
		$client->request('GET', '/login');

		$user = static::getContainer()->get(UserRepository::class)->findOneByEmail('user1@example.com');

		$client->loginUser($user);

		return $client;
	}
}
