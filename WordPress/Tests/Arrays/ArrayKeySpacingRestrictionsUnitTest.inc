<?php

// Variable keys.
foo( $arr[$test] ); // Bad.
bar( $arr[ $test] ); // Bad.
foo( $arr[$test ] ); // Bad.
bar( $arr[ $test ] ); // Good.

// The opposite with strings.
foo( $arr['test'] ); // Good.
bar( $arr[ 'test'] ); // Bad.
foo( $arr['test' ] ); // Bad.
bar( $arr[ 'test' ] ); // Bad.

// Nested ones.
foo( $arr[ $test[$taz] ] ); // Bad.
bar( $arr[ $test[ 'taz' ] ] ); // Bad.
foo( $arr[$test[ 'taz' ]] ); // Bad.
bar( $arr[ $test['taz'] ] ); // Good.

// Mixed key.
foo( $arr[ 'string' . $var ] ); // Good, should have spaces.
bar( $arr['string'.$var] ); // Bad.

// Non-string/int.
$arr[FooClass::FOO_KEY]; // Bad.

// Space size check.
bar( $arr[
	'test'

	   ] ); // Bad.
bar( $arr[     $test    ] ); // Bad x 2.
$arr[
		FooClass::FOO_KEY


	]; // Bad x 2.

$a = $arr[    /*comment*/ 'key']; // Bad x 2.
$a = $arr[/*comment*/ 'key'   ]; // Bad x 2.

// Verify handling of array assignments without an explicit key.
$bar[] = 10; // Good.
$bar[   ] = 10; // Bad.
$bar[
    ] = 10; // Bad.

// Verify handling of array assignments without an explicit key, but with a comment.
$bar[ /*comment*/ ] = 10; // Good.
$bar[/*comment*/] = 10; // Bad.

// Integer keys.
$arr[0]; // Good.
$arr[-1]; // Good.
$arr[+1]; // Good.

$arr[ 0 ]; // Bad.
$arr[ -1 ]; // Bad.
$arr[ +1 ]; // Bad.

// Verify key calculations are handled correctly.
$arr[ 10 + 5 ]; // Good.
$arr[ 5-2 - -1 ]; // Good.

$arr[ 10 + 5]; // Bad.
$arr[5-2 - -1 ]; // Bad.

// Verify non-decimal integer keys are handled correctly.
$arr[012]; // Good.
$arr[0b01]; // Good.
$arr[0xCAFE]; // Good.

$arr[ 012 ]; // Bad.
$arr[ 0b01 ]; // Bad.
$arr[ 0xCAFE ]; // Bad.

// Ensure correct handling of PHP 7.4+ numeric literals with underscores.
$arr[1_000_000]; // Good.
$arr[0xCAFE_F00D]; // Good.

$arr[ 1_000_000 ]; // Bad.
$arr[ 0xCAFE_F00D ]; // Bad.

// Ensure correct handling of PHP 8.1+ octal literals.
$arr[0o137]; // Good.
$arr[ 0o137 ]; // Bad.
