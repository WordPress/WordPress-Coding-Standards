<?php
/**
 * WordPress Coding Standard.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Forbids usage of deprecated WP CONSTANTS and recommends alternatives.
 *
 * @link     https://make.wordpress.org/themes/handbook/review/required/#core-functionality-and-features
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
 */
class WordPress_Sniffs_Theme_DeprecatedWPConstantsSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * List of deprecated WP constants and their replacements.
	 *
	 * @var array
	 */
	private $deprecated_constants = array(
		'STYLESHEETPATH'      => 'get_stylesheet_directory()',
		'TEMPLATEPATH'        => 'get_template_directory()',
		'PLUGINDIR'           => 'WP_PLUGIN_DIR',
		'MUPLUGINDIR'         => 'WPMU_PLUGIN_DIR',
		'HEADER_IMAGE'        => 'add_theme_support( \'custom-header\' )',
		'NO_HEADER_TEXT'      => 'add_theme_support( \'custom-header\' )',
		'HEADER_TEXTCOLOR'    => 'add_theme_support( \'custom-header\' )',
		'HEADER_IMAGE_WIDTH'  => 'add_theme_support( \'custom-header\' )',
		'HEADER_IMAGE_HEIGHT' => 'add_theme_support( \'custom-header\' )',
		'BACKGROUND_COLOR'    => 'add_theme_support( \'custom-background\' )',
		'BACKGROUND_IMAGE'    => 'add_theme_support( \'custom-background\' )',
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_STRING,
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$token  = $tokens[ $stackPtr ];

		if ( ! isset( $this->deprecated_constants[ $tokens[ $stackPtr ]['content'] ] ) ) {
			return;
		}

		$prev = $phpcsFile->findPrevious( T_WHITESPACE, ( $stackPtr - 1 ), null, true );

		if ( T_DOUBLE_COLON === $tokens[ $prev ]['code'] ) {
			// Class constant of the same name.
			return;
		}

		if ( T_NS_SEPARATOR === $tokens[ $prev ]['code'] && T_STRING === $tokens[ ( $prev - 1 ) ]['code'] ) {
			// Namespaced constant of the same name.
			return;
		}

		// Ok, this is really one of the deprecated constants.
		$error = 'Found usage of constant "%s". Use %s instead.';
		$data  = array( $tokens[ $stackPtr ]['content'], $this->deprecated_constants[ $tokens[ $stackPtr ]['content'] ] );
		$phpcsFile->addError( $error, $stackPtr, 'Found', $data );

	} // end process()

} // end class
