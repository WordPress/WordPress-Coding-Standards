<?php

in_array( 1, array( '1', 1, true ), true ); // Ok.

in_array( 1, array( '1', 1, true ) ); // Warning.
in_array( 1, array( '1', 1, true ), false ); // Warning.
IN_ARRAY( 1, array( '1', 1, true ), false ); // Warning.

Foo::in_array( 1, array( '1', 1, true ) ); // Ok.
Foo::
	in_array( 1, array( '1', 1, true ) ); // Ok.

$bar->in_array( 1, array( '1', 1, true ) ); // Ok.
$bar->
	in_array( 1, array( '1', 1, true ) ); // Ok.

in_array(); // Warning.


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
array_keys( array( '1', 1, true ), 'my_key', false ); // Warning
in_array( 1, array( '1', 1 ), TRUE ); // Ok.

use function in_array; // OK.

// Safeguard support for PHP 8.0+ named parameters.
in_array( strict: true, haystack: $haystack1, needle: 1, ); // Ok.
in_array( needle: 1, haystack: $haystack ); // Warning.

array_keys( array: $testing ); // Ok.
array_keys( strict: false, array: $testing ); // Ok, will cause fatal error, but that's not the concern of this sniff.
array_keys( $testing, filter_value: 'my_key' ); // Warning.
array_keys( $testing, strict: false, filter_value: 'my_key' ); // Warning.

// Safeguard that comments in the parameter are ignored.
in_array( 1, array( '1', 1 ), /* strict */ TRUE ); // Ok.
array_search(
	'needle',
	$haystack,
	true // Use strict typing.
); // Ok.
