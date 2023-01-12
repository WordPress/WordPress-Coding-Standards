<?php

function myTemplateTags() {} // Bad, using camel caps.

function my_template_tags() {} // Good.

function _my_template_tags() {} // OK.

function __my_template_tags() {} // Bad.

class My_Plugin {

	public static function __my_init() {} // Bad, only PHP magic methods should be prefixed with double underscore.

	public function myInit() {} // Bad.

	public static function _my_init() {} // OK.

	public function my_init() {} // OK.

	public function __invoke() {} // OK.
}

/*
 * Verify that CamelCase is not checked for extended classes or interfaces.
 */
class Test extends WP_UnitTestCase {

	public function setUp() {} // OK.
}

class Foo implements ArrayAccess {
	function offsetSet( $key, $value ) {} // OK.
	function offsetUnset( $key ) {} // OK.
	function offsetExists( $key ) {} // OK.
	function offsetGet( $key ) {} // OK.
}


/*
 * Verify all PHP supported magic methods.
 */
class Its_A_Kind_Of_Magic {
	function __construct() {} // Ok.
	function __destruct() {} // Ok.
	function __call( $a, $b ) {} // Ok.
	static function __callStatic( $a, $b ) {} // Ok.
	function __get( $a ) {} // Ok.
	function __set( $a, $b ) {} // Ok.
	function __isset( $a ) {} // Ok.
	function __unset( $a ) {} // Ok.
	function __sleep() {} // Ok.
	function __wakeup() {} // Ok.
	function __toString() {} // Ok.
	function __set_state() {} // Ok.
	function __clone() {} // Ok.
	function __invoke() {} // Ok.
	function __debugInfo() {} // Ok.
}

/*
 * Verify SoapClient magic methods.
 */
class My_Soap extends SoapClient {
	public function __doRequest() {} // Ok.
	public function __getFunctions() {} // Ok.
	public function __getLastRequest() {} // Ok.
	public function __getLastRequestHeaders() {} // Ok.
	public function __getLastResponse() {} // Ok.
	public function __getLastResponseHeaders() {} // Ok.
	public function __getTypes() {} // Ok.
	public function __setCookie() {} // Ok.
	public function __setLocation() {} // Ok.
	public function __setSoapHeaders() {} // Ok.
	public function __soapCall() {} // Ok.
}

class My_Soap {
	public function __doRequest() {} // Bad x 2.
	private function __getFunctions() {} // Bad x 2.
	protected function __getLastRequest() {} // Bad x 2.
	public function __getLastRequestHeaders() {} // Bad x 2.
	public function __getLastResponse() {} // Bad x 2.
	public function __getLastResponseHeaders() {} // Bad x 2.
	public function __getTypes() {} // Bad x 2.
	public function __setCookie() {} // Bad x 2.
	public function __setLocation() {} // Bad x 2.
	public function __setSoapHeaders() {} // Bad x 2.
	public function __soapCall() {} // Bad x 2.
}

class My_Soap extends somethingElse {
	public function __doRequest() {} // Ok - as somethingElse might be extended from SoapClient again.
	private function __getFunctions() {} // Ok.
	protected function __getLastRequest() {} // Ok.
	public function __getLastRequestHeaders() {} // Ok.
	public function __getLastResponse() {} // Ok.
	public function __getLastResponseHeaders() {} // Ok.
	public function __getTypes() {} // Ok.
	public function __setCookie() {} // Ok.
	public function __setLocation() {} // Ok.
	public function __setSoapHeaders() {} // Ok.
	public function __soapCall() {} // Ok.
}

function __MyTemplateTags() {} // Bad x 2.

// Verify that interfaces which extend multiple other interfaces do not throw unnecessary errors.
interface MyInterface extends InterfaceA, InterfaceB {
	function __MyTemplateTags(); // OK.
}

class Nested {
    public function get_anonymous_class() {
        return new class() {
            public function nestedFunction() {}
            protected function __something() {}
        };
    }
}

abstract class My_Class {
    public function my_Class() {}
    public function _MY_CLASS() {}
}

/**
 * Function description.
 *
 * @since 1.2.3
 * @deprecated 2.3.4
 *
 * @return void
 */
function __deprecatedFunction() {}

class Deprecated {
	/**
	 * Function description.
	 *
	 * @since 1.2.3
	 * @deprecated 2.3.4
	 *
	 * @return void
	 */
	public static function __deprecatedMethod() {}
}

class PHP74Magic {
	function __serialize() {} // OK.
	function __unserialize() {} // OK.
}

class More_Nested {
	public function method_name() {
		function __autoload() {} // OK - magic function in global namespace.
		function __CamelCase() {} // Bad x 2 for *function*, not method.
	}
}

function ___triple_underscore() {} // OK.

class Triple {
	function ___triple_underscore() {} // OK.
}

class DeprecatedWithAttribute {
	/**
	 * Function description.
	 *
	 * @since 1.2.3
	 * @deprecated 2.3.4
	 *
	 * @return void
	 */
	#[SomeAttribute]
	#[AnotherAttribute]
	public static function __deprecatedMethod() {}
}

// Safeguard that the suggested replacement name does not suggest undue changes to underscores in the name.
class UnderScoreHandling {
	public function ___Leading_Underscores() {} // Bad, replacement suggestion should be: ___leading_underscores.
	public function Multiple_______Underscores() {} // Bad, replacement suggestion should be: multiple_______underscores.
	public function Trailing_Underscores___() {}// Bad, replacement suggestion should be: trailing_underscores___.
}

// Safeguard that functions with a name only consisting of underscores are always ignored.
function __() {}

class OnlyUnderscores {
	public function _____() {}
}

// Class vs function name PHP case-sensitivity quirks.
class FooBÃÈ {
	function FooBÃÈ() {} // OK, same case.
	function fOOBÃÈ() {} // OK, same case for the non-ascii chars.
	function FooBãè() {} // Bad - not PHP 4-type constructor, non ascii chars not in same case - POC: https://3v4l.org/YOc2R.
}

// Safeguard that methods in enums are correctly handled by the sniff.
enum Suit {
	case Hearts;
	case Diamonds;

	public function color(): string {} // OK.
	public function __changeColor(): string {} // Bad

	public function &getShape(): string // Bad.
	{
		return "Rectangle";
	}

	// These are the only three magic methods allowed in enums.
	public function __call( $a, $b ) {} // Ok.
	public static function __callStatic( $a, $b ) {} // Ok.
	public function __invoke() {} // Ok.
}

// Related to #1891 - ensure the sniff does not throw an error if the suggested alternative would be the same as the original name.
function lähtöaika() {} // OK.
function lÄhtÖaika() {} // Bad, but only handled by the sniff if Mbstring is available.
function lÄhtOaika() {} // Bad, handled via transliteration of non-ASCII chars if Mbstring is not available.

// Live coding/parse error.
// This has to be the last test in the file.
function
