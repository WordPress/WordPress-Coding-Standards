<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the VariableRestrictions sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.3.0
 */
class WordPress_Tests_Variables_VariableRestrictionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Fill in the $groups property to test the abstract class.
	 */
	protected function setUp() {
		parent::setUp();

		WordPress_AbstractVariableRestrictionsSniff::$groups = array(
			'test' => array(
				'type'          => 'error',
				'message'       => 'Detected usage of %s',
				'object_vars'   => array(
					'$foo->bar',
					'FOO::var',
					'FOO::reg*',
					'FOO::$static',
				),
				'array_members' => array(
					'$foo[\'test\']',
				),
				'variables'     => array(
					'$taz',
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
			11 => 1,
			15 => 1,
			17 => 1,
			21 => 1,
			23 => 1,
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
