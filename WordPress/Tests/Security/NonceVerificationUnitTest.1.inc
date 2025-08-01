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

	if ( empty( $_POST['test'] ) || ! wp_verify_nonce( 'some_action' ) ) {
		exit;
	}

	// Do things here.
}

class Some_Class {

	// Bad, needs nonce check.
	function bar() {
		if ( empty( $_POST['test'] ) ) { // Bad.
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

// Bad - ignored via old-style ignore comment.
function foo_3() {
	bar( $_POST['var'] ); // WPCS: CSRF OK.
	bar( $_POST['var'] ); // Bad.
}

// We need to account for when there are multiple vars in a single isset().
function foo_4() {
	if ( ! isset( $_POST['foo'], $_FILES['bar'], $_POST['_wpnonce'] ) ) { // OK.
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
	do_something( sanitize_key( $_FILES['bar'] ) ); // Bad.

	check_ajax_referer( 'something' );
}

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
function test_ignoring_use_in_type_test_functions() {
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
	if ( array_search( array( 'subscribe', 'unsubscribe' ), $_POST['action'], true ) // OK.
		&& wp_verify_nonce( $_POST['newsletter_nonce'] )
	) {}
}

function allow_for_array_comparison_in_condition() {
	if ( in_array( $_GET['action'], $valid_actions, true ) ) { // OK.
		check_admin_referer( 'foo' );
		foo();
	}
}

// Issue #572.
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

function dont_allow_bypass_nonce_via_sanitization_bad() {
	$var = sanitize_text_field( $_POST['foo'] ); // Bad.
	echo $var;
}

function dont_allow_bypass_nonce_via_sanitization_good() {
	$var = sanitize_text_field( $_POST['foo'] ); // OK.
	wp_verify_nonce( $var );
	echo $var;
}

// Issue #1694
function function_containing_nested_class() {
	if ( !class_exists( 'Nested_Class' ) ) {
		class Nested_Class extends Something {
			public function method_in_nested_class() {
				if ( isset( $_POST['my_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['my_nonce'] ) ), 'the_nonce' ) ) {
					if ( isset( $_POST['hello'] ) ) {
						echo 'world';
					}
				}
			}
		}
	}
}

function function_containing_nested_closure() {
	$closure = function() {
		if ( isset( $_POST['my_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['my_nonce'] ) ), 'the_nonce' ) ) {
			if ( isset( $_POST['hello'] ) ) {
				echo 'world';
			}
		}
	};
}

// Tests specifically for the ContextHelper::is_in_function_call().
function disallow_custom_unslash_before_noncecheck_via_method() {
	$var = MyClass::stripslashes_from_strings_only( $_POST['foo'] ); // Bad.
	wp_verify_nonce( $var );
	echo $var;
}

function disallow_custom_unslash_before_noncecheck_via_namespaced_function() {
	$var = MyNamespace\stripslashes_from_strings_only( $_POST['foo'] ); // Bad.
	wp_verify_nonce( $var );
	echo $var;
}

// Tests specifically for the ContextHelper::is_in_isset_or_empty().
function allow_in_array_key_exists_before_noncecheck() {
	if (array_key_exists('foo', $_POST) === false) { // OK.
		return;
	}

	wp_verify_nonce( 'some_action' );
}

function allow_in_key_exists_before_noncecheck() {
	if (key_exists('foo', $_POST['subset']) === false) { // OK.
		return;
	}

	wp_verify_nonce( 'some_action' );
}

function disallow_in_custom_key_exists_before_noncecheck() {
	if (My\key_exists('foo', $_POST['subset']) === false) { // Bad.
		return;
	}

	if ($obj?->array_key_exists('foo', $_POST['subset']) === false) { // Bad.
		return;
	}

	wp_verify_nonce( 'some_action' );
}

function disallow_in_array_key_exists_before_noncecheck_when_not_in_array_param() {
	if ( array_key_exists( $_POST, $GLOBALS ) === false ) { // Bad (not that it makes sense anyhow).
		return;
	}

	if ( array_key_exists( arrays: $_POST, key: 'foo', ) === false ) { // Bad (typo in param label).
		return;
	}

	wp_verify_nonce( 'some_action' );
}

function allow_in_array_key_exists_before_noncecheck_with_named_params() {
	if (array_key_exists( array: $_POST, key: 'foo', ) === false) { // OK.
		return;
	}

	wp_verify_nonce( 'some_action' );
}

// Tests specifically for the ContextHelper::is_in_array_comparison().
function allow_for_array_comparison_in_condition_non_lowercase_function_call() {
	if ( Array_Keys( $_GET['actions'], 'my_action', true ) ) { // OK.
		check_admin_referer( 'foo' );
		foo();
	}
}

function disallow_for_non_array_comparison_in_condition() {
	if ( array_keys( $_GET['actions'] ) ) { // Bad.
		check_admin_referer( 'foo' );
		foo();
	}
}

function allow_for_array_comparison_in_condition_with_named_params() {
	if ( array_keys( filter_value: 'my_action', array: $_GET['actions'], strict: true, ) ) { // OK.
		check_admin_referer( 'foo' );
		foo();
	}
}

function disallow_for_non_array_comparison_in_condition_with_named_params() {
	if ( array_keys( strict: true, array: $_GET['actions'], ) ) { // Bad, missing $filter_value param. Invalid function call, but not our concern.
		check_admin_referer( 'foo' );
		foo();
	}
}

function test_long_list_assignment() {
	list( $_POST['key1'], list( $_POST['key2'] ) ) = $something; // OK.
}
function test_short_list_assignment() {
	[ $_POST['key1'], [ $_POST['key2'] ] ] = $something; // OK.
}

function test_assignment_to_long_list_with_noncecheck() {
	wp_verify_nonce( $_POST['nonce'] );
	list( $key, list( $key2 ) ) = $_POST; // OK.
}
function test_assignment_to_short_list_with_noncecheck() {
	wp_verify_nonce( $_POST['nonce'] );
	[ $key, [ $key2 ] ] = $_POST; // OK.
}

function test_assignment_to_long_list_without_noncecheck() {
	list( $key, list( $key2 ) ) = $_POST; // Bad.
}
function test_assignment_to_short_list_without_noncecheck() {
	[ $key, [ $key2 ] ] = $_POST; // Bad.
}

function dont_throw_error_when_only_used_in_unset() {
	unset( $_POST['foo'] ); // OK.
}

function dont_throw_error_when_only_used_in_unset_but_error_for_other_use() {
	unset( $_POST['foo'] ); // OK.
	echo $_POST['bar']; // Bad.
}

function dont_throw_error_when_only_used_in_unset_when_there_is_nonce_check_before_other_use() {
	unset( $_POST['foo'] ); // OK.
	wp_verify_nonce( $_POST['prefix_nonce'] );
	echo $_POST['bar']; // OK.
}

function test_null_coalesce() {
	$var = $_POST['foo'] ?? 10; // OK.
	wp_verify_nonce( $_POST['nonce'] );
}
function test_null_coalesce_without_noncecheck() {
	$var = $_POST['foo'] ?? 10; // Bad.
	// Do something.
}

function test_null_coalesce_equals() {
	$_POST['foo'][0] ??= 10; // OK.
	wp_verify_nonce( $_POST['nonce'] );
}

function test_null_coalesce_equals_without_noncecheck() {
	$_POST['foo'][0] ??= 10; // Bad.
	// Do something.
}

function test_open_arrow_fn_with_noncecheck() {
	wp_verify_nonce( $_POST['nonce'] );
	$callback = fn() => $_POST['key']++; // OK.
}

function test_open_arrow_fn_without_noncecheck() {
	$callback = fn() => $_POST['key']++; // Bad.
}

function test_disregard_noncecheck_in_nested_arrow_function() {
	$callback = fn() => check_admin_referer( 'foo' );
	echo $_POST['foo']; // Bad, we don't know if the callback has been called or not.
}

function test_match() {
	$var = match($_POST['key']) { // OK, it's a comparison, with the nonce check after.
		'value' => wp_verify_nonce( $_POST['nonce'] ), // OK.
		default => $_POST['key'], // OK, due to check above. Realistically, this is wrong, but that goes for all conditional checks.
	};
}

function function_containing_nested_enum_with_nonce_check() {
	enum MyEnum {
		public function nested_method() {
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['my_nonce'] ) ), 'the_nonce' );
		}
	}

	echo $_POST['foo']; // Bad.
}

function function_containing_nested_enum_with_nonce_check_outside() {
	wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['my_nonce'] ) ), 'the_nonce' );

	enum MyEnum {
		public function nested_method() {
			echo $_POST['foo']; // Bad.
		}
	}
}

enum MyEnum {
	public function nested_method() {
		wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['my_nonce'] ) ), 'the_nonce' );
		echo $_POST['foo']; // OK.
	}
}
