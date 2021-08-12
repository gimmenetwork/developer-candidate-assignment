<?php

declare(strict_types=1);

namespace GimmeBook\Domain\TwigDataPopulator;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

interface TwigDataPopulatorInterface
{
    public function populate(ControllerEvent $event, Environment $twig): void;
}
