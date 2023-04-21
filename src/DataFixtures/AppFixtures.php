<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        // we create 5 users
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $password = $this->encoder->hashPassword($user, 'password');
            $user->setEmail('user' . $i . '@example.com')
                ->setUsername('user' . $i)
                ->setRoles($i % 2 ? ['ROLE_USER'] : ['ROLE_ADMIN'])
                ->setPassword($password);
            $manager->persist($user);
        }

        // We create 10 task alternatively Done and Not Done
        for ($i = 0; $i < 10; $i++) {
            $task = new Task();
            $task->setTitle('Task ' . $i)
                ->setContent('Content of task ' . $i)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setIsDone($i % 2);
            $manager->persist($task);
        }

        $manager->flush();
    }
}
