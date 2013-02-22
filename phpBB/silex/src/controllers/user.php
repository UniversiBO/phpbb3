<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$userc = $app['controllers_factory'];

$userc->get('/{id}', function(Application $app, $id){
    $user_id_ary = array($id);
    $username_ary = array();
    
    $result = user_get_id_name($user_id_ary, $username_ary);
    
    if('NO_USERS' === $result) {
        throw new NotFoundHttpException('User not found');
    }
    
    return $app->json($username_ary[$id]);
});

$userc->get('/{username}', function(Application $app, $username){
    $user_id_ary = array();
    $username_ary = array($username);
    
    $result = user_get_id_name($user_id_ary, $username_ary);
    
    if('NO_USERS' === $result) {
        throw new NotFoundHttpException('User not found');
    }
    
    return $app->json((int)$user_id_ary[0]);
});

$userc->post('/', function(Application $app, Request $request) {
    $param = $request->request;
    
    $userData = array(
        'username'   => $param->get('username') ,
        'user_email' => $param->get('email')    ,
        'group_id'   => $param->get('group_id') ,
        'user_type'  => $param->get('type')     ,
    );
    
    return $app->json(user_add($userData));
});

$userc->before(function() {
    global $phpbb_root_path, $phpEx;
    require_once $phpbb_root_path . 'includes/functions_user.'.$phpEx;
});

$userc->assert('id', '\d+');

$app->mount('/user', $userc);
