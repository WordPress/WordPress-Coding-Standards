<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the ArrayAssignmentRestrictions sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.3.0
 */
class WordPress_Tests_Arrays_ArrayAssignmentRestrictionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Fill in the $groups property to test the abstract class.
	 */
	protected function setUp() {
		parent::setUp();

		WordPress_AbstractArrayAssignmentRestrictionsSniff::$groups = array(
			'posts_per_page' => array(
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
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			3 => 1,
			5 => 1,
			7 => 2,
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
