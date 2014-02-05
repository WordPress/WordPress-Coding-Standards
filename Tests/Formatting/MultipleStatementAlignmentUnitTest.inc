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

// Should be good. See https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/138
$options = array( 'min_range' => 0 );
$key     = filter_var( $key, FILTER_VALIDATE_INT, $options );

// Should be good. See https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/138#issuecomment-30581086
$args['value']  = false;
$args['type']   = 'checkbox';
$args['field']  = 'template_default';
$args['label']  = __( 'Make default ?', Plugin_Notes::$name );
$args['inline'] = true;
