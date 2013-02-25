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

$forum->put('{id}/children/order', function($id) use ($app) {
    $db = $app['db'];
    
    $compare = function($a, $b) {
        return strcasecmp($a['forum_name'], $b['forum_name']);
    };
    
    $getRows = function() use ($db, $id, $compare) {
        $db->sql_query('SELECT * FROM ' . FORUMS_TABLE . ' WHERE parent_id = '.$id. ' ORDER BY left_id');

        $rows = array();
        $i = 0;
        while(false !== ($row = $db->sql_fetchrow())) {
            $rows[] = array(
                'position'   => $i++,
                'parent_id'  => $row['parent_id'],
                'forum_name' => $row['forum_name'],
                'left_id'    => $row['left_id'],
                'right_id'   => $row['right_id']
            );
        }
        
        usort($rows, $compare);
        
        $maxDelta = 0;
        $maxI = null;
        
        foreach($rows as $i => $row) {
            $rows[$i]['delta'] = $delta = $row['position'] - $i;
            
            if(($deltax = abs($delta)) > $maxDelta) {
                $maxI = $i;
                $maxDelta = $deltax;
            }
        }
        
        return array('rows' => $rows, 'maxI' => $maxI);
    };
    
    $sorted = function(array $rows) use ($compare) {
        $n = count($rows);
        for($i=1; $i<$n; ++$i) {
            if($compare($rows[$i-1], $rows[$i]) > 0) {
                return false;
            }
        }
        
        return true;
    };
    
    $result = $getRows();
    
    while(!$sorted($result['rows'])) {
        $forum = $result['rows'][$result['maxI']];

        $action = $forum['delta'] > 0 ? 'move_up' : 'move_down';
        $steps  = abs($forum['delta']);
        
        $app['acp.forums']->move_forum_by($forum, $action, $steps);
        
        $result = $getRows();
    }
    
    return $app->json($result['rows']);
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
        $response = $app->json(array(
            'status' => 'error',
            'errors' => $errors
        ));
        $response->setStatusCode(500);
        
        return $response;
    }
    
    return $app->json($forum_data);
});

$app->mount('/forum', $forum);