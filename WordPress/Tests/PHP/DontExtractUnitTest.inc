<?php

extract( array( 'a' => 1 ) ); // Bad.

// Similarly named functions or methods however are fine.
my_extract(); // Ok.
My_Object::extract(); // Ok.
$this->extract(); // Ok.
$my_object->extract(); // Ok.

/*
 * Safeguard correct handling of all types of namespaced function calls.
 */
\extract( array( 'a' => 1 ) );
MyNamespace\extract( array( 'a' => 1 ) );
\MyNamespace\extract( array( 'a' => 1 ) );
namespace\extract( array( 'a' => 1 ) ); // The sniff should start flagging this once it can resolve relative namespaces.
