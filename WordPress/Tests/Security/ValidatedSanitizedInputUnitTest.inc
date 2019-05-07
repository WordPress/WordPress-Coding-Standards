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
	// Ok, if ValidatedSanitizedInputSniff::$check_validation_in_scope_only == false .
	if ( ! isset( $_REQUEST['bar1'] ) || ! foo( sanitize_text_field( wp_unslash( $_REQUEST['bar1'] ) ) ) ) {
		wp_die( 1 );
	}
}

// Ok, if ValidatedSanitizedInputSniff::$check_validation_in_scope_only == false .
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
if ( isset( $_POST['foo'] ) && 'bar' === $_POST['foo'] ) {} // Ok.
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

echo esc_html( wp_strip_all_tags( wp_unslash( $_GET['a'] ) ) ); // Ok.

// Test validation check vs anonymous functions.
isset( $_POST['abc'] ); // Validation in global scope, not function scope.
$b = function () {
	return sanitize_text_field( wp_unslash( $_POST['abc'] ) ); // Error for no validation.
};

/*
 * Test using custom properties, setting & unsetting (resetting).
 */
function test_this() {
	if ( ! isset( $_POST['abc_field'] ) ) {
		return;
	}
	
	$abc = sanitize_color( wp_unslash( $_POST['abc_field'] ) ); // Bad x1 - sanitize.

	// phpcs:set WordPress.Security.ValidatedSanitizedInput customSanitizingFunctions[] sanitize_color,sanitize_twitter_handle

	$abc = sanitize_color( wp_unslash( $_POST['abc_field'] ) ); // OK.
	$abc = sanitize_facebook_id( wp_unslash( $_POST['abc_field'] ) ); // Bad x1 - sanitize.
	$abc = sanitize_twitter_handle( $_POST['abc_field'] ); // Bad x1 - unslash.

	// phpcs:set WordPress.Security.ValidatedSanitizedInput customSanitizingFunctions[] sanitize_color,sanitize_facebook_id
	// phpcs:set WordPress.Security.ValidatedSanitizedInput customUnslashingSanitizingFunctions[] sanitize_twitter_handle

	$abc = sanitize_color( wp_unslash( $_POST['abc_field'] ) ); // OK.
	$abc = sanitize_facebook_id( wp_unslash( $_POST['abc_field'] ) ); // OK.
	$abc = sanitize_twitter_handle( $_POST['abc_field'] ); // OK.

	// phpcs:set WordPress.Security.ValidatedSanitizedInput customSanitizingFunctions[]
	// phpcs:set WordPress.Security.ValidatedSanitizedInput customUnslashingSanitizingFunctions[]

	$abc = sanitize_twitter_handle( $_POST['abc_field'] ); // Bad x2, sanitize + unslash.
}

// Variables in heredocs.
output( <<<EOD
some string \$_POST[some_var]
EOD
); // Ok.

output( <<<EOD
some string {$_POST[some_var]} {$_GET['evil']}
EOD
); // Bad x2.

if ( ( $_POST['foo'] ?? 'post' ) === 'post' ) {} // Bad x2 - unslash, sanitize - more complex compares are not handled.
if ( ( $_POST['foo'] <=> 'post' ) === 0 ) {} // OK.

// Test whitespace independent isset/empty detection.
function foobar() {
	if ( ! isset  ($_GET['test']) ) {
		return ;
	}
	echo sanitize_text_field( wp_unslash( $_GET['test'] ) ); // OK.
}

function barfoo() {
	if ( empty  ($_GET['test']) ) {
		return ;
	}
	echo sanitize_text_field( wp_unslash( $_GET['test'] ) ); // OK.
}

// Issue #1467.
if ( isset( $_POST[ 'currentid' ] ) ){
            $id = (int) $_POST[ "currentid" ]; // OK.
}

// Only recognize validation if the correct superglobal is examined.
if ( isset ( $_POST['thisisnotget'] ) ) {
	$get = (int) $_GET['thisisnotget']; // Bad.
}

// Recognize PHP native array_key_exists() as validation function.
if ( array_key_exists( 'my_field1', $_POST ) ) {
	$id = (int) $_POST['my_field1']; // OK.
}

if ( \array_key_exists( 'my_field2', $_POST ) ) {
	$id = (int) $_POST['my_field2']; // OK.
}

if ( \Some\ClassName\array_key_exists( 'my_field3', $_POST ) ) {
	$id = (int) $_POST['my_field3']; // Bad.
}

if ( $obj->array_key_exists( 'my_field4', $_POST ) ) {
	$id = (int) $_POST['my_field4']; // Bad.
}

if ( ClassName::array_key_exists( 'my_field5', $_POST ) ) {
	$id = (int) $_POST['my_field5']; // Bad.
}

echo sanitize_text_field (wp_unslash ($_GET['test'])); // OK.

if ( isset( $_GET['unslash_check'] ) ) {
	$clean = sanitize_text_field( WP_Faker::wp_unslash( $_GET['unslash_check'] ) ); // Bad x1 - unslash.
	$clean = WP_Faker\sanitize_text_field( wp_unslash( $_GET['unslash_check'] ) ); // Bad x1 - sanitize.
}

function test_more_safe_functions() {
	if ( ! isset( $_GET['test'] ) ) {
		return;
	}

	$float = doubleval( $_GET['test'] ); // OK.
	$count = count( $_GET['test'] ); // Issue #1659; OK.
}

