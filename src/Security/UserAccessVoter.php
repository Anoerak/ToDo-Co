<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserAccessVoter extends Voter
{
	// We create a simple voter to check the user rights (['ROLE_USER'], ['ROLE_ADMIN'])
	// We will use it in the controller to check if the user is allowed to edit or delete a task

	// We create a constant to use it in the controller
	const CONNECTED = 'connected';
	const EDIT = 'edit';
	const DELETE = 'delete';

	protected function supports(string $attribute, $subject): bool
	{
		// We check if the attribute is supported
		// We check if the subject is an instance of User
		return in_array($attribute, [self::CONNECTED, self::EDIT, self::DELETE])
			&& $subject instanceof User;
	}

	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		// We get the user from the token
		$user = $token->getUser();

		// If the user is anonymous, we deny access
		if (!$user instanceof User) {
			return false;
		}

		// We check if the user is the same as the subject
		// If it is, we allow access
		if ($user === $subject) {
			return true;
		}

		// If the user is an admin, we allow access
		if (in_array('ROLE_ADMIN', $user->getRoles())) {
			return true;
		}

		// If the user is not the same as the subject and is not an admin, we deny access
		return false;
	}
}
