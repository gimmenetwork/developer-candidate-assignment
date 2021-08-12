<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once './vendor/autoload.php';
require_once './config/bootstrap.php';

global $containerBuilder;

$containerBuilder->compile(true);

return ConsoleRunner::createHelperSet($containerBuilder->get('em'));
