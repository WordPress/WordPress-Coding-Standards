<?php

do_something( $_POST ); // OK

do_something_with( $_POST['hello'] ); // Error for no validation, Error for no sanitizing

echo esc_html( $_POST['foo1'] ); // Error for no validation

if ( isset( $_POST['foo2'] ) ) {
	bar( $_POST['foo2'] ); // Error for no sanitizing
}

// @TODO: Cover non-parenthesis'd conditions
// if ( isset( $_POST['foo'] ) ) 
// 	bar( $_POST['foo'] );


if ( isset( $_POST['foo3'] ) ) {
	bar( esc_html( $_POST['foo3'] ) ); // Good, validated and sanitized
	bar( $_POST['foo3'] ); // Error, validated but not sanitized
	bar( esc_html( $_POST['foo3'] ) ); // Good, validated and sanitized
}

// Should all be OK
$empty = (
	empty( $_GET['foo4'] )
	||
	empty( $_REQUEST['foo4'] )
	||
	empty( $_POST['foo4'] )
);

$foo = $_POST['bar']; // Bad

function foo() {
	// Ok, if WordPress_Sniffs_VIP_ValidatedSanitizedInputSniff::$check_validation_in_scope_only == false
	if ( ! isset( $_REQUEST['bar1'] ) || ! foo( esc_attr( $_REQUEST['bar1'] ) ) ) {
		wp_die( 1 );
	}
}

// Ok, if WordPress_Sniffs_VIP_ValidatedSanitizedInputSniff::$check_validation_in_scope_only == false
if ( ! isset( $_REQUEST['bar2'] ) || ! foo( esc_attr( $_REQUEST['bar2'] ) ) ) { // Ok
	wp_die( 1 );
}

function foo() {
	if ( ! isset( $_GET['test'] ) ) {
		return ;
	}
	echo esc_html( $_GET['test'] ); // Good
}

$_REQUEST['wp_customize'] = 'on'; // ok

// All OK
$keys = array_keys( $_POST );
$values = array_values( $_POST );
foreach( $_POST as $key => $value ) {
	// ..
}

unset( $_GET['test'] ) // ok
