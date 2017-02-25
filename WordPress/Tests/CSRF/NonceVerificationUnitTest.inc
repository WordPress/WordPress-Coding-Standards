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
// @codingStandardsChangeSetting WordPress.CSRF.NonceVerification customNonceVerificationFunctions my_nonce_check
// @codingStandardsChangeSetting WordPress.CSRF.NonceVerification customSanitizingFunctions sanitize_pc,sanitize_twitter
// @codingStandardsChangeSetting WordPress.CSRF.NonceVerification customUnslashingSanitizingFunctions do_something

function foo_5() {

	sanitize_twitter( $_POST['foo'] ); // OK.
	sanitize_pc( $_POST['bar'] ); // OK.
	my_nonce_check( do_something( $_POST['tweet'] ) ); // OK.
}

// @codingStandardsChangeSetting WordPress.CSRF.NonceVerification customSanitizingFunctions sanitize_pc
// @codingStandardsChangeSetting WordPress.CSRF.NonceVerification customUnslashingSanitizingFunctions false

function foo_5() {

	do_something( $_POST['foo'] ); // Bad.
	sanitize_pc( $_POST['bar'] ); // OK.
	sanitize_twitter( $_POST['bar'] ); // Bad.
	my_nonce_check( sanitize_twitter( $_POST['tweet'] ) ); // OK.
}

// @codingStandardsChangeSetting WordPress.CSRF.NonceVerification customNonceVerificationFunctions false
// @codingStandardsChangeSetting WordPress.CSRF.NonceVerification customSanitizingFunctions false

function foo_5() {

	do_something( $_POST['foo'] ); // Bad.
	sanitize_pc( $_POST['bar'] ); // Bad.
	my_nonce_check( sanitize_twitter( $_POST['tweet'] ) ); // Bad.
}
