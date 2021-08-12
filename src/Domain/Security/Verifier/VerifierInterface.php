<?php

declare(strict_types=1);

namespace GimmeBook\Domain\Security\Verifier;

use Symfony\Component\HttpKernel\Event\RequestEvent;

interface VerifierInterface
{
    public function verify(RequestEvent $event): void;
}
