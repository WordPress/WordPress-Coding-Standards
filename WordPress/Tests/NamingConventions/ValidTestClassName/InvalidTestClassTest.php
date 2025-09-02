<?php
/**
 * Test file for ValidTestClassName sniff - invalid test cases.
 *
 * @package WPCS\WordPress
 */

// This class name doesn't match the filename
class InvalidTestClassTest extends \PHPUnit_Framework_TestCase {

	/**
	 * A test method.
	 *
	 * @return void
	 */
	public function testSomething() {
		$this->assertTrue( true );
	}
}

// Class name doesn't end with Test
class AnotherInvalidTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Another test method.
	 *
	 * @return void
	 */
	public function testSomethingElse() {
		$this->assertTrue( true );
	}
}

// Invalid test class with invalid name pattern
class test_invalid_name_Test extends \PHPUnit_Framework_TestCase {

	/**
	 * Test method.
	 *
	 * @return void
	 */
	public function testSomething() {
		$this->assertTrue( true );
	}
}
