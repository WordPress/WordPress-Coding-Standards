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
	 * Skip this test on PHP 5.2.
	 *
	 * @since 0.9.0
	 *
	 * @return bool Whether to skip this test.
	 */
	protected function shouldSkipTest() {
		return version_compare( PHP_VERSION, '5.3.0', '<' );
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			1 => 1,
			2 => 1,
			6 => 1,
			7 => 1,
			10 => 1,
			11 => 1,
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
