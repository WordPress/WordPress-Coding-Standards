<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Enforces Yoda conditional statements.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#yoda-conditions
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 */
class WordPress_Sniffs_PHP_YodaConditionsSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_IS_EQUAL,
			T_IS_NOT_EQUAL,
			T_IS_IDENTICAL,
			T_IS_NOT_IDENTICAL,
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
		$tokens = $phpcsFile->getTokens();

		$beginners = array_merge(
			PHP_CodeSniffer_Tokens::$booleanOperators
			, array( T_IF, T_ELSEIF )
		);

		$beginning = $phpcsFile->findPrevious( $beginners, $stackPtr, null, null, null, true );

		$needs_yoda = false;

		// Note: going backwards!
		for ( $i = $stackPtr; $i > $beginning; $i-- ) {

			// Ignore whitespace.
			if ( in_array( $tokens[ $i ]['code'], PHP_CodeSniffer_Tokens::$emptyTokens, true ) ) {
				continue;
			}

			// If this is a variable or array, we've seen all we need to see.
			if ( T_VARIABLE === $tokens[ $i ]['code'] || T_CLOSE_SQUARE_BRACKET === $tokens[ $i ]['code'] ) {
				$needs_yoda = true;
				break;
			}

			// If this is a function call or something, we are OK.
			if ( in_array( $tokens[ $i ]['code'], array( T_CONSTANT_ENCAPSED_STRING, T_CLOSE_PARENTHESIS, T_OPEN_PARENTHESIS, T_RETURN ), true ) ) {
				return;
			}
		}

		if ( ! $needs_yoda ) {
			return;
		}

		// Check if this is a var to var comparison, e.g.: if ( $var1 == $var2 ).
		$next_non_empty = $phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );

		if ( in_array( $tokens[ $next_non_empty ]['code'], PHP_CodeSniffer_Tokens::$castTokens, true ) ) {
			$next_non_empty = $phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, ( $next_non_empty + 1 ), null, true );
		}

		if ( in_array( $tokens[ $next_non_empty ]['code'], array( T_SELF, T_PARENT, T_STATIC ), true ) ) {
			$next_non_empty = $phpcsFile->findNext(
				array_merge( PHP_CodeSniffer_Tokens::$emptyTokens, array( T_DOUBLE_COLON ) )
				, ( $next_non_empty + 1 )
				, null
				, true
			);
		}

		if ( T_VARIABLE === $tokens[ $next_non_empty ]['code'] ) {
			return;
		}

		$phpcsFile->addError( 'Use Yoda Condition checks, you must', $stackPtr, 'NotYoda' );

	} // end process()

} // End class.
