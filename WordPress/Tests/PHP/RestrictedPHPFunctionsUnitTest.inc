<?php

add_action( 'widgets_init', create_function( '', // Error.
	'return register_widget( "time_more_on_time_widget" );'
) );

/*
 * Safeguard correct handling of all types of namespaced function calls.
 */
\create_function('', 'return;');
MyNamespace\create_function('', 'return;');
\MyNamespace\create_function('', 'return;');
namespace\create_function('', 'return;'); // The sniff should start flagging this once it can resolve relative namespaces.
