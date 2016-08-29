<?php

in_array( 1, array( '1', 1, true ), true ); // Ok.

in_array( 1, array( '1', 1, true ) ); // Warning.

in_array( 1, array( '1', 1, true ); // Error.

in_array( 1, array( '1', 1, true ), false ); // Warning.
IN_ARRAY( 1, array( '1', 1, true ), false ); // Warning.

Foo::in_array( 1, array( '1', 1, true ) ); // Ok.
Foo::
	in_array( 1, array( '1', 1, true ) ); // Ok.

$bar->in_array( 1, array( '1', 1, true ) ); // Ok.
$bar->
	in_array( 1, array( '1', 1, true ) ); // Ok.

in_array(); // Error.


array_search( 1, $array, true ); // Ok.
$my->array_search( $array, $needle ); // Ok.

array_search( 1, $array, false ); // Warning.
array_search( 1, $array ); // Warning.
Array_Search( 1, $array ); // Warning.
ARRAY_SEARCH( 1, array( '1', 1, true ) ); // Warning.


array_keys( $testing ); // Ok.
array_keys( array( '1', 1, true ) ); // Ok.
array_keys( $testing, 'my_key', true ); // Ok.
array_keys( array( '1', 1, true ), 'my_key', true ); // Ok.

array_keys( $testing, 'my_key' ); // Warning.
array_keys( array( '1', 1, true ), 'my_key' ); // Warning.
array_keys( $testing, 'my_key', false ); // Warning.
array_keys( array( '1', 1, true ), 'my_key', false ); // Warning.
