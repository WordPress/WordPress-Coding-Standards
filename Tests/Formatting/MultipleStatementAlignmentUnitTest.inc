<?php

// Good
$foo   = 'somevalue';
$foo2  = 'somevalue2';
$foo34 = 'somevalue3';
$foo5  = 'somevalue4';

// NOT Good, produces warning.
$foo = 'somevalue';
$foo2 = 'somevalue2';
$foo34 = 'somevalue3';
$foo5 = 'somevalue4';

// Good
$my_array = array(
	'foo'   => 'somevalue',
	'foo2'  => 'somevalue2',
	'foo3'  => 'somevalue3',
	'foo34' => 'somevalue3',
);

// Still Good
$my_array = array(
	'foo' => 'somevalue',
	'foo2' => 'somevalue2',
	'foo3' => 'somevalue3',
	'foo34' => 'somevalue3',
);