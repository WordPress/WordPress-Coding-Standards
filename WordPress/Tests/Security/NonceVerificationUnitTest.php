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
 * @since 0.5.0
 * @since 0.13.0 Class name changed: this class is now namespaced.
 * @since 1.0.0  This sniff has been moved from the `CSRF` category to the `Security` category.
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
					138 => 1,
					140 => 1,
					149 => 1,
					150 => 1,
					151 => 1,
					167 => 1,
					175 => 1,
					180 => 1,
					188 => 1,
					192 => 1,
					242 => 1,
					259 => 1,
					296 => 1,
					302 => 1,
					325 => 1,
					329 => 1,
					337 => 1,
					341 => 1,
					402 => 1,
					405 => 1,
					414 => 1,
					428 => 1,
					438 => 1,
					448 => 1,
					453 => 1,
					470 => 1,
					478 => 1,
				);

			case 'NonceVerificationUnitTest.2.inc':
				return array(
					10 => 1,
					14 => 1,
				);

			case 'NonceVerificationUnitTest.7.inc':
				return array(
					17 => 1,
					23 => 1,
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
					365 => 1,
					379 => 1,
				);

			case 'NonceVerificationUnitTest.4.inc':
				return array(
					25 => 1,
				);

			case 'NonceVerificationUnitTest.6.inc':
				return array(
					8 => 1,
				);

			default:
				return array();
		}
	}
}
