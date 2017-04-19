<?php
/**
 * WordPress Coding Standard.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * WordPress_Sniffs_Theme_FileIncludeSniff.
 *
 * WARNING (manual check required) | Check if a theme uses include(_once) or
 * require(_once) (where they should use get_template_part()). Current implementation
 * excluded the functions.php file from this check. We may want to continue doing so.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    khacoder
 */
class WordPress_Sniffs_Theme_FileIncludeSniff extends WordPress_AbstractThemeSniff {

	/**
	 * A list of tokenizers this sniff supports.
	 *
	 * @var array
	 */
	public $supportedTokenizers = array(
		'PHP',
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_REQUIRE,
			T_REQUIRE_ONCE,
			T_INCLUDE,
			T_INCLUDE_ONCE,
		);
	}//end register()

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

		$fileName = basename( $phpcsFile->getFileName() );

		$checks = array(
			'include"',
			'include_once',
			'require',
			'require_once',
		);

		if ( 'functions.php' !== $fileName ) {
			foreach ( $checks as $check ) {
				if ( false !== strpos( $token['content'], $check ) ) {
					$phpcsFile->addWarning( 'The theme appears to use include or require. If these are being used to include separate sections of a template from independent files, then <strong>get_template_part()</strong> should be used instead.' , $stackPtr, 'FileIncludeCheck' );
				}
			}
		}
	}//end process()
}
