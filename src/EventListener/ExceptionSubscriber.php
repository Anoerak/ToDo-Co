<?php

namespace App\EventListener;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionSubscriber extends AbstractController
{
	static public function getSubscribedEvents()
	{
		return [
			'kernel.exception' => 'onKernelException',
		];
	}

	public function onKernelException(ExceptionEvent $event)
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
