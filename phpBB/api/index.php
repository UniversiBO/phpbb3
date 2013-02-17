<?php

ini_set('display_errors', 0);

require_once __DIR__.'/../silex/vendor/autoload.php';

$app = require __DIR__.'/../silex/src/app.php';
require __DIR__.'/../silex/config/prod.php';
require __DIR__.'/../silex/src/services.php';
require __DIR__.'/../silex/src/controllers.php';
$app->run();
