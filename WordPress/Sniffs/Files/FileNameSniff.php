<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\Files;

use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\ObjectDeclarations;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\Helpers\IsUnitTestTrait;
use WordPressCS\WordPress\Sniff;

/**
 * Ensures filenames do not contain underscores and where applicable are prefixed with `class-`.
 *
 * @link https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/#naming-conventions
 *
 * @since 0.1.0
 * @since 0.11.0 - This sniff will now also check for all lowercase file names.
 *               - This sniff will now also verify that files containing a class start with `class-`.
 *               - This sniff will now also verify that files in `wp-includes` containing
 *                 template tags end in `-template`. Based on @subpackage file DocBlock tag.
 *               - This sniff will now allow for underscores in file names for certain theme
 *                 specific exceptions if the `$is_theme` property is set to `true`.
 * @since 0.12.0 Now extends the WordPressCS native `Sniff` class.
 * @since 0.13.0 Class name changed: this class is now namespaced.
 * @since 3.0.0  Test class files are now completely exempt from this rule.
 *
 * @uses \WordPressCS\WordPress\Helpers\IsUnitTestTrait::$custom_test_classes
 */
final class FileNameSniff extends Sniff {

	use IsUnitTestTrait;

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
	 * @link https://developer.wordpress.org/themes/template-files-section/partial-and-miscellaneous-template-files/#content-slug-php
	 * @link https://wphierarchy.com/
	 * @link https://en.wikipedia.org/wiki/Media_type#Naming
	 *
	 * @since 0.11.0
	 *
	 * @var string
	 */
	const THEME_EXCEPTIONS_REGEX = '`
		^                    # Anchor to the beginning of the string.
		(?:
							 # Template prefixes which can have exceptions.
			(?:archive|category|content|embed|page|single|tag|taxonomy)
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
	 * Note: these files were renamed to comply with the naming conventions in
	 * WP 6.1.0.
	 * This means we no longer need to make an exception for them in the
	 * `check_filename_has_class_prefix()` check, however, we do still need to
	 * make an exception in the `check_filename_is_hyphenated()` check.
	 *
	 * @since 0.11.0
	 * @since 3.0.0  Property has been renamed from `$class_exceptions` to `$hyphenation_exceptions`,
	 *
	 * @var array
	 */
	private $hyphenation_exceptions = array(
		'class.wp-dependencies.php' => true,
		'class.wp-scripts.php'      => true,
		'class.wp-styles.php'       => true,
		'functions.wp-scripts.php'  => true,
		'functions.wp-styles.php'   => true,
	);

	/**
	 * Unit test version of the historical exceptions in WP core.
	 *
	 * @since 0.11.0
	 * @since 3.0.0  Property has been renamed from `$unittest_class_exceptions` to `$unittest_hyphenation_exceptions`,
	 *
	 * @var array
	 */
	private $unittest_hyphenation_exceptions = array(
		'class.wp-dependencies.inc' => true,
		'class.wp-scripts.inc'      => true,
		'class.wp-styles.inc'       => true,
		'functions.wp-scripts.inc'  => true,
		'functions.wp-styles.inc'   => true,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		if ( \defined( '\PHP_CODESNIFFER_IN_TESTS' ) ) {
			$this->hyphenation_exceptions += $this->unittest_hyphenation_exceptions;
		}

		return Collections::phpOpenTags();
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {
		// Usage of `stripQuotes` is to ensure `stdin_path` passed by IDEs does not include quotes.
		$file = TextStrings::stripQuotes( $this->phpcsFile->getFileName() );
		if ( 'STDIN' === $file ) {
			return;
		}

		$class_ptr = $this->phpcsFile->findNext( \T_CLASS, $stackPtr );
		if ( false !== $class_ptr && $this->is_test_class( $this->phpcsFile, $class_ptr ) ) {
			/*
			 * This rule should not be applied to test classes (at all).
			 * @link https://github.com/WordPress/WordPress-Coding-Standards/issues/1995
			 */
			return;
		}

		// Respect phpcs:disable comments as long as they are not accompanied by an enable.
		$i = -1;
		while ( $i = $this->phpcsFile->findNext( \T_PHPCS_DISABLE, ( $i + 1 ) ) ) {
			if ( empty( $this->tokens[ $i ]['sniffCodes'] )
				|| isset( $this->tokens[ $i ]['sniffCodes']['WordPress'] )
				|| isset( $this->tokens[ $i ]['sniffCodes']['WordPress.Files'] )
				|| isset( $this->tokens[ $i ]['sniffCodes']['WordPress.Files.FileName'] )
			) {
				do {
					$i = $this->phpcsFile->findNext( \T_PHPCS_ENABLE, ( $i + 1 ) );
				} while ( false !== $i
					&& ! empty( $this->tokens[ $i ]['sniffCodes'] )
					&& ! isset( $this->tokens[ $i ]['sniffCodes']['WordPress'] )
					&& ! isset( $this->tokens[ $i ]['sniffCodes']['WordPress.Files'] )
					&& ! isset( $this->tokens[ $i ]['sniffCodes']['WordPress.Files.FileName'] ) );

				if ( false === $i ) {
					// The entire (rest of the) file is disabled.
					return;
				}
			}
		}

		$file_name = basename( $file );

		$this->check_filename_is_hyphenated( $file_name );

		if ( true === $this->strict_class_file_names && false !== $class_ptr ) {
			$this->check_filename_has_class_prefix( $class_ptr, $file_name );
		}

		if ( false !== strpos( $file, \DIRECTORY_SEPARATOR . 'wp-includes' . \DIRECTORY_SEPARATOR )
			&& false === $class_ptr
		) {
			$this->check_filename_for_template_suffix( $stackPtr, $file_name );
		}

		// Only run this sniff once per file, no need to run it again.
		return ( $this->phpcsFile->numTokens + 1 );
	}

	/**
	 * Generic check for lowercase hyphenated file names.
	 *
	 * @since 3.0.0
	 *
	 * @param string $file_name The name of the current file.
	 *
	 * @return void
	 */
	protected function check_filename_is_hyphenated( $file_name ) {
		$extension = strrchr( $file_name, '.' );
		$name      = substr( $file_name, 0, ( strlen( $file_name ) - strlen( $extension ) ) );

		$expected = strtolower( preg_replace( '`[[:punct:]]`', '-', $name ) ) . $extension;
		if ( $file_name === $expected
			|| isset( $this->hyphenation_exceptions[ $file_name ] )
		) {
			return;
		}

		if ( true === $this->is_theme && 1 === preg_match( self::THEME_EXCEPTIONS_REGEX, $file_name ) ) {
			return;
		}

		$this->phpcsFile->addError(
			'Filenames should be all lowercase with hyphens as word separators. Expected %s, but found %s.',
			0,
			'NotHyphenatedLowercase',
			array( $expected, $file_name )
		);
	}


	/**
	 * Check files containing a class for the "class-" prefix and that the rest of
	 * the file name reflects the class name.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $class_ptr Stack pointer to the first T_CLASS in the file.
	 * @param string $file_name The name of the current file.
	 *
	 * @return void
	 */
	protected function check_filename_has_class_prefix( $class_ptr, $file_name ) {
		$extension  = strrchr( $file_name, '.' );
		$class_name = ObjectDeclarations::getName( $this->phpcsFile, $class_ptr );
		$expected   = 'class-' . strtolower( str_replace( '_', '-', $class_name ) ) . $extension;

		if ( $file_name === $expected ) {
			return;
		}

		$this->phpcsFile->addError(
			'Class file names should be based on the class name with "class-" prepended. Expected %s, but found %s.',
			0,
			'InvalidClassFileName',
			array(
				$expected,
				$file_name,
			)
		);
	}

	/**
	 * Check non-class files in "wp-includes" with a "@subpackage Template" tag for a "-template" suffix.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $stackPtr  Stack pointer to the first PHP open tag in the file.
	 * @param string $file_name The name of the current file.
	 *
	 * @return void
	 */
	protected function check_filename_for_template_suffix( $stackPtr, $file_name ) {
		$subpackage_tag = $this->phpcsFile->findNext( \T_DOC_COMMENT_TAG, $stackPtr, null, false, '@subpackage' );
		if ( false === $subpackage_tag ) {
			return;
		}

		$subpackage = $this->phpcsFile->findNext( \T_DOC_COMMENT_STRING, $subpackage_tag );
		if ( false === $subpackage ) {
			return;
		}

		$fileName_end = substr( $file_name, -13 );

		if ( ( 'Template' === trim( $this->tokens[ $subpackage ]['content'] )
			&& $this->tokens[ $subpackage_tag ]['line'] === $this->tokens[ $subpackage ]['line'] )
			&& ( ( ! \defined( '\PHP_CODESNIFFER_IN_TESTS' ) && '-template.php' !== $fileName_end )
			|| ( \defined( '\PHP_CODESNIFFER_IN_TESTS' ) && '-template.inc' !== $fileName_end ) )
		) {
			$this->phpcsFile->addError(
				'Files containing template tags should have "-template" appended to the end of the file name. Expected %s, but found %s.',
				0,
				'InvalidTemplateTagFileName',
				array(
					substr( $file_name, 0, -4 ) . '-template.php',
					$file_name,
				)
			);
		}
	}
}
