<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the ControlStructureSpacing sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   2013-06-11
 */
class WordPress_Tests_WhiteSpace_ControlStructureSpacingUnitTest extends AbstractSniffUnitTest {

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
		$ret = array(
			4  => 2,
			17 => 2,
			29 => 5,
			37 => 1,
			41 => 1,
			42 => 1,
			49 => 5,
			58 => 3,
			67 => 1,
			68 => 1,
			69 => 1,
			71 => 1,
			72 => 1,
			81 => 3,
			82 => 1,
			85 => 1,
			91 => 2,
			92 => 1,
			94 => 1,
			95 => 1,
			97 => 1,
			98 => 1,
		);

		/*
		Uncomment when "$blank_line_check" parameter will be "true" by default.

		$ret[29] += 1;
		$ret[33]  = 1;
		$ret[36]  = 1;
		$ret[38]  = 1;
		 */

		return $ret;

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
