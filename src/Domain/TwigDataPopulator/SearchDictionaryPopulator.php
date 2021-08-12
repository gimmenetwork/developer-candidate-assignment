<?php

declare(strict_types=1);

namespace GimmeBook\Domain\TwigDataPopulator;

use GimmeBook\Domain\Provider\SearchDictionaryDataProvider;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

/**
 * Populates the search dictionary
 */
class SearchDictionaryPopulator implements TwigDataPopulatorInterface
{
    public function __construct(
        private SearchDictionaryDataProvider $searchDictionaryDataProvider
    ) {
    }

    public function populate(ControllerEvent $event, Environment $twig): void
    {
        $twig->addGlobal('searchDictionary', $this->searchDictionaryDataProvider->getData());
    }
}
