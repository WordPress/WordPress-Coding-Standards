<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package  PHP\CodeSniffer\WordPress-Coding-Standards
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Unit test class for the CastStructureSpacing sniff.
 *
 * @package PHP\CodeSniffer\WordPress-Coding-Standards
 * @author  Matt Robinson
 * @since   2014-12-11
 */
class WordPress_Tests_WhiteSpace_CastStructureSpacingUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array();

	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			 3 => 1,
			 6 => 1,
			 9 => 1,
			 12 => 2,
			 15 => 1,
			 18 => 1,
			 21 => 1,
		);

	}

} // End class.
