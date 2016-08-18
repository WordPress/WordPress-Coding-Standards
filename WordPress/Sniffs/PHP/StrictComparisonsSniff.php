<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Enforces Strict Comparison checks, based upon Squiz code.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.4.0
 *
 * Last synced with base class ?[unknown date]? at commit ?[unknown commit]?.
 * It is currently unclear whether this sniff is actually based on Squiz code on whether the above
 * reference to it is a copy/paste oversight.
 * @link    Possibly: https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/Squiz/Sniffs/Operators/ComparisonOperatorUsageSniff.php
 */
class WordPress_Sniffs_PHP_StrictComparisonsSniff extends WordPress_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_IS_EQUAL,
			T_IS_NOT_EQUAL,
		);

	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token in the
	 *                                        stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$this->init( $phpcsFile );

		if ( ! $this->has_whitelist_comment( 'loose comparison', $stackPtr ) ) {
			$error  = 'Found: ' . $this->tokens[ $stackPtr ]['content'] . '. Use strict comparisons (=== or !==).';
			$phpcsFile->addWarning( $error, $stackPtr, 'LooseComparison' );
		}

	}

} // End class.
