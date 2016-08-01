<?php

$GLOBALS['wpdb'] = 'test';

global $wpdb;
$wpdb = 'test';

global $post;
$post = get_post( 1 ); // Override ok.