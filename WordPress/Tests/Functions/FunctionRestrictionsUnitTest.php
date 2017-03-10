<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the FunctionRestrictions sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.10.0
 */
class WordPress_Tests_Functions_FunctionRestrictionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Add a number of extra restricted functions to unit test the abstract
	 * FunctionRestrictions class.
	 *
	 * Note: as that class extends the abstract FunctionRestrictions class, that's
	 * where we are passing the parameters to.
	 */
	protected function setUp() {
		parent::setUp();

		WordPress_AbstractFunctionRestrictionsSniff::$unittest_groups = array(
			'test' => array(
				'type'      => 'warning',
				'message'   => 'Detected usage of %s.',
				'functions' => array(
					'foobar',
					'barfoo',
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
		return array();

	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		$array     = array_fill( 4, 33, 1 );
		$array[18] = 2;
		$array[21] = 2;
		return $array;

	}

} // End class.
