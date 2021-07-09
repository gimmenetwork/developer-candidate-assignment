<?php

namespace App\EventSubscriber;

use App\Contracts\AuthenticationInterface;
use App\Controller\HomeController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AuthSubscriber implements EventSubscriberInterface
{
    private bool $isAuthenticated = true;

    public function __construct(
        private RequestStack $requestStack
    )
    {
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof AuthenticationInterface) {
            $session = $this->requestStack->getSession();
            if (!$session->has('isLogin') || $session->get('isLogin') != 1) {
                $event->setController(function() {
                    return new RedirectResponse('/');
                });
            }
        }
    }



    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController']
        ];
    }
}
