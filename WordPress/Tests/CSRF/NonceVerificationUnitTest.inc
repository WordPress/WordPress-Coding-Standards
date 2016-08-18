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
