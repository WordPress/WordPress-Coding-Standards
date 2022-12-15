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

// Safeguard support for PHP 8.0+ named parameters.
add_settings_section( $id, $title, $callback, args: $args ); // OK, optional parameter skipped.
add_settings_field( page: $page, section: $section, id: $id, title: $title, callback: $callback ); // OK, undetermined.
add_settings_section( callback: $callback, page: 'general', section: $section, id: $id, title: $title, ); // OK, not the deprecated value.
add_settings_field( page: 'misc', section: $section, id: $id, title: $title, callback: $callback ); // Error.

get_bloginfo( filter: $filter ); // OK, optional parameter skipped.
get_bloginfo( filter: $filter, show: $show, ); // OK, undetermined.
get_bloginfo( filter: $filter, show: 'admin_email', ); // OK, not the deprecated value.
get_bloginfo( filter: $filter, show: 'text_direction', ); // Error.
