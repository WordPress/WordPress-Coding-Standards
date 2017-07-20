<?php

$query = "SELECT * FROM $wpdb->users"; // Error.

$wp_db->update( $wpdb->users, array( 'displayname' => 'Kanobe!' ), array( 'ID' => 1 ) ); // Error.

$query = "SELECT * FROM $wpdb->usermeta"; // Error.

$wp_db->update( $wpdb->usermeta, array( 'meta_value' => 'bar!' ), array( 'user_id' => 1, 'meta_key' => 'foo' ) ); // Error.

$query = "SELECT * FROM $wpdb->posts"; // Ok.

if ( isset( $_SERVER['REMOTE_ADDR'] ) ) { // Warning.
	foo( $_SERVER['HTTP_USER_AGENT'] ); // Warning.
}

$x = $_COOKIE['bar']; // Warning.

$y = $_SERVER['REQUEST_URI']; // Ok.

// Error.
$query = <<<EOD
SELECT * FROM $wpdb->usermeta
EOD;

// Warning
$phrase = <<<EOD
Your user-agent is {$_SERVER['HTTP_USER_AGENT']}
EOD;
