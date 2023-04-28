<?php

namespace App\Tests\Unit\EventListener;

use App\Entity\User;
use App\EventListener\LoginSubscriber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LoginSubscriberTest extends KernelTestCase
{
	public function testGetSubscribedEvents(): void
	{
		$events = LoginSubscriber::getSubscribedEvents();

		$this->assertIsArray($events);
		$this->assertArrayHasKey('Symfony\Component\Security\Http\Event\InteractiveLoginEvent', $events);
		$this->assertEquals('onLogin', $events['Symfony\Component\Security\Http\Event\InteractiveLoginEvent']);
	}
}
