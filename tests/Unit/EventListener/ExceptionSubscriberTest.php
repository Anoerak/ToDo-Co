<?php

namespace App\Tests\Unit\EventListener;

use App\EventListener\ExceptionSubscriber;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ExceptionSubscriberTest extends WebTestCase
{
	public function testGetSubscribedEvents(): void
	{
		$subscriber = new ExceptionSubscriber(
			static::createMock('Symfony\Component\DependencyInjection\ContainerInterface')
		);
		$subscribedEvents = $subscriber::getSubscribedEvents();

		$this->assertIsArray($subscribedEvents);
		$this->assertArrayHasKey('kernel.exception', $subscribedEvents);
		$this->assertEquals('onKernelException', $subscribedEvents['kernel.exception']);
	}
}
