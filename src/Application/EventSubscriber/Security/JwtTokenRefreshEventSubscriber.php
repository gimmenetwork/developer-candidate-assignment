<?php

declare(strict_types=1);

namespace GimmeBook\Application\EventSubscriber\Security;

use GimmeBook\Domain\Security\Verifier\JwtVerifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Populate response with refreshed tokens if necessary
 */
class JwtTokenRefreshEventSubscriber implements EventSubscriberInterface
{
    public function onResponseEvent(ResponseEvent $event): void
    {
        if ($tokens = $event->getRequest()->attributes->get(JwtVerifier::SHOULD_UPDATE_TOKENS_ATTR)) {
            $event->getResponse()->headers->add($tokens);
        }
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::RESPONSE => 'onResponseEvent'];
    }
}
