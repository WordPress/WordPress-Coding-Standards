<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @category PHP
 * @package  PHP\CodeSniffer\WordPress-Coding-Standards
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Unit test class for the DisallowAlternativePHPTags sniff.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category PHP
 * @package  PHP\CodeSniffer\WordPress-Coding-Standards
 * @author   Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
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
	 * The key of the array should represent the line number and the value
	 * should represent the number of errors that should occur on that line.
	 *
	 * @return array<int, int>
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
	 * The key of the array should represent the line number and the value
	 * should represent the number of warnings that should occur on that line.
	 *
	 * @return array<int, int>
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
