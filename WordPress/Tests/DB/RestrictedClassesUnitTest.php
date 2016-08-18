<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the DB_RestrictedClasses sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.10.0
 */
class WordPress_Tests_DB_RestrictedClassesUnitTest extends AbstractSniffUnitTest {

	/**
	 * Add a number of extra restricted classes to unit test the abstract
	 * ClassRestrictions class.
	 *
	 * Note: as that class extends the abstract FunctionRestrictions class, that's
	 * where we are passing the parameters to.
	 */
	protected function setUp() {
		parent::setUp();

		WordPress_AbstractFunctionRestrictionsSniff::$unittest_groups = array(
			'test' => array(
				'type'      => 'error',
				'message'   => 'Detected usage of %s.',
				'classes' => array(
					'\My\DBlayer',
					'AdoDb\Test*',
				),
			),
		);
	}

	/**
	 * Skip this test on PHP 5.2 as otherwise testing the namespace resolving would fail.
	 *
	 * @return bool Whether to skip this test.
	 */
	protected function shouldSkipTest() {
		return version_compare( PHP_VERSION, '5.3.0', '<' );
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'RestrictedClassesUnitTest.1.inc':
				return array(
					17 => 1,
					18 => 1,
					19 => 1,
					20 => 1,
					22 => 1,
					23 => 1,
					24 => 1,
					25 => 1,
					26 => 1,
					27 => 1,
					29 => 1,
					30 => 1,
					32 => 1,
					33 => 1,
					35 => 1,
					36 => 1,
					37 => 1,
					39 => 1,
					40 => 1,
				);

			case 'RestrictedClassesUnitTest.2.inc':
				return array(
					16 => 1,
					22 => 1,
					26 => 1,
					27 => 1,
					29 => 1,
					30 => 1,
					31 => 1,
					39 => 1,
					41 => 1,
					42 => 1,
					43 => 1,
					45 => 1,
					46 => 1,
					47 => 1,
				);

			case 'RestrictedClassesUnitTest.3.inc':
				return array(
					16 => 1,
					22 => 1,
					26 => 1,
					27 => 1,
					29 => 1,
					30 => 1,
					31 => 1,
					39 => 1,
					41 => 1,
					42 => 1,
					43 => 1,
					45 => 1,
					46 => 1,
					47 => 1,
					66 => 1,
					68 => 1,
					69 => 1,
					70 => 1,
					84 => 1,
					86 => 1,
					88 => 1,
					89 => 1,
				);

			default:
				return array();

		}

	} // end getErrorList()

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array();

	}

} // End class.
