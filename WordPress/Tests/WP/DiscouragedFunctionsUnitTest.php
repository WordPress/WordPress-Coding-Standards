<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\WP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the WP_DiscouragedFunctions sniff.
 *
 * @since 0.11.0
 * @since 0.13.0 Class name changed: this class is now namespaced.
 *
 * @covers \WordPressCS\WordPress\AbstractFunctionRestrictionsSniff
 * @covers \WordPressCS\WordPress\Helpers\ContextHelper::has_object_operator_before
 * @covers \WordPressCS\WordPress\Helpers\ContextHelper::is_token_namespaced
 * @covers \WordPressCS\WordPress\Sniffs\WP\DiscouragedFunctionsSniff
 */
final class DiscouragedFunctionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
	 */
	public function getErrorList() {
		return array();
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected warnings.
	 */
	public function getWarningList() {
		return array(
			3  => 1,
			4  => 1,
			20 => 1,
			33 => 1,
			34 => 1,
			53 => 1,
			62 => 1,
			65 => 1,
		);
	}
}
