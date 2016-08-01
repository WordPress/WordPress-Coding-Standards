<?php

$query = "SELECT * FROM $wpdb->users"; // Bad.

$wp_db->update( $wpdb->users, array( 'displayname' => 'Kanobe!' ), array( 'ID' => 1 ) ); // Bad.

$query = "SELECT * FROM $wpdb->usermeta"; // Bad.

$wp_db->update( $wpdb->usermeta, array( 'meta_value' => 'bar!' ), array( 'user_id' => 1, 'meta_key' => 'foo' ) ); // Bad.

$query = "SELECT * FROM $wpdb->posts"; // Ok.

if ( isset( $_SERVER['REMOTE_ADDR'] ) ) { // Bad.
	foo( $_SERVER['HTTP_USER_AGENT'] ); // Bad.
}

$x = $_COOKIE['bar']; // Bad.

$y = $_SERVER['REQUEST_URI']; // Ok.
