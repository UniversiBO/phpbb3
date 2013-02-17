<?php

$app['acp.forums'] = $app->share(function(){
    global $phpbb_root_path, $phpEx;
    
    require_once $phpbb_root_path . 'includes/acp/acp_forums.'.$phpEx;
    
    return new acp_forums();
});

$app['acp.users'] = $app->share(function(){
    global $phpbb_root_path, $phpEx;
    
    require_once $phpbb_root_path . 'includes/acp/acp_users.'.$phpEx;
    
    return new acp_users();
});