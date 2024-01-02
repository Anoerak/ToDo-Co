<?php

namespace App\Tests\Security;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use App\Security\UserAccessVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserAccessVoterTest extends TestCase
{
	public function testSupports(): void
	{
		$voter = new UserAccessVoter();
		$user = new User('user1', 'password');

		$this->assertTrue($voter->supports(UserAccessVoter::CONNECTED, $user));
		$this->assertTrue($voter->supports(UserAccessVoter::EDIT, $user));
		$this->assertTrue($voter->supports(UserAccessVoter::DELETE, $user));
		$this->assertFalse($voter->supports('invalid_attribute', $user));
		$this->assertFalse($voter->supports(UserAccessVoter::CONNECTED, 'invalid_subject'));
	}

	public function testVoteOnAttribute(): void
	{
		$voter = new UserAccessVoter();
		$user = new User('user', 'password');
		$token = $this->createMock(TokenInterface::class);
		$token->method('getUser')->willReturn($user);

		$this->assertTrue($voter->voteOnAttribute(UserAccessVoter::CONNECTED, $user, $token));
		$this->assertTrue($voter->voteOnAttribute(UserAccessVoter::EDIT, $user, $token));
		$this->assertTrue($voter->voteOnAttribute(UserAccessVoter::DELETE, $user, $token));


		$adminUser = new User('user0', 'password', ['ROLE_ADMIN']);
		$this->assertFalse($voter->voteOnAttribute(UserAccessVoter::CONNECTED, $adminUser, $token));
		$this->assertFalse($voter->voteOnAttribute(UserAccessVoter::EDIT, $adminUser, $token));
		$this->assertFalse($voter->voteOnAttribute(UserAccessVoter::DELETE, $adminUser, $token));
	}

	public function testVoteOnAttributeWithInvalidUser(): void
	{
		$voter = new UserAccessVoter();
		$user = null;
		$token = $this->createMock(TokenInterface::class);
		$token->method('getUser')->willReturn($user);

		$this->assertFalse($voter->voteOnAttribute(UserAccessVoter::CONNECTED, $user, $token));
		$this->assertFalse($voter->voteOnAttribute(UserAccessVoter::EDIT, $user, $token));
		$this->assertFalse($voter->voteOnAttribute(UserAccessVoter::DELETE, $user, $token));
	}

	public function testVoteOnAttributeWithAdminUser(): void
	{
		$voter = new UserAccessVoter();
		$user2 = new User('user0', 'password');
		$user2->setRoles(['ROLE_ADMIN']);
		$token = $this->createMock(TokenInterface::class);
		$token->method('getUser')->willReturn($user2);

		// We connect as a regular user so we can check if the admin user has access
		$user = new User('user', 'password');
		$token->method('getUser')->willReturn($user);

		$this->assertTrue(in_array('ROLE_ADMIN', $user2->getRoles()));

		$this->assertTrue($voter->voteOnAttribute(UserAccessVoter::CONNECTED, $user2, $token));
		$this->assertTrue($voter->voteOnAttribute(UserAccessVoter::EDIT, $user2, $token));
		$this->assertTrue($voter->voteOnAttribute(UserAccessVoter::DELETE, $user2, $token));
	}
}
