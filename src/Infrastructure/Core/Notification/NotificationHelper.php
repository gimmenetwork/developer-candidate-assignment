<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Core\Notification;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sends notifications to the front by cookie
 */
class NotificationHelper
{
    public static function send(Response $response, string $message, bool $isError = true): void
    {
        $response->headers->setCookie(Cookie::create($isError ? 'error' : 'notification', $message, time() + 1000));
    }
}
