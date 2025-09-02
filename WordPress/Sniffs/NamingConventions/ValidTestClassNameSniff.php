<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPress
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Ensures test classes are named correctly according to the test type.
 *
 * @package WPCS\WordPress
 * @since   3.0.0
 */
class ValidTestClassNameSniff implements Sniff {

	/**
	 * The file extension of test files.
	 *
	 * @var string
	 */
	const TEST_FILE_EXT = 'php';

	/**
	 * Test class prefix.
	 *
	 * @var string
	 */
	const TEST_CLASS_PREFIX = 'Test';

	/**
	 * Test class suffix.
	 *
	 * @var string
	 */
	const TEST_CLASS_SUFFIX = 'Test';

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array( T_CLASS );
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The position of the current token in the
	 *                                               stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$className = $phpcsFile->getDeclarationName( $stackPtr );

		// Only check test classes.
		if ( ! $this->isTestClass( $phpcsFile, $stackPtr ) ) {
			return;
		}

		// Check if the class name follows the naming convention.
		$is_valid = true;
		$error    = '';

		if ( ! preg_match( '/^[A-Z][a-zA-Z0-9_]*' . preg_quote( self::TEST_CLASS_SUFFIX, '/' ) . '$/', $className ) ) {
			$is_valid = false;
			$error    = 'Test class names must be in PascalCase and end with "%s". Found: %s';
		}

		if ( ! $is_valid ) {
			$phpcsFile->addError(
				$error,
				$stackPtr,
				'InvalidTestClassName',
				array(
					self::TEST_CLASS_SUFFIX,
					$className,
				)
			);
		}

		// Check if the filename matches the class name.
		$filename = basename( $phpcsFile->getFilename(), '.' . self::TEST_FILE_EXT );
		if ( $filename !== $className ) {
			$phpcsFile->addError(
				'Test class name must match the filename. Expected: %s, found: %s',
				$stackPtr,
				'TestFileNameMismatch',
				array(
					$filename . self::TEST_CLASS_SUFFIX,
					$className,
				)
			);
		}
	}

	/**
	 * Check if the current class is a test class.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The position of the current token in the
	 *                                               stack passed in $tokens.
	 *
	 * @return bool True if it's a test class, false otherwise.
	 */
	private function isTestClass( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		// Only check files that end with Test.php
		$filename = basename( $phpcsFile->getFilename() );
		if ( substr( $filename, -8 ) !== 'Test.php' ) {
			return false;
		}

		// Check if the class extends a test case class.
		$extends = $phpcsFile->findExtendedClassName( $stackPtr );
		if ( $extends === false ) {
			return false;
		}

		// Check for common PHPUnit test case class names.
		$testCaseClasses = array(
			'PHPUnit_Framework_TestCase',
			'PHPUnit\\Framework\\TestCase',
			'WP_UnitTestCase',
			'\\PHPUnit_Framework_TestCase', // With leading backslash
			'\\PHPUnit\\Framework\\TestCase', // With leading backslash
			'\\WP_UnitTestCase', // With leading backslash
		);
		
		// Check both with and without leading backslash
		$normalizedExtends = ltrim( $extends, '\\' );
		return in_array( $normalizedExtends, $testCaseClasses, true ) || 
			   in_array( $extends, $testCaseClasses, true );
	}
}
