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
