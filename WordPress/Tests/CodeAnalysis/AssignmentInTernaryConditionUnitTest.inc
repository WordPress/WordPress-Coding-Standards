<?php

/*
 * Ternaries can only reliably be checked when in parenthesis.
 */

// OK.
$mode = ( $a == 'something' ) ? 'on' : 'off';
$mode = ( $a == 'on' ? 'true' : $a == 'off' ? 't' : 'f' );

// Bad.
$mode = ( $a = 'on' ) ? 'on' : 'off';
$mode = ( $a = 'on' ) ?: 'off';
$mode = ( $a = 'on' ) ? 'true' : ( $a = 'off' ? 't' : ${$a->prop} ); // Bad x 2.
$mode = ( $a = 'on' ? 'on' : 'off' );
$mode = ( ${$a->prop} = 'on' ?: 'off' );
$mode = ( $a = 'on' ? 'true' : ( $a = 'off' ? 't' : 'f' ) ); // Bad x 2.
$mode = ( $a['test'] = 'on' ? 'true' : $a['test'] = 'off' ? 't' : 'f' ); // Bad x 3. The first ? triggers 1, the second (correctly) 2.

// Currently not checked.
$mode = $a = 'on' ? 'on' : 'off';
$mode = $a = 'on' ?: 'off';
$mode = $a = 'on' ? 'true' : $a = 'off' ? 't' : 'f';

// Issue #1227.
( function () {
	$foo = 42;
	return 1 === 2 ? 'a' : 'b';
} );

call_user_func( function () {
	$foo = 42;
	return 1 === 2 ? 'a' : 'b';
} );

$content = preg_replace_callback(
	'/(?s)<table class="viz full.+?<\/table>/',
	function( $matches ) {
		$rowcount = substr_count( $table, '<tr>' );
		$height   = $rowcount < 6 ? '100' : '250';
	},
	$content
);
