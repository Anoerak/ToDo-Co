<?php

namespace App\EventListener;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginSubscriber extends AbstractController implements EventSubscriberInterface
{
    /**
     * Class LoginSubscriber
     *
     * This class is responsible for handling login events.
     * It is used to display a flash message when a user logs in.
     * It is also used to redirect the user to the homepage after login.
     * We don't want phpstan to check the constructor of this class so we ignore it.
     *
     * @phpstan-ignore-next-line
     * @param UrlGeneratorInterface $urlGenerator
     * @return void
     */
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * Returns the subscribed events for the LoginSubscriber class.
     *
     * @return array<mixed> The subscribed events.
     */
    public static function getSubscribedEvents(): array
    {
        return [InteractiveLoginEvent::class => 'onLogin'];
    }

    /**
     * Event listener method called when a user logs in.
     *
     * @param InteractiveLoginEvent $event The event object.
     * @return void
     */
    public function onLogin(InteractiveLoginEvent $event): void
    {
        $this->addFlash('success', 'Vous êtes connecté !!');
    }
}
