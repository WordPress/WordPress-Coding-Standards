<?php

$good = array( 'value1', 'value2' ); // Ok.

$query_vars = array( 'food' ); // Bad, no spaces after opening and before closing parenthesis.

// Test for fixing of extra whitespace.
$test = array( 1, 2 );

$bad = array(
	'key' => 'value',
); // Bad, each value of an associative array should start on a new line.

// Test for fixing nested associative arrays.
$bad = array(
	array(
		'key1' => 'value1',
		'key2' => [
			'sub1' => 1,
			'sub2' => 2,
		],
	),
	$key3 => 'value3',
	[
		'value4',
		10 => 'value5',
	],
); // Bad.

// Test for fixing mixed single & multi-line nested associative arrays.
$bad = array(
	array(
		'key1' => 'value1',
		array(
			'sub1' => 1,
			'sub2' => 2,
		),
	),
	$key3 => 'value3',
	[
		'value4',
		10 => 'value5',
	]
); // Bad.

// Test for fixing associative arrays with multiple values & whitespace in front.
// This needs to be the last test as the scope indent sniff will otherwise screw things up.
		$bad = array(
			'key1' => 'value1',
			'key2' => 'value2',
			$key3 => 'value3',
			'value4',
			10 => 'value5',
		); // Bad.
