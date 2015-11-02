<?php

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . $_GET['title'] . "';" ); // Bad
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '{$_GET['title']}';" ); // Bad
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '$var';" ); // Bad
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE 'Hello World!';" ); // OK
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '{$_GET['title']}';" ) ); // Bad
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '$var';" ) ); // Bad
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE %s;", $_GET['title'] ) ); // OK

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . $escaped_var . "';" ); // WPCS: unprepared SQL OK.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '{$escaped_var}';" ); // WPCS: unprepared SQL OK.

$wpdb->query( $wpdb->prepare( "SELECT SUBSTRING( post_name, %d + 1 ) REGEXP '^[0-9]+$'", array( 123 ) ) ); // OK
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title = 'The \$_GET var can be evil.' AND ID = %s", array( 123 ) ) ); // OK
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title = 'The $_GET[foo] var is evil.' AND ID = %s", array( 123 ) ) ); // Bad
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title = 'The \\$_GET[foo]// var is evil again.' AND ID = %s", array( 123 ) ) ); // Bad
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title = 'The \$_GET var can be evil, but $_GET[foo] var is evil.' AND ID = %s", array( 123 ) ) ); // Bad
