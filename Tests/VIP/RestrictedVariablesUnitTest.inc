<?php

$query = "SELECT * FROM $wpdb->users";

$wp_db->update( $wpdb->users, array( 'displayname' => 'Kanobe!' ), array( 'ID' => 1 ) );

$query = "SELECT * FROM $wpdb->usermeta";

$wp_db->update( $wpdb->usermeta, array( 'displayname' => 'Kanobe!' ), array( 'ID' => 1 ) );
