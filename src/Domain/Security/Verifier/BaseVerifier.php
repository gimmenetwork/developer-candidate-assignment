<?php

declare(strict_types=1);

namespace GimmeBook\Domain\Security\Verifier;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

abstract class BaseVerifier implements VerifierInterface
{
    protected const WILDCARD = '*';

    protected array $controllersToCheck;
    protected array $selectedMethods;
    protected string $currentMethod;

    public function verify(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $this->doActionsForAny($request);

        if (!$this->shouldCheckController($request->attributes->get('_controller'))) {
            return;
        }

        $this->doVerify($request);
    }

    /**
     * Do some actions for any controller
     */
    protected function doActionsForAny(Request $request): void
    {
        // implement in child
    }

    private function shouldCheckController(string $controllerAndMethod): bool
    {
        [$controllerClass, $this->currentMethod] = explode('::', $controllerAndMethod);

        $this->selectedMethods = $this->controllersToCheck[$controllerClass] ?? [];

        // no such controller
        if (!$this->selectedMethods) {
            return false;
        }

        return $this->doCheckMethod();
    }

    /**
     * Is current method should be verified
     */
    abstract protected function doCheckMethod(): bool;

    abstract protected function doVerify(Request $request): void;
}
