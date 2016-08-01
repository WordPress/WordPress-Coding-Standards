<?php

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . $_GET['title'] . "';" ); // Bad.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '{$_GET['title']}';" ); // Bad.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '$var';" ); // Bad.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE 'Hello World!';" ); // Ok.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '{$_GET['title']}';" ) ); // Bad.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '$var';" ) ); // Bad.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE %s;", $_GET['title'] ) ); // Ok.

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . $escaped_var . "';" ); // WPCS: unprepared SQL OK.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '{$escaped_var}';" ); // WPCS: unprepared SQL OK.

$wpdb->query( $wpdb->prepare( "SELECT SUBSTRING( post_name, %d + 1 ) REGEXP '^[0-9]+$'", array( 123 ) ) ); // Ok.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title = 'The \$_GET var can be evil.' AND ID = %s", array( 123 ) ) ); // Ok.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title = 'The $_GET[foo] var is evil.' AND ID = %s", array( 123 ) ) ); // Bad.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title = 'The \\$_GET[foo]// var is evil again.' AND ID = %s", array( 123 ) ) ); // Bad.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title = 'The \$_GET var can be evil, but $_GET[foo] var is evil.' AND ID = %s", array( 123 ) ) ); // Bad.

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . foo() . "';" ); // Bad.
$wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . foo() . "';" ) ); // Bad.

$wpdb->query( "SELECT * FROM " . $wpdb->posts . " WHERE post_title LIKE 'foo';" ); // Ok.

// All OK.
$all_post_meta = $wpdb->get_results( $wpdb->prepare( sprintf(
	'SELECT `post_id`, `meta_value` FROM `%s` WHERE `meta_key` = "sort_order" AND `post_id` IN (%s)',
	$wpdb->postmeta,
	implode( ',', array_fill( 0, count( $post_ids ), '%d' ) )
), $post_ids ) );

$wpdb->query( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '" . esc_sql( $foo ) . "';" ); // Ok.
$wpdb->query( "SELECT * FROM $wpdb->posts WHERE ID = " . absint( $foo ) . ";" ); // Ok.
