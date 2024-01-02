<?php

namespace App\EventListener;

use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LogoutSubscriber extends AbstractController implements EventSubscriberInterface
{
    /**
     * LogoutSubscriber constructor.
     *
     * @phpstan-ignore-next-line
     * @param UrlGeneratorInterface $urlGenerator
     * @return void
     */
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * Returns the subscribed events for the LogoutSubscriber class.
     *
     * @return array<mixed> The subscribed events.
     */
    public static function getSubscribedEvents(): array
    {
        return [LogoutEvent::class => 'onLogout'];
    }

    /**
     * Handles the logout event.
     *
     * @param LogoutEvent $event The logout event.
     * @return void
     */
    public function onLogout(LogoutEvent $event): void
    {
        $this->addFlash('success', 'Vous êtes maintenant déconnecté');
    }
}
