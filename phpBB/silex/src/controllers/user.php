<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$user = $app['controllers_factory'];

$user->get('/{id}', function(Application $app, $id){
    $user_id_ary = array($id);
    $username_ary = array();
    
    $result = user_get_id_name($user_id_ary, $username_ary);
    
    if('NO_USERS' === $result) {
        throw new NotFoundHttpException('User not found');
    }
    
    return $app->json($username_ary[$id]);
});

$user->get('/{username}', function(Application $app, $username){
    $user_id_ary = array();
    $username_ary = array($username);
    
    $result = user_get_id_name($user_id_ary, $username_ary);
    
    if('NO_USERS' === $result) {
        throw new NotFoundHttpException('User not found');
    }
    
    return $app->json((int)$user_id_ary[0]);
});

$user->post('/', function(Application $app, Request $request) {
    $userData = array(
        
    );
    
    user_add($userData);
});

$user->before(function() {
    global $phpbb_root_path, $phpEx;
    require_once $phpbb_root_path . 'includes/functions_user.'.$phpEx;
});

$user->assert('id', '\d+');

$app->mount('/user', $user);
