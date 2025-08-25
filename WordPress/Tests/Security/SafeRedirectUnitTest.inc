<?php

wp_redirect( $location ); // Warning.
wp_safe_redirect( $location ); // OK.

/*
 * Safeguard support for PHP 8.0+ tokenization of namespaced "names".
 */
\wp_redirect( $location );
MyNamespace\wp_redirect( $location );
\MyNamespace\wp_redirect( $location );
namespace\wp_redirect( $location ); // The sniff should start flagging this once it can resolve relative namespaces.
