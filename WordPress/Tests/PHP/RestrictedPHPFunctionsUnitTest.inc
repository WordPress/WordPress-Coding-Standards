<?php

add_action( 'widgets_init', create_function( '', // Error.
	'return register_widget( "time_more_on_time_widget" );'
) );

/*
 * Safeguard support for PHP 8.0+ tokenization of namespaced "names".
 */
\create_function('', 'return;');
MyNamespace\create_function('', 'return;');
\MyNamespace\create_function('', 'return;');
namespace\create_function('', 'return;'); // The sniff should start flagging this once it can resolve relative namespaces.
