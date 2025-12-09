<?php

esc_html( 'text' );
esc_html( $var );

esc_html( 'text', 'domain' ); // Warning.
\ESC_HTML( $foo, $bar ); // Warning.
esc_ATTR(
	'text', // Some comment.
	MY_DOMAIN // More comment.
); // Warning.

/*
 * Safeguard correct handling of all types of namespaced function calls.
 */
\esc_attr( 'text', 'domain' ); // Warning.
MyNamespace\esc_html( 'text', 'domain' );
\MyNamespace\esc_attr( 'text', 'domain' );
namespace\esc_html( 'text', 'domain' ); // The sniff should start flagging this once it can resolve relative namespaces.
namespace\Sub\esc_attr( 'text', 'domain' );
