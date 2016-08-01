<?php
/**
 * WordPress Coding Standard.
 *
 * @package PHP\CodeSniffer\WordPress-Coding-Standards
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Discourages removal of the admin bar.
 *
 * @link    https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#removing-the-admin-bar
 *
 * @package PHP\CodeSniffer\WordPress-Coding-Standards
 * @author  Shady Sharaf <shady@x-team.com>
 *
 * @since   2014-12-11
 */
class WordPress_Sniffs_VIP_AdminBarRemovalSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_STRING,
			T_CONSTANT_ENCAPSED_STRING,
			T_DOUBLE_QUOTED_STRING,
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

		if ( in_array( trim( $tokens[ $stackPtr ]['content'], '"\'' ), array( 'show_admin_bar' ), true ) ) {
			$phpcsFile->addError( 'Removal of admin bar is prohibited.', $stackPtr, 'RemovalDetected' );
		}
	} // end process()

} // End class.
