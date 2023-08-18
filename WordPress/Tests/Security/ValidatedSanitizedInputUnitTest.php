<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Security;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the ValidatedSanitizedInput sniff.
 *
 * @since 0.3.0
 * @since 0.13.0 Class name changed: this class is now namespaced.
 * @since 1.0.0  This sniff has been moved from the `VIP` category to the `Security` category.
 *
 * @covers \WordPressCS\WordPress\Helpers\ArrayWalkingFunctionsHelper
 * @covers \WordPressCS\WordPress\Helpers\SanitizationHelperTrait
 * @covers \WordPressCS\WordPress\Helpers\UnslashingFunctionsHelper
 * @covers \WordPressCS\WordPress\Helpers\ValidationHelper
 * @covers \WordPressCS\WordPress\Helpers\VariableHelper
 * @covers \WordPressCS\WordPress\Sniffs\Security\ValidatedSanitizedInputSniff
 */
final class ValidatedSanitizedInputUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
	 */
	public function getErrorList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'ValidatedSanitizedInputUnitTest.1.inc':
				return array(
					5   => 3,
					7   => 1,
					10  => 1,
					20  => 1,
					33  => 3,
					65  => 1,
					76  => 2, // Old-style WPCS ignore comments are no longer supported.
					79  => 1,
					80  => 1,
					81  => 1,
					82  => 1,
					85  => 1,
					90  => 1,
					93  => 1,
					96  => 1,
					100 => 2,
					101 => 1,
					104 => 2,
					105 => 1,
					114 => 2,
					121 => 1,
					132 => 1,
					137 => 1,
					138 => 1,
					150 => 2,
					160 => 2,
					164 => 2,
					189 => 1,
					202 => 1,
					206 => 1,
					210 => 1,
					216 => 1,
					217 => 1,
					238 => 1,
					242 => 1,
					245 => 1,
					251 => 1,
					257 => 1,
					266 => 1,
					277 => 1,
					290 => 2,
					300 => 1,
					305 => 2,
					306 => 2,
					307 => 2,
					309 => 2,
					310 => 2,
					311 => 2,
					315 => 2,
					317 => 1,
					323 => 1,
					338 => 1,
					342 => 3,
					345 => 3,
					354 => 1,
					366 => 1,
					372 => 1,
					378 => 1,
					384 => 1,
					387 => 1,
					397 => 1,
					405 => 1,
					413 => 1,
					434 => 1,
					449 => 1,
					450 => 1,
					455 => 1,
					456 => 1,
					457 => 1,
					474 => 1,
					475 => 1,
					476 => 1,
					481 => 2,
				);

			case 'ValidatedSanitizedInputUnitTest.2.inc':
			case 'ValidatedSanitizedInputUnitTest.3.inc':
			case 'ValidatedSanitizedInputUnitTest.4.inc':
			case 'ValidatedSanitizedInputUnitTest.5.inc':
				return array(
					7 => 3,
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
