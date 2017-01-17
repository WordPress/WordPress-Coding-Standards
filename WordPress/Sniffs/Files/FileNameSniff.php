<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Ensures filenames do not contain underscores.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#naming-conventions
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.1.0
 * @since   0.11.0 - This sniff will now also check for all lowercase file names.
 *                 - This sniff will now also verify that files containing a class start with `class-`.
 */
class WordPress_Sniffs_Files_FileNameSniff implements PHP_CodeSniffer_Sniff {

	/**
	/**
	 * Historical exceptions in WP core to the class name rule.
	 *
	 * @since 0.11.0
	 *
	 * @var array
	 */
	private $class_exceptions = array(
		'class.wp-dependencies.php' => true,
		'class.wp-scripts.php'      => true,
		'class.wp-styles.php'       => true,
	);

	/**
	 * Unit test version of the historical exceptions in WP core.
	 *
	 * @since 0.11.0
	 *
	 * @var array
	 */
	private $unittest_class_exceptions = array(
		'class.wp-dependencies.inc' => true,
		'class.wp-scripts.inc'      => true,
		'class.wp-styles.inc'       => true,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		if ( defined( 'PHP_CODESNIFFER_IN_TESTS' ) ) {
			$this->class_exceptions = array_merge( $this->class_exceptions, $this->unittest_class_exceptions );
		}

		return array( T_OPEN_TAG );
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token in the
	 *                                        stack passed in $tokens.
	 *
	 * @return int
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

		$file     = $phpcsFile->getFileName();
		$fileName = basename( $file );
		$expected = strtolower( str_replace( '_', '-', $fileName ) );

		/*
		 * Generic check for lowercase hyphenated file names.
		 */
		if ( $fileName !== $expected ) {
			$phpcsFile->addError(
				'Filenames should be all lowercase with hyphens as word separators. Expected %s, but found %s.',
				0,
				'NotHyphenatedLowercase',
				array( $expected, $fileName )
			);
		}
		unset( $expected );

		/*
		 * Check files containing a class for the "class-" prefix.
		 */
		$has_class = $phpcsFile->findNext( T_CLASS, $stackPtr );
		if ( false !== $has_class ) {
			$class_name = $phpcsFile->getDeclarationName( $has_class );
			$expected   = 'class-' . strtolower( str_replace( '_', '-', $class_name ) );

			if ( substr( $fileName, 0, -4 ) !== $expected && ! isset( $this->class_exceptions[ $fileName ] ) ) {
				$phpcsFile->addError(
					'Class file names should be based on the class name with "class-" prepended. Expected %s, but found %s.',
					0,
					'InvalidClassFileName',
					array(
						$expected . '.php',
						$fileName,
					)
				);
			}
			unset( $expected );
		}
		// Only run this sniff once per file, no need to run it again.
		return ( $phpcsFile->numTokens + 1 );

	} // End process().

} // End class.
