<?php

namespace App\EventListener;

// use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
// use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExceptionSubscriber extends AbstractController
{
	static public function getSubscribedEvents(): array
	{
		return [
			'kernel.exception' => 'onKernelException',
		];
	}
	/**
	 * @codeCoverageIgnore
	 */
	public function onKernelException(ExceptionEvent $event): void
	{
		$exception = $event->getThrowable();

		$response = new Response();
		$response->setContent('Une erreur est survenue : ' . $exception->getMessage());
		$response->setStatusCode($exception->getCode());

		$event->setResponse($response);

		// We display the error message in a banner
		$this->addFlash('danger', 'Une erreur est survenue : ' . $exception->getMessage());
	}
}
