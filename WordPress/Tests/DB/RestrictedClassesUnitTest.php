<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\DB;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;
use WordPressCS\WordPress\AbstractFunctionRestrictionsSniff;

/**
 * Unit test class for the DB_RestrictedClasses sniff.
 *
 * @since 0.10.0
 * @since 0.13.0 Class name changed: this class is now namespaced.
 * @since 3.0.0  Renamed the fixtures to create compatibility with PHPCS 4.x/PHPUnit >=8.
 *
 * @covers \WordPressCS\WordPress\AbstractClassRestrictionsSniff
 * @covers \WordPressCS\WordPress\Helpers\RulesetPropertyHelper
 * @covers \WordPressCS\WordPress\Sniffs\DB\RestrictedClassesSniff
 */
final class RestrictedClassesUnitTest extends AbstractSniffUnitTest {

	/**
	 * Add a number of extra restricted classes to unit test the abstract
	 * ClassRestrictions class.
	 *
	 * Note: as that class extends the abstract FunctionRestrictions class, that's
	 * where we are passing the parameters to.
	 *
	 * @before
	 *
	 * @return void
	 */
	protected function enhanceGroups() {
		AbstractFunctionRestrictionsSniff::$unittest_groups = array(
			'test' => array(
				'type'    => 'error',
				'message' => 'Detected usage of %s.',
				'classes' => array(
					'\My\DBlayer',
					'AdoDb\Test*',
				),
			),
		);
	}

	/**
	 * Reset the $groups property.
	 *
	 * @after
	 *
	 * @return void
	 */
	protected function resetGroups() {
		AbstractFunctionRestrictionsSniff::$unittest_groups = array();
		parent::tearDown();
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
	 */
	public function getErrorList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'RestrictedClassesUnitTest.1.inc':
				return array(
					17  => 1,
					18  => 1,
					19  => 1,
					20  => 1,
					22  => 1,
					23  => 1,
					24  => 1,
					25  => 1,
					26  => 1,
					27  => 1,
					29  => 1,
					30  => 1,
					32  => 1,
					33  => 1,
					35  => 1,
					36  => 1,
					37  => 1,
					39  => 1,
					40  => 1,
					42  => 1,
					51  => 1,
					52  => 1,
					63  => 1,
					65  => 1,
					66  => 1,
					69  => 1,
					82  => 1,
					87  => 1,
					88  => 1,
					91  => 1,
					103 => 1,
					106 => 1,
					115 => 1,
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
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected warnings.
	 */
	public function getWarningList() {
		return array();
	}
}
