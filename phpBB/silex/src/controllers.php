<?php

$files = array(
    'forum',
    'user'
);

foreach($files as $file) {
    require_once __DIR__ .'/controllers/'.$file.'.'.$phpEx;
}
