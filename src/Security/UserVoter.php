<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
	// We create a simple voter to check the user right
	protected function supports(string $attribute, $subject): bool
	{
		return in_array($attribute, ['USER_EDIT', 'USER_VIEW'])
			&& $subject instanceof User;
	}

	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		// We get the user from the token
		$user = $token->getUser();

		// If the user is anonymous, do not grant access
		if (!$user instanceof User) {
			return false;
		}

		// If the user is admin, he can do everything
		if ($user->getRoles()[0] === 'ROLE_ADMIN') {
			return true;
		}

		// If the user is not admin, he can only edit or view his own profile
		switch ($attribute) {
			case 'USER_EDIT':
				return $subject->getId() === $user->getId();
				break;
			case 'USER_VIEW':
				return $subject->getId() === $user->getId();
				break;
		}

		return false;
	}
}
