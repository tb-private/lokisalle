<?php

define('SITEURL', 'http://lokisalle.thomasbethmont.fr');
define('CONTROLLER_POS', 0);

require_once __DIR__.'/../vendor/Autoload.php';

$app = Lib\App::getInstance();
$app->run(true);
