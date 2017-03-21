<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the EnqueuedResources sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.3.0
 */
class WordPress_Tests_WP_EnqueuedResourcesUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			1 => 1,
			2 => ( PHP_VERSION_ID >= 50300 ) ? 1 : 0, // PHPCS on PHP 5.2 has a bug tokenizing inline HTML / `<s`.
			6 => 1,
			7 => ( PHP_VERSION_ID >= 50300 ) ? 1 : 0, // PHPCS on PHP 5.2 has a bug tokenizing inline HTML / `<s`.
			10 => 1,
			11 => 1,
			13 => 1,
			14 => 1,
			16 => 1,
			17 => 1,
			20 => 1,
			21 => 1,
			25 => ( PHP_VERSION_ID >= 50300 ) ? 1 : 0, // PHPCS on PHP 5.2 does not recognize double quoted T_HEREDOC.
			26 => ( PHP_VERSION_ID >= 50300 ) ? 1 : 0, // PHPCS on PHP 5.2 does not recognize double quoted T_HEREDOC.
			30 => 1, // PHPCS on PHP 5.2 does not recognize T_NOWDOC, but sees this as a literal string anyway.
			31 => ( PHP_VERSION_ID >= 50300 ) ? 1 : 0, // PHPCS on PHP 5.2 does not recognize T_NOWDOC.
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
