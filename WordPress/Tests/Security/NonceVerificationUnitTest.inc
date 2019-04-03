<?php

// Bad, needs nonce check.
function bar() {
	if ( ! isset( $_POST['test'] ) ) { // Bad.
		return;
	}

	do_something( $_POST['test'] ); // Bad.
}

// Good, has an nonce check.
function ajax_process() {
	check_ajax_referer( 'something' );

	update_post_meta( (int) $_POST['id'], 'a_key', $_POST['a_value'] );
}
add_action( 'wp_ajax_process', 'ajax_process' );

// It's also OK to check with isset() before the nonce check.
function foo() {
	if ( ! isset( $_POST['test'] ) || ! wp_verify_nonce( 'some_action' ) ) {
		exit;
	}

	// Do things here.
}

// Doing other things with the request params before the nonce check is prohibited.
function process() {
	do_something( $_POST['foo'] ); // Bad.

	if ( ! isset( $_POST['test'] ) || ! wp_verify_nonce( 'some_action' ) ) {
		exit;
	}

	// Do things here.
}

class Some_Class {

	// Bad, needs nonce check.
	function bar() {
		if ( ! isset( $_POST['test'] ) ) { // Bad.
			return;
		}

		do_something( $_POST['test'] ); // Bad.
	}

	// Good, has an nonce check.
	function ajax_process() {
		check_ajax_referer( 'something' );

		update_post_meta( (int) $_POST['id'], 'a_key', $_POST['a_value'] );
	}

	// It's also OK to check with isset() before the the nonce check.
	function foo() {
		if ( ! isset( $_POST['test'] ) || ! wp_verify_nonce( 'some_action' ) ) {
			exit;
		}

		// Do things here.
	}

	// Doing other things with the request params before the nonce check is prohibited.
	function process() {
		do_something( $_POST['foo'] ); // Bad.

		if ( ! isset( $_POST['test'] ) || ! wp_verify_nonce( 'some_action' ) ) {
			exit;
		}

		// Do things here.
	}
}

// Assignments are allowed.
function foo_2() {
	$_POST = array( 'a' => 'b' ); // OK.
	$_POST['test'] = somethin(); // OK.
	$_POST['settings'][ $setting ] = 'bb'; // OK.
}

// Particular cases can be whitelisted with a comment.
function foo_3() {
	bar( $_POST['var'] ); // WPCS: CSRF OK.
	bar( $_POST['var'] ); // Bad.
}

// We need to account for when there are multiple vars in a single isset().
function foo_4() {
	if ( ! isset( $_POST['foo'], $_POST['bar'], $_POST['_wpnonce'] ) ) { // OK.
		return;
	}

	check_ajax_referer( 'something' );
}

// Sanitization before the nonce check is permitted.
function sanitization_allowed() {

	$foo = (int) $_POST['foo']; // OK.
	$bar = sanitize_key( $_POST['bar'] ); // OK.

	check_ajax_referer( "something-{$foo}-{$bar}" );
}

// The value must only be sanitized though.
function foo_5() {

	do_something( (int) $_POST['foo'] ); // Bad.
	do_something( sanitize_key( $_POST['bar'] ) ); // Bad.

	check_ajax_referer( 'something' );
}

// Test anonymous function - Bad, needs nonce check.
check_ajax_referer( 'something' ); // Nonce check is not in function scope.
$b = function () {
	if ( ! isset( $_POST['abc'] ) ) { // Bad.
		return;
	}

	do_something( $_POST['abc'] ); // Bad.
};

/*
 * Test using custom properties, setting & unsetting (resetting).
 */
// phpcs:set WordPress.Security.NonceVerification customNonceVerificationFunctions[] my_nonce_check
// phpcs:set WordPress.Security.NonceVerification customSanitizingFunctions[] sanitize_pc,sanitize_twitter
// phpcs:set WordPress.Security.NonceVerification customUnslashingSanitizingFunctions[] do_something

function foo_6() {

	sanitize_twitter( $_POST['foo'] ); // OK.
	sanitize_pc( $_POST['bar'] ); // OK.
	my_nonce_check( do_something( $_POST['tweet'] ) ); // OK.
}

