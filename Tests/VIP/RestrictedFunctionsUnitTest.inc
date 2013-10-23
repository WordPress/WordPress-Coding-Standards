<?php

switch_to_blog( $blogid );

eval( 'some_code' );

create_function( 'foo', 'bar(foo);' );

file_get_contents( $url );

vip_wp_file_get_contents( $url );

wp_remote_get( $url );

curl_init();

curl_close();
