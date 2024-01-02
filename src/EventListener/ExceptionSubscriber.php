<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExceptionSubscriber extends AbstractController
{
    /**
     * Returns the subscribed events for the ExceptionSubscriber.
     *
     * @return array<mixed> The subscribed events.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
    /**
     * Event listener method that handles kernel exceptions.
     *
     * @param ExceptionEvent $event The exception event object.
     * @return void
     */
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
