<?php
/**
 * WordPress Coding Standard.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Flag calling in_array(), array_search() and array_keys() without true as the third parameter.
 *
 * @link     https://vip.wordpress.com/documentation/code-review-what-we-look-for/#using-in_array-without-strict-parameter
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 */
class WordPress_Sniffs_PHP_StrictInArraySniff extends WordPress_Sniff {

	/**
	 * List of array functions to which a $strict parameter can be passed.
	 *
	 * The $strict parameter is the third and last parameter for each of these functions.
	 *
	 * The array_keys() function only requires the $strict parameter when the optional
	 * second parameter $search has been set.
	 *
	 * @link http://php.net/in-array
	 * @link http://php.net/array-search
	 * @link http://php.net/array-keys
	 *
	 * @var array <string function_name> => <bool always needed ?>
	 */
	protected $array_functions = array(
		'in_array'     => true,
		'array_search' => true,
		'array_keys'   => false,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_STRING
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
		$token  = strtolower( $tokens[ $stackPtr ]['content'] );

		// Bail out if not one of the targetted functions.
		if ( ! isset( $this->array_functions[ $token ] ) ) {
			return;
		}

		if ( ! isset( $tokens[ ( $stackPtr - 1 ) ] ) ) {
			return;
		}

		$prev = $phpcsFile->findPrevious( PHP_CodeSniffer_Tokens::$emptyTokens, ( $stackPtr - 1 ), null, true );

		if ( false !== $prev ) {
			// Skip sniffing if calling a same-named method, or on function definitions.
			if ( in_array( $tokens[ $prev ]['code'], array( T_FUNCTION, T_DOUBLE_COLON, T_OBJECT_OPERATOR ), true ) ) {
				return;
			}

			// Skip namespaced functions, ie: \foo\bar() not \bar().
			$pprev = $phpcsFile->findPrevious( PHP_CodeSniffer_Tokens::$emptyTokens, ( $prev - 1 ), null, true );
			if ( false !== $pprev && T_NS_SEPARATOR === $tokens[ $prev ]['code'] && T_STRING === $tokens[ $pprev ]['code'] ) {
				return;
			}
		}
		unset( $prev, $pprev );

		// Get the closing parenthesis.
		$openParenthesis = $phpcsFile->findNext( T_OPEN_PARENTHESIS, ( $stackPtr + 1 ) );
		if ( false === $openParenthesis ) {
			return;
		}

		// Gracefully handle syntax error.
		if ( ! isset( $tokens[ $openParenthesis ]['parenthesis_closer'] ) ) {
			$phpcsFile->addError( 'Missing closing parenthesis for in_array().', $openParenthesis, 'MissingClosingParenthesis' );
			return;
		}

		// Get last token in the function call.
		$closeParenthesis = $tokens[ $openParenthesis ]['parenthesis_closer'];
		$lastToken        = $phpcsFile->findPrevious( PHP_CodeSniffer_Tokens::$emptyTokens, ( $closeParenthesis - 1 ), ( $openParenthesis + 1 ), true );

		// Check if the strict check is actually needed.
		if ( false === $this->array_functions[ $token ] ) {
			$hasComma = $phpcsFile->findPrevious( T_COMMA, ( $closeParenthesis - 1 ), ( $openParenthesis + 1 ) );
			if ( false === $hasComma || end( $tokens[ $hasComma ]['nested_parenthesis'] ) !== $closeParenthesis ) {
				return;
			}
		}

		$errorData = array( $token );

		if ( false === $lastToken ) {
			$phpcsFile->addError( 'Missing arguments to %s.', $openParenthesis, 'MissingArguments', $errorData );
			return;
		}

		if ( T_TRUE !== $tokens[ $lastToken ]['code'] ) {
			$phpcsFile->addWarning( 'Not using strict comparison for %s; supply true for third argument.', $lastToken, 'MissingTrueStrict', $errorData );
			return;
		}
	} // end process()

} // end class
