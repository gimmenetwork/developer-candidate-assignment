<?php

declare(strict_types=1);

namespace GimmeBook\Application\EventSubscriber;

use GimmeBook\Domain\TwigDataPopulator\TwigDataPopulatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

/**
 * Populates Twig with data
 */
class TwigDataPopulatorEventSubscriber implements EventSubscriberInterface
{
    /**
     * @param TwigDataPopulatorInterface[] $populators
     */
    public function __construct(private Environment $twig, private array $populators)
    {
    }

    public function onControllerEvent(ControllerEvent $event): void
    {
        foreach ($this->populators as $populator) {
            $populator->populate($event, $this->twig);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER => 'onControllerEvent'];
    }
}
