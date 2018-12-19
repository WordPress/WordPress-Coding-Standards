<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Files;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the FileName sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   2013-06-11
 * @since   0.11.0     Actually added tests ;-)
 * @since   0.13.0     Class name changed: this class is now namespaced.
 */
class FileNameUnitTest extends AbstractSniffUnitTest {

	/**
	 * Error files with the expected nr of errors.
	 *
	 * @var array
	 */
	private $expected_results = array(

		/*
		 * In /FileNameUnitTests.
		 */

		// File names generic.
		'some_file.inc'                              => 1,
		'SomeFile.inc'                               => 1,
		'some-File.inc'                              => 1,
		'SomeView.inc'                               => 1,

		// Class file names.
		'my-class.inc'                               => 1,
		'class-different-class.inc'                  => 1,
		'ClassMyClass.inc'                           => 2,

		// Theme specific exceptions in a non-theme context.
		'single-my_post_type.inc'                    => 1,
		'taxonomy-post_format-post-format-audio.inc' => 1,

		/*
		 * In /FileNameUnitTests/NonStrictClassNames.
		 */

		// Non-strict class names still have to comply with lowercase hyphenated.
		'ClassNonStrictClass.inc'                    => 1,

		/*
		 * In /FileNameUnitTests/PHPCSAnnotations.
		 */

		// Non-strict class names still have to comply with lowercase hyphenated.
		'blanket-disable.inc'                        => 0,
		'non-relevant-disable.inc'                   => 1,
		'partial-file-disable.inc'                   => 1,
		'rule-disable.inc'                           => 0,
		'wordpress-disable.inc'                      => 0,

		/*
		 * In /FileNameUnitTests/TestFiles.
		 */
		'test-sample-phpunit.inc'                    => 0,
		'test-sample-phpunit6.inc'                   => 0,
		'test-sample-wpunit.inc'                     => 0,
		'test-sample-custom-unit.inc'                => 0,
		'test-sample-namespaced-declaration.1.inc'   => 0,
		'test-sample-namespaced-declaration.2.inc'   => 1, // Namespaced vs non-namespaced.
		'test-sample-namespaced-declaration.3.inc'   => 1, // Wrong namespace.
		'test-sample-namespaced-declaration.4.inc'   => 1, // Non-namespaced vs namespaced.
		'test-sample-global-namespace-extends.1.inc' => 0, // Prefixed user input.
		'test-sample-global-namespace-extends.2.inc' => 1, // Non-namespaced vs namespaced.
		'test-sample-extends-with-use.inc'           => 0,
		'test-sample-namespaced-extends.1.inc'       => 0,
		'test-sample-namespaced-extends.2.inc'       => 1, // Wrong namespace.
		'test-sample-namespaced-extends.3.inc'       => 1, // Namespaced vs non-namespaced.
		'test-sample-namespaced-extends.4.inc'       => 1, // Non-namespaced vs namespaced.
		'test-sample-namespaced-extends.5.inc'       => 0,

		/*
		 * In /FileNameUnitTests/ThemeExceptions.
		 */

		// Files in a theme context.
		'front_page.inc'                             => 1,
		'FrontPage.inc'                              => 1,
		'author-nice_name.inc'                       => 1,

		/*
		 * In /FileNameUnitTests/wp-includes.
		 */

		// Files containing template tags.
		'general.inc'                                => 1,

		/*
		 * In /.
		 */

		// Fall-back file in case glob() fails.
		'FileNameUnitTest.inc'                       => 1,
	);

	/**
	 * Get a list of all test files to check.
	 *
	 * @param string $testFileBase The base path that the unit tests files will have.
	 *
	 * @return string[]
	 */
	protected function getTestFiles( $testFileBase ) {

		$sep        = \DIRECTORY_SEPARATOR;
		$test_files = glob( dirname( $testFileBase ) . $sep . 'FileNameUnitTests{' . $sep . ',' . $sep . '*' . $sep . '}*.inc', \GLOB_BRACE );

		if ( ! empty( $test_files ) ) {
			return $test_files;
		}

		return array( $testFileBase . '.inc' );
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList( $testFile = '' ) {

		if ( isset( $this->expected_results[ $testFile ] ) ) {
			return array(
				1 => $this->expected_results[ $testFile ],
			);
		}

		return array();
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array();
	}

}
