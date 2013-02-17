<?php

foreach(array('forums') as $acpService) {
    $app['acp.'.$acpService] = $app->share(function() use ($acpService) {
        global $phpbb_root_path, $phpEx;

        require_once $phpbb_root_path . 'includes/acp/acp_' . $acpService.'.'.$phpEx;

        return new acp_forums();
    });
}

$app['db'] = function() { 
    return $GLOBALS['db'];
};