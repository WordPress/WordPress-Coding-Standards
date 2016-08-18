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

/**
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


/**
 * Verify all PHP supported magic methods.
 */
class Its_A_Kind_Of_Magic {
	function __construct() {} // Ok.
	function __destruct() {} // Ok.
	function __call() {} // Ok.
	function __callStatic() {} // Ok.
	function __get() {} // Ok.
	function __set() {} // Ok.
	function __isset() {} // Ok.
	function __unset() {} // Ok.
	function __sleep() {} // Ok.
	function __wakeup() {} // Ok.
	function __toString() {} // Ok.
	function __set_state() {} // Ok.
	function __clone() {} // Ok.
	function __invoke() {} // Ok.
	function __debugInfo() {} // Ok.
}

/**
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
	public function __doRequest() {} // Bad.
	private function __getFunctions() {} // Bad.
	protected function __getLastRequest() {} // Bad.
	public function __getLastRequestHeaders() {} // Bad.
	public function __getLastResponse() {} // Bad.
	public function __getLastResponseHeaders() {} // Bad.
	public function __getTypes() {} // Bad.
	public function __setCookie() {} // Bad.
	public function __setLocation() {} // Bad.
	public function __setSoapHeaders() {} // Bad.
	public function __soapCall() {} // Bad.
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
