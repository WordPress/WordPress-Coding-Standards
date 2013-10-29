<?php

$query = "SELECT * FROM $wpdb->users"; // bad

$wp_db->update( $wpdb->users, array( 'displayname' => 'Kanobe!' ), array( 'ID' => 1 ) ); // bad

$query = "SELECT * FROM $wpdb->usermeta"; // bad

$wp_db->update( $wpdb->usermeta, array( 'meta_value' => 'bar!' ), array( 'user_id' => 1, 'meta_key' => 'foo' ) ); // bad

$query = "SELECT * FROM $wpdb->posts"; // ok

if ( isset( $_SERVER['REMOTE_ADDR'] ) ) { // bad
	foo( $_SERVER['HTTP_USER_AGENT'] ); // bad
}

echo $_COOKIE['bar']; // bad
