<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\Files;

use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;
use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\TestUtils\ConfigDouble;

/**
 * Unit test class for the FileName sniff.
 *
 * @since 2013-06-11
 * @since 0.11.0     Actually added tests ;-)
 * @since 0.13.0     Class name changed: this class is now namespaced.
 *
 * @covers \WordPressCS\WordPress\Sniffs\Files\FileNameSniff
 */
final class FileNameUnitTest extends AbstractSniffUnitTest {

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
		'class.with.dot.not.underscore.inc'          => 2,
		'class@with#other+punctuation.inc'           => 2,
		'class-wrong-with-different-extension.php3'  => 1,

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
		'disable-non-matching-enable.inc'            => 0,

		/*
		 * In /FileNameUnitTests/TestFiles.
		 */
		'test-sample-phpunit.inc'                    => 0,
		'test-sample-phpunit6.inc'                   => 0,
		'test-sample-phpunit6-case-insensitive.inc'  => 0,
		'test-sample-wpunit.inc'                     => 0,
		'test-sample-custom-unit-1.inc'              => 0,
		'test-sample-custom-unit-2.inc'              => 0,
		'test-sample-custom-unit-3.inc'              => 0,
		'test-sample-custom-unit-4.inc'              => 0,
		'test-sample-custom-unit-5.inc'              => 1, // Namespaced vs non-namespaced.
		'test-sample-namespaced-declaration-1.inc'   => 0,
		'test-sample-namespaced-declaration-2.inc'   => 1, // Namespaced vs non-namespaced.
		'test-sample-namespaced-declaration-3.inc'   => 1, // Wrong namespace.
		'test-sample-namespaced-declaration-4.inc'   => 1, // Non-namespaced vs namespaced.
		'test-sample-global-namespace-extends-1.inc' => 0, // Prefixed user input.
		'test-sample-global-namespace-extends-2.inc' => 1, // Non-namespaced vs namespaced.
		'test-sample-extends-with-use.inc'           => 0,
		'test-sample-namespaced-extends-1.inc'       => 0,
		'test-sample-namespaced-extends-2.inc'       => 1, // Wrong namespace.
		'test-sample-namespaced-extends-3.inc'       => 1, // Namespaced vs non-namespaced.
		'test-sample-namespaced-extends-4.inc'       => 1, // Non-namespaced vs namespaced.
		'test-sample-namespaced-extends-5.inc'       => 0,
		'Test_Sample.inc'                            => 0,

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

		$php3_test_files = glob( dirname( $testFileBase ) . $sep . 'FileNameUnitTests{' . $sep . ',' . $sep . '*' . $sep . '}*.php3', \GLOB_BRACE );
		if ( is_array( $php3_test_files ) ) {
			foreach ( $php3_test_files as $file ) {
				$test_files[] = $file;
			}
		}

		if ( ! empty( $test_files ) ) {
			return $test_files;
		}

		return array( $testFileBase . '.inc' );
	}

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
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
	 * @return array<int, int> Key is the line number, value is the number of expected warnings.
	 */
	public function getWarningList() {
		return array();
	}

	/**
	 * Test the sniff bails early when handling STDIN.
	 *
	 * @return void
	 */
	public function testStdIn() {
		$config = new ConfigDouble();
		Helper::setConfigData( 'installed_paths', dirname( dirname( __DIR__ ) ), true, $config );
		$config->standards = array( 'WordPress' );
		$config->sniffs    = array( 'WordPress.Files.FileName' );

		$ruleset = new Ruleset( $config );

		$content = '<?php ';
		$file    = new DummyFile( $content, $ruleset, $config );
		$file->process();

		$this->assertSame( 0, $file->getErrorCount() );
		$this->assertSame( 0, $file->getWarningCount() );
		$this->assertCount( 0, $file->getErrors() );
	}
}
