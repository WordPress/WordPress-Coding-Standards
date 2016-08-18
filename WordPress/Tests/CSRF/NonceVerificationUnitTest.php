<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the NonceVerification sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.5.0
 */
class WordPress_Tests_CSRF_NonceVerificationUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {

		return array(
			5 => 1,
			9 => 1,
			31 => 1,
			44 => 1,
			48 => 1,
			69 => 1,
			89 => 1,
			113 => 1,
			114 => 1,
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
