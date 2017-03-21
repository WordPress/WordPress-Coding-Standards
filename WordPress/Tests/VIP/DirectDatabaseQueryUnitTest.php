<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the DirectDatabaseQuery sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.3.0
 */
class WordPress_Tests_VIP_DirectDatabaseQueryUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			6   => 1,
			8   => 1,
			32  => 1,
			34  => 1,
			50  => 1,
			78  => 1,
			79  => 1,
			80  => 1,
			170 => 1,
			178 => 1,
			190 => 1,
			250 => 1,
			257 => 1,
			274 => ( PHP_VERSION_ID >= 50300 ) ? 1 : 0, // PHPCS on PHP 5.2 does not recognize T_NOWDOC.
		);

	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			6   => 1,
			17  => 1,
			38  => 1,
			50  => 1,
			112 => 1,
			250 => 1,
		);

	}

} // End class.
