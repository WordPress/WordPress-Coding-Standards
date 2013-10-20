<?php
/**
 * Flag any non-validated/sanitized input ( _GET / _POST / _REQUEST / _SERVER )
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 * @link     https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/69
 */
class WordPress_Sniffs_VIP_ValidatedSanitizedInputSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
				T_VARIABLE,
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
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr )
	{
		$tokens = $phpcsFile->getTokens();

		// Check for $wpdb variable
		if ( ! in_array( $tokens[$stackPtr]['content'], array( '$_GET', '$_POST', '$_REQUEST', '$_SERVER' ) ) )
			return;

		$instance = $tokens[$stackPtr];
		$varName = $instance['content'];

		$nested = $instance['nested_parenthesis'];

		// Ignore if wrapped inside ISSET
		end($nested); // Get closest parenthesis
		if ( T_ISSET === $tokens[ key( $nested ) - 1 ]['code'] )
			return;

		$varKey = $this->getArrayIndexKey( $phpcsFile, $tokens, $stackPtr );

		if ( empty( $varKey ) ) {
			$phpcsFile->addWarning( 'Detected access of super global var %s without targeting a member variable.', $stackPtr, null, array( $varName ) );
			return;
		}

		// Check for validation first
		$is_validated = false;
		
		// Wrapped in a condition? check existence of isset with the variable as an argument
		if ( ! empty( $tokens[$stackPtr]['conditions'] ) ) {
			$conditionPtr = key( $tokens[$stackPtr]['conditions'] );
			$condition = $tokens[$conditionPtr];

			$issetPtr = $phpcsFile->findNext( array( T_ISSET, T_EMPTY ), $condition['parenthesis_opener'], $condition['parenthesis_closer'] );
			if ( ! empty( $issetPtr ) ) {
				$isset = $tokens[$issetPtr];
				$issetOpener = $issetPtr + 1;
				$issetCloser = $tokens[$issetOpener]['parenthesis_closer'];

				// Check that it is the same variable name
				if ( $validated = $phpcsFile->findNext( array( T_VARIABLE ), $issetOpener, $issetCloser, null, $varName ) ) {
					// Double check the $varKey inside the variable, ex: 'hello' in $_POST['hello']
					
					$varKeyValidated = $this->getArrayIndexKey( $phpcsFile, $tokens, $validated );

					if ( $varKeyValidated == $varKey ) {
						// everything matches, variable IS validated afterall ..
						$is_validated = true;
					}
				}
			}
		}

		if ( ! $is_validated ) {
			$phpcsFile->addError( 'Detected usage of a non-validated input variable: %s', $stackPtr, null, array( $tokens[$stackPtr]['content'] ) );
			// return; // Should we just return and not look for sanitizing functions ?
		}

		// Now look for sanitizing functions
		$is_sanitized = false;

		$functionPtr = key( $nested ) - 1;
		$function = $tokens[$functionPtr];
		if ( T_STRING === $function['code'] ) {
			$functionName = $function['content'];
			if ( 
				in_array( $functionName, WordPress_Sniffs_XSS_EscapeOutputSniff::$autoEscapedFunctions )
				||
				in_array( $functionName, WordPress_Sniffs_XSS_EscapeOutputSniff::$sanitizingFunctions )
				) {
				$is_sanitized = true;
			}
		}

		if ( ! $is_sanitized ) {
			$phpcsFile->addError( 'Detected usage of a non-sanitized input variable: %s', $stackPtr, null, array( $tokens[$stackPtr]['content'] ) );
		}

		
		return;
	}//end process()

	/**
	 * Get array index key of the variable requested
	 * @param  [type] $phpcsFile [description]
	 * @param  [type] $tokens    [description]
	 * @param  [type] $stackPtr  [description]
	 * @return [type]            [description]
	 */
	public function getArrayIndexKey( $phpcsFile, $tokens, $stackPtr ) {
		// Find next bracket
		$bracketOpener = $phpcsFile->findNext( array( T_OPEN_SQUARE_BRACKET ), $stackPtr, $stackPtr + 3 );

		// If no brackets, exit with a warning, this is a non-typical usage of super globals
		if ( empty ( $bracketOpener ) ) {
			return false;
		}

		$bracketCloser = $tokens[$bracketOpener]['bracket_closer'];

		$varKey = trim( $phpcsFile->getTokensAsString( $bracketOpener + 1, $bracketCloser - $bracketOpener - 1 ) ); // aka 'hello' in $_POST['hello']

		return $varKey;
	}

}//end class
