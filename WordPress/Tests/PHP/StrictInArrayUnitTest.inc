<?php

in_array( 1, array( '1', 1, true ), true ); // OK

in_array( 1, array( '1', 1, true ) ); // Warning

in_array( 1, array( '1', 1, true ); // Error

in_array( 1, array( '1', 1, true ), false ); // Warning
IN_ARRAY( 1, array( '1', 1, true ), false ); // Warning

Foo::in_array( 1, array( '1', 1, true ) ); // OK
Foo::
	in_array( 1, array( '1', 1, true ) ); // OK

$bar->in_array( 1, array( '1', 1, true ) ); // OK
$bar->
	in_array( 1, array( '1', 1, true ) ); // OK

in_array(); // Error
