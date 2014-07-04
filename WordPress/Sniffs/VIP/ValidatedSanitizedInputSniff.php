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
	 * Check for validation functions for a variable within its own parenthesis only
	 * @var boolean
	 */
	public $check_validation_in_scope_only = false;

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

		// If we're overriding a superglobal with an assignment, no need to test
		$semicolon_position = $phpcsFile->findNext( array( T_SEMICOLON ), $stackPtr + 1, null, null, null, true );
		$assignment_position = $phpcsFile->findNext( array( T_EQUAL ), $stackPtr + 1, null, null, null, true );
		if ( $semicolon_position !== false && $assignment_position !== false && $assignment_position < $semicolon_position ) {
			return;
		}

		if ( ! isset( $instance['nested_parenthesis'] ) ) {
			$phpcsFile->addError( 'Detected usage of a non-sanitized input variable: %s', $stackPtr, null, array( $tokens[$stackPtr]['content'] ) );
			return;
		}

		$nested = $instance['nested_parenthesis'];

		// Ignore if wrapped inside ISSET
		end( $nested ); // Get closest parenthesis
		if ( in_array( $tokens[ key( $nested ) - 1 ]['code'], array( T_ISSET, T_EMPTY, T_UNSET ) ) )
			return;

		$varKey = $this->getArrayIndexKey( $phpcsFile, $tokens, $stackPtr );

		if ( empty( $varKey ) ) {
			return;
		}

		// Check for validation first
		$is_validated = false;

		// Validation check in inner scope ?
		if ( $this->check_validation_in_scope_only ) {
			// Wrapped in a condition? check existence of isset with the variable as an argument
			if ( ! empty( $tokens[$stackPtr]['conditions'] ) ) {
				$conditions = $tokens[$stackPtr]['conditions'];
				end( $conditions ); // Get closest condition
				$conditionPtr = key( $conditions );
				$condition = $tokens[$conditionPtr];

				if ( isset( $condition['parenthesis_opener'] ) ) {
					$scope_start = $condition['parenthesis_opener'];
					$scope_end   = $condition['parenthesis_closer'];
				}
			}
		} else {
			// Get outer scope
			$function = $phpcsFile->findPrevious( T_FUNCTION, $stackPtr );
			if ( $function !== false && $stackPtr < $tokens[$function]['scope_closer'] ) {
				$scope_start = $tokens[$function]['scope_opener'];
				$scope_end = $stackPtr;
			} else { // In the open air, check whole file
				$scope_start = 0;
				$scope_end = $stackPtr;
			}
		}

		for ( $i = $scope_start + 1; $i < $scope_end; $i++ ) {
			if ( ! in_array( $tokens[$i]['code'], array( T_ISSET, T_EMPTY, T_UNSET ) ) ) {
				continue;
			}
			$issetPtr = $i;
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
		} elseif ( T_UNSET === $function['code'] ) {
			$is_sanitized = true;
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
