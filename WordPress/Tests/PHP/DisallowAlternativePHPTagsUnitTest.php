<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Unit test class for the DisallowAlternativePHPTags sniff.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.10.0
 */
class WordPress_Tests_PHP_DisallowAlternativePHPTagsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Whether ASP tags are enabled or not.
	 *
	 * @var bool
	 */
	private $asp_tags = false;

	/**
	 * Get the ini values only once.
	 */
	protected function setUp() {
		parent::setUp();

		if ( version_compare( PHP_VERSION, '7.0.0alpha1', '<' ) ) {
			$this->asp_tags = (bool) ini_get( 'asp_tags' );
		}
	}

	/**
	 * Skip this test on HHVM.
	 *
	 * @return bool Whether to skip this test.
	 */
	protected function shouldSkipTest() {
		return defined( 'HHVM_VERSION' );
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		$errors = array(
			8 => 1,
			11 => 1,
			12 => 1,
			15 => 1,
		);

		if ( true === $this->asp_tags ) {
			$errors[4] = 1;
			$errors[5] = 1;
			$errors[6] = 1;
			$errors[7] = 1;
		}

		return $errors;
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		$warnings = array();

		if ( false === $this->asp_tags ) {
			$warnings = array(
				4 => 1,
				5 => 1,
				6 => 1,
				7 => 1,
			);
		}

		return $warnings;
	}

} // End class.
