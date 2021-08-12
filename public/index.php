<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

require_once "../vendor/autoload.php";
require_once "../config/bootstrap.php";

global $fileLocator, $containerBuilder, $routes;

$request = Request::createFromGlobals();

$matcher = new UrlMatcher($routes, new RequestContext());

$containerBuilder->compile(true);

$controllerResolver = new ContainerControllerResolver($containerBuilder);
$argumentResolver = new ArgumentResolver();

$dispatcher = $containerBuilder->get('event_dispatcher');
$dispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));

$kernel = new HttpKernel($dispatcher, $controllerResolver, new RequestStack(), $argumentResolver);

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
