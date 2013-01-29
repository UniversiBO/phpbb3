<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

define('IN_PHPBB', true);
$phpEx = 'php';
$phpbb_root_path = dirname(__DIR__) .'/';

require __DIR__.'/../common.php';
require __DIR__ .'/../includes/vendor/autoload.php';

require_once $phpbb_root_path . 'includes/acp/acp_forums.'.$phpEx;
require_once $phpbb_root_path . 'adm/includes/functions.'.$phpEx;
$forum = new acp_forums();

function create_json_response(array $data)
{
    $response = new Symfony\Component\HttpFoundation\Response();
    $response->headers->set('Content-type', 'application/json');
    $response->setContent(json_encode($data));
    
    return $response;
}

$app = new Application();

$app->post('/forum', function(Request $request) use ($forum){
    $forum_data['prune_days'] = $forum_data['prune_viewed'] = $forum_data['prune_freq'] = 0;
    $forum_data['forum_topics_per_page'] = 0;
    
    $forum_data['forum_type'] = (int)$request->get('type', FORUM_POST);
    $forum_data['parent_id'] = (int)$request->get('parent_id', 0);
    $forum_data['forum_name'] = $request->get('name', '');
    $forum_data['forum_desc'] = $request->get('description', '');
    $forum_data['forum_rules'] = $request->get('rules', '');

    $errors = $forum->update_forum_data($forum_data);
    $count = count($errors);
    if($count > 0) {
        throw new InvalidArgumentException($count.' invalid parameters');
    }
    
    return create_json_response($forum_data);
});

$app->get('/forum/{id}', function($id) use($forum) {
    return create_json_response($forum->get_forum_info($id));
});

$app->run();