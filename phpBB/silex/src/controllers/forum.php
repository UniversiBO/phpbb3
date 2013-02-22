<?php

use Symfony\Component\HttpFoundation\Request;

$forum = $app['controllers_factory'];

$forum->get('/{id}', function($id) use($app) {
    return $app->json($app['acp.forums']->get_forum_info($id));
})->assert('id', '\d+');

$forum->get('/{name}', function(Silex\Application $app, $name){
    $db = $app['db'];
    $table = FORUMS_TABLE;
    
    $query = <<<EOT
SELECT *
    FROM $table
    WHERE
        forum_name = '{$db->sql_escape($name)}'
EOT;
    
    $db->sql_query($query);
    
    $rows = array();
    
    while(false !== ($row = $db->sql_fetchrow())) {
        $rows[] = $row;
    }
    
    return $app->json($rows);
});

$forum->post('', function(Request $request) use ($app){
    $forum_data['prune_days'] = $forum_data['prune_viewed'] = $forum_data['prune_freq'] = 0;
    $forum_data['forum_topics_per_page'] = 0;
    
    $forum_data['forum_type'] = (int)$request->get('type', FORUM_POST);
    $forum_data['parent_id'] = (int)$request->get('parent_id', 0);
    $forum_data['forum_name'] = $request->get('name', '');
    $forum_data['forum_desc'] = $request->get('description', '');
    $forum_data['forum_rules'] = $request->get('rules', '');
    
    if(empty($forum_data['forum_name'])) {
        throw new InvalidArgumentException('Empty forum name');
    }

    $errors = $app['acp.forums']->update_forum_data($forum_data);
    $count = count($errors);
    if($count > 0) {
        throw new InvalidArgumentException($count.' invalid parameters');
    }
    
    return $app->json($forum_data);
});

$app->mount('/forum', $forum);