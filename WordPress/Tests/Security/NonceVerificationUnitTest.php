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
 * Unit test class for the NonceVerification sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.5.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   1.0.0  This sniff has been moved from the `CSRF` category to the `Security` category.
 *
 * @covers \WordPressCS\WordPress\Helpers\ContextHelper::is_in_function_call
 * @covers \WordPressCS\WordPress\Helpers\ContextHelper::is_in_type_test
 * @covers \WordPressCS\WordPress\Helpers\ContextHelper::is_in_isset_or_empty
 * @covers \WordPressCS\WordPress\Helpers\ContextHelper::is_in_array_comparison
 * @covers \WordPressCS\WordPress\Sniffs\Security\NonceVerificationSniff
 */
final class NonceVerificationUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'NonceVerificationUnitTest.1.inc':
				return array(
					5   => 1,
					9   => 1,
					31  => 1,
					44  => 1,
					48  => 1,
					69  => 1,
					88  => 1, // Old-style WPCS ignore comments are no longer supported.
					89  => 1,
					113 => 1,
					114 => 1,
					122 => 1,
					126 => 1,
					148 => 1,
					150 => 1,
					159 => 1,
					160 => 1,
					161 => 1,
					177 => 1,
					185 => 1,
					190 => 1,
					198 => 1,
					202 => 1,
					252 => 1,
					269 => 1,
					306 => 1,
					312 => 1,
					335 => 1,
					339 => 1,
					347 => 1,
					351 => 1,
				);

			default:
				return array();
		}
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList( $testFile = '' ) {
		switch ( $testFile ) {
			case 'NonceVerificationUnitTest.1.inc':
				return array(
					375 => 1,
					389 => 1,
				);

			default:
				return array();
		}
	}
}
