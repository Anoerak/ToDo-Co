<?php

namespace App\Tests\Unit\EventListener;

use App\EventListener\ExceptionSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionSubscriberTest extends TestCase
{
	public function testGetSubscribedEvents(): void
	{
		$events = ExceptionSubscriber::getSubscribedEvents();

		$this->assertIsArray($events);
		$this->assertArrayHasKey('kernel.exception', $events);
		$this->assertEquals('onKernelException', $events['kernel.exception']);
	}

	public function testOnKernelException(): void
	{
		$exception = new \Exception('Test exception', 500);
	}
}
