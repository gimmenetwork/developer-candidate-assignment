<?php

declare(strict_types=1);

namespace GimmeBook\Application\EventSubscriber\Security;

use GimmeBook\Domain\Security\Verifier\VerifierInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class VerificationEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var VerifierInterface[]
     */
    private array $verifiers = [];

    public function addVerifier(VerifierInterface $verifier): self
    {
        $this->verifiers[] = $verifier;
        return $this;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        foreach ($this->verifiers as $verifier) {
            $verifier->verify($event);
        }
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }
}
