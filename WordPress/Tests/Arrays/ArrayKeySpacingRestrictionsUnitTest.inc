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

$arr[0]; // Good.
$arr[ 0 ]; // Bad.
$arr[-1]; // Good.
$arr[ -1 ]; // Bad.
