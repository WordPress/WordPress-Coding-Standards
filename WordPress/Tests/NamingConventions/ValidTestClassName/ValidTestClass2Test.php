<?php
/**
 * Test file for ValidTestClassName sniff - valid test case.
 *
 * @package WPCS\WordPress
 */

/**
 * Test class for ValidTestClassName sniff.
 */
class ValidTestClass2Test extends WP_UnitTestCase {

	/**
	 * A test method.
	 *
	 * @return void
	 */
	public function testSomething() {
		$this->assertTrue( true );
	}
}
