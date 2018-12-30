<?php

/*
 * Bad: invalid prefix passed
 */
// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[] wp
function wp_do_something() {}

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[] ^%&
function ^%&_do_something() {}

// Now let's set the real prefixes we want to test for.
// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[] acronym,tgmpa

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
function tgmpacd_do_something() {} // OK.


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
function acronym_do_something_else( $param = 'default' ) {
	$var = 'abc';
	${$something} = 'value';
}

function ( $param ) {
	$var = 'abc';
};

class Acronym_Example {
	const SOME_CONSTANT = 'value';

	public $var = 'abc';

	function do_something( $param = 'default' ) {}
}

$acronym_class = new class {
	const SOME_CONSTANT = 'value';

	public $var = 'abc';

	function do_something( $param = 'default' ) {}
};

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

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals custom_test_class_whitelist[] My_TestClass
class Test_Class_D extends My_TestClass {

	const SOME_CONSTANT = 'value';

	public $var = 'abc';

	function do_something() {}
}
// phpcs:set WordPress.NamingConventions.PrefixAllGlobals custom_test_class_whitelist[]


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

// Dashes and other non-word characters are ok as a hook name separator after the prefix.
// The rule that these should be underscores is handled by another sniff.
do_action( 'acronym-action' ); // OK.
apply_filters( 'acronym/filter', $var ); // OK.
do_action( "acronym-action-{$acronym_filter_var}" ); // OK.
apply_filters( 'acronym/filter-' . $acronym_filter_var ); // OK.

// Issue #1056.
define( 'SomeNameSpace\PLUGIN_FILE', __FILE__ ); // OK.
define( '\OtherNameSpace\PLUGIN_FILE', __FILE__ ); // OK.
// OK: unreachable constants.
define( __NAMESPACE__ . '\PLUGIN_FILE', __FILE__ );
define( '\PLUGIN_FILE', __FILE__ );

namespace TGMPA\Testing {
	define( 'MY' . __NAMESPACE__, __FILE__ ); // Error, not actually namespaced.
	define( 'MY\\' . __NAMESPACE__, __FILE__ ); // OK, even though strangely setup, the constant is in a namespace.
}

// OK: whitelisted core hooks.
apply_filters( 'widget_title', $title );
do_action( 'add_meta_boxes' );

add_shortcode( 'acronym_hello', function( $attrs, $content = null ) { // OK. Variables are function params.
	// Do something.
} );

// Issue #1239 - word separator check is not the concern of this sniff.
do_action( 'acronymAction' ); // OK.
apply_filters( 'acronymFilter', $var ); // OK.

function acronymDoSomething( $param = 'default' ) {} // OK.
class AcronymExample {} // OK.

// Issue #1236 - detect non-prefixed variables created in control structure conditions.
if ( ( $acronym_abc = function_call() ) === true ) {} // OK.
if ( ( $abc = function_call() ) === true ) {} // Bad.

$acronym_something = array();
foreach ( $acronym_something as $acronym_some ) {} // OK.
foreach ( $acronym_something as $something ) {} // Bad.
foreach ( $acronym_something as $key => $acronym_something ) {} // Bad.
foreach ( $acronym_something as $acronym_key => $something ) {} // Bad.
foreach ( $acronym_something as $key => $something ) {} // Bad x 2.

while ( ( $acronymSomething = function_call() ) === true ) {} // OK.
while ( ( $something = function_call() ) === true ) {} // Bad.

for ( $acronym_i = 0; $acronym_i < 10; $acronym_i++ ) {} // OK.
for ( $i = 0; $i < 10; $i++ ) {} // Bad.

switch( true ) {
	case ($acronym_case = 'abc'): // OK.
		break;
	case ($case = 'abc'): // Bad.
		break;
	case ($case === 'abc'): // OK, not an assignment.
		break;
}

// All OK: Variables created within a non-global scope do not need to be prefixed.
function acronymFunction() {
	if ( ( $abc = function_call() ) === true ) {}
	foreach ( $acronym_something as $something ) {}
	foreach ( $acronym_something as $key => $something ) {}
	while ( ( $something = function_call() ) === true ) {}
	for ( $i = 0; $i < 10; $i++ ) {}

	switch( true ) {
		case ($case = 'abc'):
			break;
	}
}

// Issue #1311 - allow for overrulable WP Core constants.
define( 'FORCE_SSL_ADMIN', true );
const SCRIPT_DEBUG = $develop_src;

// Allow for hook name prefixes with less conventional separators.
// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[] test-this,myplugin\
do_action( 'test-this-hookname' ); // OK.
apply_filters( 'myplugin\filtername', $var ); // OK.

// Non-prefixed constant and action within a (nested) anonymous test class is fine.
class Some_Test_Class extends NonTestClass { // Bad.
	public function some_test_method() {
		define( 'SOME_GLOBAL', '4.0.0' ); // Bad.

		return new class extends \PHPUnit_Framework_TestCase {
			public function testPass() {
				define( 'SOME_GLOBAL', '4.0.0' ); // OK.

				do_action( 'some-action', $something ); // OK.
			}
		};
	}
}

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[] wordpress,somethingelse
// The above line adds an issue to line 1 about a blacklisted prefix.
function wordpress_do_something() {} // Bad.
function somethingelse_do_something() {} // OK.

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[] my_wordpress_plugin
apply_filters( 'my_wordpress_plugin_filtername', $var ); // OK.

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[] test-this
do_action( 'Test-THIS-hookname' ); // OK.

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[] acronym,tgmpa
// Issue #1495 - throw the error on the line with the non-prefixed name.
$acronym = apply_filters(
	'content-types-post-types', // Bad.
	[
		PostType\Post::NAME => PostType\Post::class,
	]
);

define(
	/* comment */
	'SOME_GLOBAL', // Bad.
	[ 1, 2, 3 ]
);

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[]
