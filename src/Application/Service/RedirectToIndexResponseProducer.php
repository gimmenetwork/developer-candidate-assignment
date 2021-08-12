<?php

declare(strict_types=1);

namespace GimmeBook\Application\Service;

use GimmeBook\Infrastructure\Core\Notification\NotificationHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RedirectToIndexResponseProducer
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function produce(string $message, bool $isSuccess): RedirectResponse
    {
        $response = new RedirectResponse($this->urlGenerator->generate('index', ['page' => 1]));
        NotificationHelper::send($response, $message, !$isSuccess);
        return $response;
    }
}
