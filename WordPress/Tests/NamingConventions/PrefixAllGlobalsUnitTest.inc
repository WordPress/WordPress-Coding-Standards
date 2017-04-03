<?php

/*
 * Bad: invalid prefix passed
 */
// @codingStandardsChangeSetting WordPress.NamingConventions.PrefixAllGlobals prefixes wp
function wp_do_something() {}

// @codingStandardsChangeSetting WordPress.NamingConventions.PrefixAllGlobals prefixes ^%&
function ^%&_do_something() {}

// Now let's set the real prefixes we want to test for.
// @codingStandardsChangeSetting WordPress.NamingConventions.PrefixAllGlobals prefixes acronym,tgmpa

/*
 * Bad - not prefixed.
 */
function do_something() {
	global $something;

	$something = 'value';
	$GLOBALS['something'] = 'value';
}

$var = 'abc';

define( 'SOME_CONSTANT', 'value' );
const SOME_CONSTANT = 'value';

class Example {}
interface Example_Interface {}
trait Example_Trait {}

do_action( 'plugin_action' );
apply_filters( 'theme_filter', $var );


/*
 * OK - prefixed.
 */
function acronym_do_something() {
	global $acronym_something;

	$acronym_something = 'value';
	$GLOBALS['acronym_something'] = 'value';
}

$acronym_var = 'abc';

define( 'ACRONYM_SOME_CONSTANT', 'value' );
const ACRONYM_SOME_CONSTANT = 'value';

class Acronym_Example {}
interface Acronym_Example_Interface {}
trait Acronym_Example_Trait {}

do_action( 'acronym_plugin_action' );
apply_filters( 'acronym_theme_filter', $var );

/*
 * OK - test secondary prefix.
 */
function tgmpa_do_something() {}

$tgmpa_var = 'abc';

define( 'TGMPA_SOME_CONSTANT', 'value' );
const TGMPA_SOME_CONSTANT = 'value';

class TGMPA_Example {}

do_action( 'tgmpa_plugin_action' );
apply_filters( 'tgmpa_theme_filter', $var );


/*
 * Bad: prefix not correctly used.
 */
function abtgmpa_do_something() {} // Bad.
function tgmpacd_do_something() {} // Bad.


/*
 * OK - allow for function/var/constant/class etc names to be just and only the prefix.
 */
function acronym() {
	global $acronym;

	$acronym = 'value';
	$GLOBALS['acronym'] = 'value';
}

$acronym = 'abc';

define( 'ACRONYM', 'value' );
const ACRONYM = 'value';

class Acronym {}
interface Acronym {}
trait Acronym {}

do_action( 'acronym' );
apply_filters( 'acronym', $var );


/*
 * OK - not in the global namespace.
 */
function acronym_do_something( $param = 'default' ) {
	$var = 'abc';
}

function ( $param ) {
	$var = 'abc';
}

class Acronym_Example {
	const SOME_CONSTANT = 'value';

	public $var = 'abc';

	function do_something( $param = 'default' ) {}
}

$acronym_class = new class {
	const SOME_CONSTANT = 'value';

	public $var = 'abc';

	function do_something( $param = 'default' ) {}
}

namespace Acronym {
	function do_something( $param = 'default' ) {}

	const SOME_CONSTANT = 'value';

	class Example {}
	interface I_Example {}
	trait T_Example {}
}


/*
 * OK - exceptions whitelisted by default.
 */
$_POST['something'] = 'value';

do_action_deprecated( 'set_current_user' ); // Deprecated hook, ignored.

// WP global variables, override warning is handled by another sniff.
function acronym_do_something() {
	global $post;
	$post = 'value';
	$GLOBALS['post'] = 'value';
}

/*
 * OK - test class - skips forward.
 */
class Example extends WP_UnitTestCase {
	const SOME_CONSTANT = 'value';

	public $var = 'abc';

	function do_something() {}
}

// @codingStandardsChangeSetting WordPress.NamingConventions.PrefixAllGlobals custom_test_class_whitelist My_TestClass
class Test_Class_D extends My_TestClass {

	const SOME_CONSTANT = 'value';

	public $var = 'abc';

	function do_something() {}
}
// @codingStandardsChangeSetting WordPress.NamingConventions.PrefixAllGlobals custom_test_class_whitelist false


/*
 * OK - whitelisted via whitelist comment.
 */
if ( ! function_exists( 'intdiv' ) ) {
	// Fill in for a PHP function which is not available in low PHP versions.
	function intdiv() { // WPCS: prefix ok.
		// Some code.
    }
}

if ( ! defined( 'PHP_VERSION_ID' ) ) {
    $acronym_version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', (int) (($acronym_version[0] * 10000) + ($acronym_version[1] * 100) + $acronym_version[2])); // WPCS: prefix ok.
    unset($acronym_version);
}

$something = 'abc'; // WPCS: prefix ok.

// Executing a WP core action or filter is sometimes ok.
do_action( 'set_current_user' ); // WPCS: prefix ok.
apply_filters( 'excerpt_edit_pre', $var ); // WPCS: prefix ok.