// phpcs:set WordPress.Security.NonceVerification customSanitizingFunctions[] sanitize_pc
// phpcs:set WordPress.Security.NonceVerification customUnslashingSanitizingFunctions[]

function foo_7() {

	do_something( $_POST['foo'] ); // Bad.
	sanitize_pc( $_POST['bar'] ); // OK.
	sanitize_twitter( $_POST['bar'] ); // Bad.
	my_nonce_check( sanitize_twitter( $_POST['tweet'] ) ); // OK.
}

// phpcs:set WordPress.Security.NonceVerification customNonceVerificationFunctions[]
// phpcs:set WordPress.Security.NonceVerification customSanitizingFunctions[]

function foo_8() {

	do_something( $_POST['foo'] ); // Bad.
	sanitize_pc( $_POST['bar'] ); // Bad.
	my_nonce_check( sanitize_twitter( $_POST['tweet'] ) ); // Bad.
}

/*
 * Using a superglobal in a is_...() function is OK as long as a nonce check is done
 * before the variable is *really* used.
 */
function test_whitelisting_use_in_type_test_functions() {
	if ( ! is_numeric ( $_POST['foo'] ) ) { // OK.
		return;
	}

	wp_verify_nonce( 'some_action' );
}

function test_incorrect_use_in_type_test_functions() {
	if ( ! is_numeric ( $_POST['foo'] ) ) { // Bad.
		return;
	}
}

function fix_false_negatives_userland_method_same_name() {
	WP_Faker::check_ajax_referer( 'something' );
	$faker->check_admin_referer( 'something' );
	do_something( $_POST['abc'] ); // Bad.
}

function fix_false_negatives_namespaced_function_same_name() {
	WP_Faker\SecurityBypass\wp_verify_nonce( 'something' );
	do_something( $_POST['abc'] ); // Bad.
}

function skip_over_nested_constructs_1() {
	$b = function () {
		check_ajax_referer( 'something' ); // Nonce check is not in the same function scope.
	};

	do_something( $_POST['abc'] ); // Bad.
}

function skip_over_nested_constructs_2() {
	if ( $_POST['abc'] === 'test' ) { // Bad.
		return;
	}

	$b = new class() {
		public function named() {
			check_ajax_referer( 'something' ); // Nonce check is not in the same function scope.
		}
	};
}

// Issue #1506
function allow_for_compare_before_noncecheck() {
	if (
		'newsletter_sign_up' === $_POST['action'] && // OK.
		wp_verify_nonce( $_POST['newsletter_nonce'] )
	) {}
}

// Issue #1114
function allow_for_nonce_check_within_switch() {
	if ( ! isset( $_REQUEST['action'] ) ) {
		return;
	}

	switch ( $_REQUEST['action'] ) { // OK.
		case 'foo':
			check_admin_referer( 'foo' );
			break;
		case 'bar':
			check_admin_referer( 'bar' );
			break;
	}
}

function allow_for_array_compare_before_noncecheck() {
	if ( array_search( array( 'subscribe', 'unsubscribe', $_POST['action'], true ) // OK.
		&& wp_verify_nonce( $_POST['newsletter_nonce'] )
	) {}
}

function allow_for_array_comparison_in_condition() {
	if ( in_array( $_GET['action'], $valid_actions, true ) ) { // OK.
		check_admin_referer( 'foo' );
		foo();
	}
}

# Issue #572.
function allow_for_unslash_before_noncecheck_but_demand_noncecheck() {
	$var = wp_unslash( $_POST['foo'] ); // Bad.
	echo $var;
}

function allow_for_unslash_before_noncecheck() {
	$var = stripslashes_from_strings_only( $_POST['foo'] ); // OK.
	wp_verify_nonce( $var );
	echo $var;
}

function allow_for_unslash_in_sanitization() {
	$var = sanitize_text_field( wp_unslash( $_POST['foo'] ) ); // OK.
	wp_verify_nonce( $var );
	echo $var;
}

function dont_allow_bypass_nonce_via_sanitization() {
	$var = sanitize_text_field( $_POST['foo'] ); // Bad.
	echo $var;
}

function dont_allow_bypass_nonce_via_sanitization() {
	$var = sanitize_text_field( $_POST['foo'] ); // OK.
	wp_verify_nonce( $var );
	echo $var;
}
