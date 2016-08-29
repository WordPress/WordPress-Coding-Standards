<?php

do_something( $_POST ); // Ok.

do_something_with( $_POST['hello'] ); // Error for no validation, Error for no sanitizing, Error for no unslashing.

echo sanitize_text_field( wp_unslash( $_POST['foo1'] ) ); // Error for no validation.

if ( isset( $_POST['foo2'] ) ) {
	bar( wp_unslash( $_POST['foo2'] ) ); // Error for no sanitizing.
}

// @TODO: Cover non-parenthesis'd conditions
// if ( isset( $_POST['foo'] ) ) 
// 	bar( $_POST['foo'] );


if ( isset( $_POST['foo3'] ) ) {
	bar( sanitize_text_field( wp_unslash( $_POST['foo3'] ) ) ); // Good, validated and sanitized.
	bar( wp_unslash( $_POST['foo3'] ) ); // Error, validated but not sanitized.
	bar( sanitize_text_field( wp_unslash( $_POST['foo3'] ) ) ); // Good, validated and sanitized.
}

// Should all be OK.
$empty = (
	empty( $_GET['foo4'] )
	||
	empty( $_REQUEST['foo4'] )
	||
	empty( $_POST['foo4'] )
);

$foo = $_POST['bar']; // Bad x 3.

function foo() {
	// Ok, if WordPress_Sniffs_VIP_ValidatedSanitizedInputSniff::$check_validation_in_scope_only == false .
	if ( ! isset( $_REQUEST['bar1'] ) || ! foo( sanitize_text_field( wp_unslash( $_REQUEST['bar1'] ) ) ) ) {
		wp_die( 1 );
	}
}

// Ok, if WordPress_Sniffs_VIP_ValidatedSanitizedInputSniff::$check_validation_in_scope_only == false .
if ( ! isset( $_REQUEST['bar2'] ) || ! foo( sanitize_text_field( wp_unslash( $_REQUEST['bar2'] ) ) ) ) { // Ok.
	wp_die( 1 );
}

function bar() {
	if ( ! isset( $_GET['test'] ) ) {
		return ;
	}
	echo sanitize_text_field( wp_unslash( $_GET['test'] ) ); // Good.
}

$_REQUEST['wp_customize'] = 'on'; // Ok.

// All OK.
$keys = array_keys( $_POST );
$values = array_values( $_POST );
foreach( $_POST as $key => $value ) {
	// ..
}

unset( $_GET['test'] ); // Ok.

output( "some string {$_POST['some_var']}" ); // Bad.

echo (int) $_GET['test']; // Ok.
some_func( $some_arg, (int) $_GET['test'] ); // Ok.

function zebra() {
	if ( isset( $_GET['foo'], $_POST['bar'] ) ) {
		echo sanitize_text_field( wp_unslash( $_POST['bar'] ) ); // Ok.
	}
}

echo $_GET['test']; // WPCS: sanitization OK.

echo array_map( 'sanitize_text_field', wp_unslash( $_GET['test'] ) ); // Ok.
echo array_map( 'foo', wp_unslash( $_GET['test'] ) ); // Bad.
echo array_map( $something, wp_unslash( $_GET['test'] ) ); // Bad.
echo array_map( array( $obj, 'func' ), wp_unslash( $_GET['test'] ) ); // Bad.
echo array_map( array( $obj, 'sanitize_text_field' ), wp_unslash( $_GET['test'] ) ); // Bad.

// Sanitized but not validated.
$foo = (int) $_POST['foo6']; // Bad.

// Non-assignment checks are OK.
if ( 'bar' === $_POST['foo'] ) {} // Ok.
if ( $_GET['test'] != 'a' ) {} // Ok.
if ( 'bar' === do_something( wp_unslash( $_POST['foo'] ) ) ) {} // Bad.

switch ( $_POST['foo'] ) {} // Ok.
switch ( do_something( wp_unslash( $_POST['foo'] ) ) ) {} // Bad.

// Sanitization is required even when the value is being escaped.
echo esc_html( wp_unslash( $_POST['foo'] ) ); // Bad.
echo esc_html( sanitize_text_field( wp_unslash( $_POST['foo'] ) ) ); // Ok.

$current_tax_slug = isset( $_GET['a'] ) ? sanitize_key( $_GET['a'] ) : false; // Ok.
$current_tax_slug = isset( $_GET['a'] ) ? $_GET['a'] : false; // Bad x 2
$current_tax_slug = isset( $_GET['a'] ) ? wp_unslash( $_GET['a'] ) : false; // Bad.
$current_tax_slug = isset( $_GET['a'] ) ? sanitize_text_field( wp_unslash( $_GET['a'] ) ) : false; // Ok.

echo sanitize_text_field( $_POST['foo545'] ); // Error for no validation, unslashing.
echo array_map( 'sanitize_text_field', $_GET['test'] ); // Bad, no unslashing.
echo array_map( 'sanitize_key', $_GET['test'] ); // Ok.

foo( absint( $_GET['foo'] ) ); // Ok.
$ids = array_map( 'absint', $_GET['test'] ); // Ok.

if ( is_array( $_GET['test'] ) ) {} // Ok.

output( "some string \$_POST[some_var]" ); // Ok.
output( "some string \\$_POST[some_var] $_GET[evil]" ); // Bad x2.
