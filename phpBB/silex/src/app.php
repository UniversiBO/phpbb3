<?php

use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;

$app = new Application();
$app->register(new ServiceControllerServiceProvider());

return $app;
