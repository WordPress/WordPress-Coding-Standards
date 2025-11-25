<?php

date_default_timezone_set( 'Foo/Bar' ); // Bad.

$date = new DateTime();
$date->setTimezone( new DateTimeZone( 'America/Toronto' ) ); // Yay!

$post_data['post_title'] = sprintf( __( 'Draft created on %1$s at %2$s' ), date( __( 'F j, Y' ), $now ), date( __( 'g:i a' ), $now ) ); // Error.
$post_data['post_title'] = sprintf( __( 'Draft created on %1$s at %2$s' ), gmdate( __( 'F j, Y' ), $now ), gmdate( __( 'g:i a' ), $now ) ); // OK.

/*
 * Safeguard correct handling of all types of namespaced function calls.
 */
\date_default_timezone_set( 'Foo/Bar' );
MyNamespace\date_default_timezone_set( 'Foo/Bar' );
\MyNamespace\date_default_timezone_set( 'Foo/Bar' );
namespace\date_default_timezone_set( 'Foo/Bar' ); // The sniff should start flagging this once it can resolve relative namespaces.
