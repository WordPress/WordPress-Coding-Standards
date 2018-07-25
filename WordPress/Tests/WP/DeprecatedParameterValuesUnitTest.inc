<?php

// All will give an ERROR.

get_bloginfo( 'home' );
get_bloginfo( 'siteurl' );
get_bloginfo( "text_direction" );
echo bloginfo( 'home' );
echo bloginfo( "siteurl" );
echo bloginfo( 'text_direction' );
add_settings_field( '', '', '', 'misc' );
add_settings_field( '', '', '', 'privacy' );
add_settings_section( '', '', '', 'misc' );
add_settings_section( '', '', 'privacy', 'privacy' );
register_setting( 'misc' );
register_setting( 'privacy' );
unregister_setting( 'misc' );
unregister_setting( 'privacy' );

add_settings_field( '', '', '' ); // OK.

// Dynamic values will not give a notice.
$value = 'text_direction';
get_bloginfo( $value );
get_bloginfo( CONSTANT );
echo bloginfo( function_name() );

get_bloginfo( 'wpurl' ); // OK.
add_settings_field( '', '', '', /* deliberately not passed */ ); // Valid for PHP 7.3+.
