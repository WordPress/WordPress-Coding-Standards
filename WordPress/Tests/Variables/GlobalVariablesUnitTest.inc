<?php

$GLOBALS['wpdb'] = 'test'; // Bad.

global $wpdb;
$wpdb = 'test'; // Bad.

$post = get_post( 1 ); // Ok, $post has not been made global yet.

global $post;
$post = get_post( 1 ); // Override ok.

// Bad: Using different types of assignment operators.
function test_different_assignment_operators() {
	global $totals, $blogname;
	$totals   += 10;
	$totals   /= 2;
	$blogname .= 'test';
}

// $GLOBALS with non-string key, so we don't know what is really overwritten.
$GLOBALS[ $blogname ] = 'test'; // Ok.

// $GLOBALS with simple obfuscated string key.
$GLOBALS[ 'p' . 'a' . 'ge' ] = 'test'; // Bad.
$GLOBALS[ 'p' . $a . 'ge' ] = 'test'; // Probably bad, but not checked for at the moment.

// Issue #300 - Setting default value for a function parameter.
function local_var_with_default( $param1, $error = true, $post = null ) {} // Ok.

// Issue #300 - Take scope into account.
function global_vars() {
	global $pagenow;

	$pagenow           = 'test'; // Bad, global variable in function scope.
	$GLOBALS['domain'] = 'http://example.com/'; // Bad, global variable in function scope.

	$post = '2016'; // Ok, non-global variable in function scope.
}

// Test against cross-contamination of global detection.
// https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/486
function local_var_only() {
	$pagenow = 'test'; // Ok, function scope.
}

add_filter( 'comments_open', function( $open, $post_id ) {
	$post = get_post( $post_id ); // Ok, non-global variable in function scope.
	return 'page' === $page->post_type;
}, 10, 2 );

add_filter( 'comments_open', function( $open, $post_id ) {
	global $page;
	$page = get_post( $post_id ); // Bad.
	return 'page' === $page->post_type;
}, 10, 2 );

$page = 'test'; // Ok, check against cross-contaminiation from within a closure.

// Allow overriding globals in functions within unit test classes.
// https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/300#issuecomment-158778606
trait WP_UnitTestCase {

	public function test_something() {
		global $tabs;
		$tabs = 50; // Ok.
	}
}

class Test_Class_A extends WP_UnitTestCase {

	public function test_something() {
		global $tabs;
		$tabs = 50; // Ok.
	}

	private function arbitrary_function() {
		global $post_ID;
		$post_ID = 50; // Ok.
	}
}

class Test_Class_B extends PHPUnit_Framework_TestCase {

	public function test_something() {
		global $cat_id;
		$cat_id = 50; // Ok.
	}
}

class Test_Class_C extends NonTestClass {

	public function test_something() {
		global $cat_id;
		$cat_id = 50; // Bad - trait does not extend either of the two acceptable testcase classes.
	}
}

// Ok: overriding class property with same name as global variable.
trait My_Class {
	private static $page;

	public function do_something() {
		global $page;
		self::$page = 'test'; // Ok, class property, not global variable.
		$post       = 'test'; // Ok, local variable.
	}
}

// Test adding additional test classes to the whitelist.
// @codingStandardsChangeSetting WordPress.Variables.GlobalVariables custom_test_class_whitelist My_TestClass
class Test_Class_D extends My_TestClass {

	public function test_something() {
		global $tabs;
		$tabs = 50; // Ok.
	}
}
// @codingStandardsChangeSetting WordPress.Variables.GlobalVariables custom_test_class_whitelist false

// Test detecting within and skipping over anonymous classes.
global $year;
add_filter( 'comments_open', new class {
	public $year = 2017; // Ok.

	public function __construct( $open, $post_id ) {
		global $page;
		$page = get_post( $post_id ); // Bad.
		return 'page' === $page->post_type;
	}
}, 10, 2 );

$GLOBALS['totals']   ??= 10; // Bad.
