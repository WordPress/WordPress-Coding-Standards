<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Tests\Arrays;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;
use WordPress\AbstractArrayAssignmentRestrictionsSniff;

/**
 * Unit test class for the ArrayAssignmentRestrictions sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class ArrayAssignmentRestrictionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Fill in the $groups property to test the abstract class.
	 */
	protected function setUp() {
		parent::setUp();

		AbstractArrayAssignmentRestrictionsSniff::$groups = array(
			'foobar' => array(
				'type'    => 'error',
				'message' => 'Found assignment value of %s to be %s',
				'keys'    => array(
					'foo',
					'bar',
				),
			),
		);
	}

	/**
	 * Reset the $groups property.
	 */
	protected function tearDown() {
		AbstractArrayAssignmentRestrictionsSniff::$groups = array();
		parent::tearDown();
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			3  => 1,
			5  => 1,
			7  => 2,
			20 => 1,
		);

	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array();

	}

} // End class.
