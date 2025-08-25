<?php

extract( array( 'a' => 1 ) ); // Bad.

// Similarly named functions or methods however are fine.
my_extract(); // Ok.
My_Object::extract(); // Ok.
$this->extract(); // Ok.
$my_object->extract(); // Ok.

/*
 * Safeguard support for PHP 8.0+ tokenization of namespaced "names".
 */
\extract( array( 'a' => 1 ) );
MyNamespace\extract( array( 'a' => 1 ) );
\MyNamespace\extract( array( 'a' => 1 ) );
namespace\extract( array( 'a' => 1 ) ); // The sniff should start flagging this once it can resolve relative namespaces.
