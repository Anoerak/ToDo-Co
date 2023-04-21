<?php

namespace App\EventListener;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginSubscriber extends AbstractController implements EventSubscriberInterface
{
	public function __construct(
		private UrlGeneratorInterface $urlGenerator
	) {
	}

	public static function getSubscribedEvents(): array
	{
		return [InteractiveLoginEvent::class => 'onLogin'];
	}

	public function onLogin(InteractiveLoginEvent $event): void
	{
		$this->addFlash('success', 'Vous êtes connecté !!');
	}
}
