<?php
$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('Monolog\\Handler\\', __DIR__);
date_default_timezone_set('UTC');