function test_allow_array_key_exists_alias() {
	if ( key_exists( 'my_field1', $_POST ) ) {
	$id = (int) $_POST['my_field1']; // OK.
}

function test_correct_multi_level_array_validation() {
	if ( isset( $_POST['toplevel']['sublevel'] ) ) {
		$id = (int) $_POST['toplevel']; // OK, if a subkey exists, the top level key *must* also exist.
		$id = (int) $_POST['toplevel']['sublevel']; // OK.
		$id = (int) $_POST['toplevel']['sublevel']['subsub']; // Bad x1 - validate, this sub has not been validated.
	}

	if ( array_key_exists( 'bar', $_POST['foo'] ) ) {
		$id = (int) $_POST['bar']; // Bad x 1 - validate.
		$id = (int) $_POST['foo']; // OK.
		$id = (int) $_POST['foo']['bar']; // OK.
		$id = (int) $_POST['foo']['bar']['baz']; // Bad x 1 - validate.
	}
}

function test_correct_multi_level_array_validation_key_order() {
	if ( isset( $_POST['toplevel']['sublevel'] ) ) {
		$id = (int) $_POST['sublevel']['toplevel']; // Bad x 1 - validate - key order is wrong.
	}
}

function test_invalid_array_key_exists_call() {
	if ( array_key_exists( 'bar' ) ) {
		$id = (int) $_POST['bar']; // Bad x 1 - validate.
	}
}

function test_ignoring_is_type_function_calls() {
	// Usage within variable type tests does not need unslashing or sanitization.
	if ( isset( $_POST[ $key ] ) && is_numeric( $_POST[ $key ] ) ) {} // OK.
	if ( isset($_POST['plugin']) && is_string( $_POST['plugin'] ) ) {} // OK.

	if ( ! is_null( $_GET['not_set'] ) ) {} // Bad x1 - missing validation.
	if ( array_key_exists( 'null', $_GET ) && ! is_null( $_GET['null'] ) ) {} // OK.
	if ( array_key_exists( 'null', $_POST ) && $_POST['null'] !== null ) {} // OK.
}

function test_additional_array_walking_functions() {
	if ( ! isset( $_GET['test'] ) ) {
		return;
	}

	$sane = map_deep( wp_unslash( $_GET['test'] ), 'sanitize_text_field' ); // Ok.
	$sane = map_deep( wp_unslash( $_GET['test'] ), 'foo' ); // Bad.
}

function test_recognize_array_comparison_functions_as_such() {
	if ( ! isset( $_POST['form_fields'] ) ) {
		return;
	}

	if ( isset( $_REQUEST['plugin_status'] ) && in_array( $_REQUEST['plugin_status'], array( 'install', 'update', 'activate' ), true ) ) {} // OK.
	if ( isset( $_REQUEST['plugin_state'] ) && in_array( $_REQUEST['plugin_state'], $states, true ) ) {} // OK.
	if ( in_array( 'my_form_hidden_field_value', $_POST['form_fields'], true ) ) {} // OK.
	if ( array_search( $_POST['form_fields'], 'my_form_hidden_field_value' ) ) {} // OK.
	if ( array_keys( $_POST['form_fields'], 'my_form_hidden_field_value', true ) ) {} // OK.
	if ( array_keys( $_POST['form_fields'] ) ) {} // Bad x2.
}

/*
 * Test recognition of validation via null coalesce, while still checking the var for sanitization.
 */
function test_null_coalesce_1() {
	$var = sanitize_text_field( wp_unslash( $_POST['foo'] ?? '' ) ); // OK.
	$var = sanitize_text_field( wp_unslash( $_POST['fool'] ?? $_POST['secondary'] ?? '' ) ); // OK.
	$var = sanitize_text_field( wp_unslash( $_POST['bar']['sub'] ?? '' ) ); // OK.
	$var = sanitize_text_field( $_POST['foobar'] ?? '' ); // Bad x1 - unslash.
}

// The below two sets should give the same errors.
function test_null_coalesce_2() {
	$var = $_POST['foo'] ?? ''; // Bad x2 - sanitize + unslash.
	$var = $_POST['bar']['sub'] ?? ''; // Bad x2 - sanitize + unslash.
	$var = ( $_POST['foobar']['sub'] ?? '' ); // Bad x2 - sanitize + unslash.

	$var = isset( $_POST['_foo'] ) ? $_POST['_foo'] : ''; // Bad x2 - sanitize + unslash.
	$var = isset( $_POST['_bar']['_sub'] ) ? $_POST['_bar']['_sub'] : ''; // Bad x2 - sanitize + unslash.
	$var = ( isset( $_POST['_foobar']['_sub'] ) ? $_POST['_foobar']['_sub'] : '' ); // Bad x2 - sanitize + unslash.
}

function test_null_coalesce_validation() {
	$_POST['key'] = $_POST['key'] ?? 'default'; // OK, assignment & Bad x2 - unslash, sanitize.
	$key            = sanitize_text_field( wp_unslash( $_POST['key'] ) ); // OK, validated via null coalesce.
	$another_key    = sanitize_text_field( wp_unslash( $_POST['another_key'] ) ); // Bad, not validated, different key.
}

function test_null_coalesce_equals_validation() {
	$_POST['key'] ??= 'default'; // OK, assignment.
	$key            = sanitize_text_field( wp_unslash( $_POST['key'] ) ); // OK, validated via null coalesce equals.
	$another_key    = sanitize_text_field( wp_unslash( $_POST['another_key'] ) ); // Bad, not validated, different key.
}

function test_using_different_unslashing_functions() {
	if ( ! isset( $_GET['test'] ) ) {
		return;
	}

	$sane = sanitize_text_field(stripslashes_deep($_GET['test'])); // Ok.
	$sane = sanitize_text_field( stripslashes_from_strings_only( $_GET['test'] ) ); // OK.
}
