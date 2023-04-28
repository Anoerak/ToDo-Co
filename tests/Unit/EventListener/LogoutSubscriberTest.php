<?php

namespace App\Tests\Unit\EventListener;

use App\EventListener\LogoutSubscriber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LogoutSubscriberTest extends KernelTestCase
{
	public function testGetSubscribedEvents(): void
	{
		$events = LogoutSubscriber::getSubscribedEvents();

		$this->assertIsArray($events);
		$this->assertArrayHasKey('Symfony\Component\Security\Http\Event\LogoutEvent', $events);
		$this->assertEquals('onLogout', $events['Symfony\Component\Security\Http\Event\LogoutEvent']);
	}
}
