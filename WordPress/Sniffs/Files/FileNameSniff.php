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
 *                 - This sniff will now also verify that files in `wp-includes` containing
 *                   template tags end in `-template`. Based on @subpackage file DocBlock tag.
 *                 - This sniff will now allow for underscores in file names for certain theme
 *                   specific exceptions if the `$is_theme` property is set to `true`.
 */
class WordPress_Sniffs_Files_FileNameSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Regex for the theme specific exceptions.
	 *
	 * N.B. This regex currently does not allow for mimetype sublevel only file names,
	 * such as `plain.php`.
	 *
	 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
	 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#custom-taxonomies
	 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#custom-post-types
	 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#embeds
	 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#attachment
	 * @link https://en.wikipedia.org/wiki/Media_type#Naming
	 *
	 * @since 0.11.0
	 *
	 * @var string
	 */
	const THEME_EXCEPTIONS_REGEX = '`
		^                    # Anchor to the beginning of the string.
		(?:
			(?:archive|embed|single|taxonomy) # Template prefixes which can have exceptions
			-[^\.]+          # These need to be followed by a dash and some chars.
		|
			(?:application|audio|example|image|message|model|multipart|text|video) #Top-level mime-types
			(?:_[^\.]+)?     # Optionally followed by an underscore and a sub-type.
		)\.(?:php|inc)$      # End in .php (or .inc for the test files) and anchor to the end of the string.
	`Dx';

	/**
	 * Whether the codebase being sniffed is a theme.
	 *
	 * If true, it will allow for certain typical theme specific exceptions to the filename rules.
	 *
	 * @since 0.11.0
	 *
	 * @var bool
	 */
	public $is_theme = false;

	/**
	 * Whether to apply strict class file name rules.
	 *
	 * If true, it demands that classes are prefixed with `class-` and that the rest of the
	 * file name reflects the class name.
	 *
	 * @since 0.11.0
	 *
	 * @var bool
	 */
	public $strict_class_file_names = true;

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
		if ( $fileName !== $expected && ( false === $this->is_theme || 1 !== preg_match( self::THEME_EXCEPTIONS_REGEX, $fileName ) ) ) {
			$phpcsFile->addError(
				'Filenames should be all lowercase with hyphens as word separators. Expected %s, but found %s.',
				0,
				'NotHyphenatedLowercase',
				array( $expected, $fileName )
			);
		}
		unset( $expected );

		/*
		 * Check files containing a class for the "class-" prefix and that the rest of
		 * the file name reflects the class name.
		 */
		if ( true === $this->strict_class_file_names ) {
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
		}

		/*
		 * Check non-class files in "wp-includes" with a "@subpackage Template" tag for a "-template" suffix.
		 */
		if ( false !== strpos( $file, DIRECTORY_SEPARATOR . 'wp-includes' . DIRECTORY_SEPARATOR ) ) {
			$subpackage_tag = $phpcsFile->findNext( T_DOC_COMMENT_TAG, $stackPtr, null, false, '@subpackage' );
			if ( false !== $subpackage_tag ) {
				$subpackage = $phpcsFile->findNext( T_DOC_COMMENT_STRING, $subpackage_tag );
				if ( false !== $subpackage ) {
					$tokens       = $phpcsFile->getTokens();
					$fileName_end = substr( $fileName, -13 );
					$has_class    = $phpcsFile->findNext( T_CLASS, $stackPtr );

					if ( ( 'Template' === trim( $tokens[ $subpackage ]['content'] )
						&& $tokens[ $subpackage_tag ]['line'] === $tokens[ $subpackage ]['line'] )
						&& ( ( ! defined( 'PHP_CODESNIFFER_IN_TESTS' ) && '-template.php' !== $fileName_end )
						|| ( defined( 'PHP_CODESNIFFER_IN_TESTS' ) && '-template.inc' !== $fileName_end ) )
						&& false === $has_class
					) {
						$phpcsFile->addError(
							'Files containing template tags should have "-template" appended to the end of the file name. Expected %s, but found %s.',
							0,
							'InvalidTemplateTagFileName',
							array(
								substr( $fileName, 0, -4 ) . '-template.php',
								$fileName,
							)
						);
					}
				}
			}
		} // End if().

		// Only run this sniff once per file, no need to run it again.
		return ( $phpcsFile->numTokens + 1 );

	} // End process().

} // End class.
