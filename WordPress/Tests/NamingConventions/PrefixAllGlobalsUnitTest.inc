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
	global $something, $else;

	$something = 'value';
	$GLOBALS['something'] = 'value';
	$GLOBALS[ 'something' . $else ] = 'value';
	$GLOBALS[ "something_{$else}" ] = 'value';
	$GLOBALS[ "something$else" ] = 'value';
}

$var = 'abc';

define( 'SOME_CONSTANT', 'value' );
const SOME_CONSTANT = 'value';

class Example {}
interface Example_Interface {}
trait Example_Trait {}

do_action( 'plugin_action' );
apply_filters( 'theme_filter', $var );
do_action( "plugin_action_{$acronym_filter_var}" );
apply_filters( 'theme_filter_' . $acronym_filter_var );


/*
 * OK - prefixed.
 */
function acronym_do_something() {
	global $acronym_something, $else;

	$acronym_something = 'value';
	$GLOBALS['acronym_something'] = 'value';
	$GLOBALS[ 'acronym_' . $else ] = 'value';
	$GLOBALS[ "acronym_something_{$else}" ] = 'value';
}

$acronym_var = 'abc';

define( 'ACRONYM_SOME_CONSTANT', 'value' );
const ACRONYM_SOME_CONSTANT = 'value';

class Acronym_Example {}
interface Acronym_Example_Interface {}
trait Acronym_Example_Trait {}

do_action( 'acronym_plugin_action' );
apply_filters( 'acronym_theme_filter', $var );
do_action( "acronym_plugin_action_{$acronym_filter_var}" );
apply_filters( 'acronym_theme_filter_' . $acronym_filter_var );


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
do_action( "tgmpa_plugin_action_{$acronym_filter_var}" );


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
	$GLOBALS[ 'acronym'  . $else ] = 'value'; // Presume the '_' is part of the $else.
	$GLOBALS[ "acronym{$else}" ] = 'value'; // Presume the '_' is part of the $else.
	$GLOBALS[ "acronym$else" ] = 'value'; // Presume the '_' is part of the $else.
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
	${$something} = 'value';
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


/*
 * Issue 915: OK/Bad - backfilled PHP functions will be recognized depending on the PHP version PHPCS runs on
 * and the extensions loaded in that version.
 */
if ( ! function_exists( 'mb_strpos' ) ) {
	// Fill in for a PHP function which is not always available (extension needs to be loaded).
	function mb_strpos() {}
}

if ( ! function_exists( 'array_column' ) ) {
	// Fill in for a PHP function which is not always available - introduced in PHP 5.5.
	function array_column() {}
}

if ( ! defined( 'E_DEPRECATED' ) ) {
	define( 'E_DEPRECATED', true ); // Introduced in PHP 5.3.0.
}

if ( ! class_exists( 'IntlTimeZone' ) ) {
	class IntlTimeZone {} // Introduced in PHP 5.5.0.
}


/*
 * Issue 915: dynamic names. Names starting with a dynamic part or
 * which are completely dynamic, will receive a warning.
 */
function acronym_something() {
	global $something;

	$GLOBALS[ $something ] = 'value'; // Warning.
	$GLOBALS[ "{$something}_something" ] = 'value'; // Warning.
}

$$something = 'value'; // Warning.
${$something} = 'value'; // Warning.
$$$${$something} = 'value'; // Warning.
${$something}['foo'] = 'value'; // Warning.
${$something}['foo']['bar'] = 'value'; // Warning.
${$something['foo']} = 'value'; // Warning.
$GLOBALS[ $something ] = 'value'; // Warning.
$GLOBALS[ "{$something}_something" ] = 'value'; // Warning.
$GLOBALS[ ${$something} ] = 'value'; // Warning.

define( ${$something}, 'value' ); // Warning.
define( $something, 'value' ); // Warning.
define( $something . '_CONSTANT', 'value' ); // Warning.
define( "{$something}_CONSTANT", 'value' ); // Warning.
define( $something . '_CONSTANT', 'value' ); // Warning.

do_action( "{$acronym_filter_var}_hook_name" ); // Warning.
do_action( "{$acronym_filter_var}hook_name" ); // Warning.
do_action( $acronym_filter_var ); // Warning.
do_action( $GLOBALS['something'] ); // Warning.
do_action( ${$acronym_filter_var} ); // Warning.
do_action( $GLOBALS[ ${$something} ] ); // Warning.
apply_filters( $_REQUEST['else'] ); // Warning.

class Acronym_Dynamic_Hooks {
	const FILTER = 'acronym';
	const FILTER_WITH_UNDERSCORE = 'acronym_';

	protected $filter = 'acronym';
	protected $filter_with_underscore = 'acronym_';

	public function test() {
		global $acronym_filter_var;
		${$this->name} = 'value'; // Warning.
		apply_filters( "{$acronym_filter_var}_hook" ); // Warning.
		do_action( $acronym_filter_var ); // Warning.

		do_action( $this->filter ); // Warning.
		apply_filters( $this->filter_array['key'] ); // Warning.
		do_action( "{$this->filter}_hook_name" ); // Warning.
		do_action( "{$this->filter_with_underscore}hook_name" ); // Warning.

		apply_filters( self::FILTER ); // Warning.
		apply_filters( self::FILTER_WITH_UNDERSCORE . 'hook_name' ); // Warning.
		apply_filters( self::FILTER_ARRAY['key'] ); // Warning.

		do_action( $this->parent_property ); // Warning.
	}
}
