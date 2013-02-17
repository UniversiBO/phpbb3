<?php
use Silex\Provider\MonologServiceProvider;

define('IN_PHPBB', true);
$phpEx = 'php';
$phpbb_root_path = dirname(dirname(__DIR__)) .'/';

require_once $phpbb_root_path . 'common.'.$phpEx;
require_once $phpbb_root_path . 'adm/includes/functions.'.$phpEx;

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

// enable the debug mode
$app['debug'] = true;

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../logs/api.log',
));