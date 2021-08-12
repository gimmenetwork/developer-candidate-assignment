<?php

declare(strict_types=1);

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader as ContainerYamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;

function getConfigPath(string $filename = null): array
{
    return [dirname(__DIR__) . '/config/' . $filename];
}

global $containerBuilder, $routes;

$fileLocator = new FileLocator(getConfigPath());
$containerBuilder = new ContainerBuilder();

$containerYamlLoader = new ContainerYamlFileLoader($containerBuilder, $fileLocator);
$containerYamlLoader->load('services.yml');

$configYamlLoader = new YamlFileLoader($fileLocator);
$routes = $configYamlLoader->load('routes.yml');
$containerBuilder->register(UrlGeneratorInterface::class, UrlGenerator::class)
    ->addArgument($routes)
    ->addArgument(new RequestContext());

$containerBuilder->addCompilerPass(new RegisterListenersPass());
