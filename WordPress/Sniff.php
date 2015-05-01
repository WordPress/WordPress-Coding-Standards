<?php

/**
 * Represents a PHP_CodeSniffer sniff for sniffing WordPress coding standards.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 */

/**
 * Represents a PHP_CodeSniffer sniff for sniffing WordPress coding standards.
 *
 * Provides a bootstrap for the sniffs, to reduce code duplication.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @version   0.4.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
abstract class WordPress_Sniff implements PHP_CodeSniffer_Sniff {

	/**
	 * List of the functions which verify nonces.
	 *
	 * @since 0.5.0
	 *
	 * @var array
	 */
	public static $nonceVerificationFunctions = array(
		'wp_verify_nonce',
		'check_admin_referer',
		'check_ajax_referer',
	);

	/**
	 * The current file being sniffed.
	 *
	 * @since 0.4.0
	 *
	 * @var PHP_CodeSniffer_File
	 */
	protected $phpcsFile;

	/**
	 * The list of tokens in the current file being sniffed.
	 *
	 * @since 0.4.0
	 *
	 * @var array
	 */
	protected $tokens;

	/**
	 * A list of superglobals that incorporate user input.
	 *
	 * @since 0.4.0
	 *
	 * @var string[]
	 */
	protected static $input_superglobals = array( '$_COOKIE', '$_GET', '$_FILES', '$_POST', '$_REQUEST', '$_SERVER' );

	/**
	 * Initialize the class for the current process.
	 *
	 * This method must be called by child classes before using many of the methods
	 * below.
	 *
	 * @since 0.4.0
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file currently being processed.
	 */
	protected function init( PHP_CodeSniffer_File $phpcsFile ) {
		$this->phpcsFile = $phpcsFile;
		$this->tokens = $phpcsFile->getTokens();
	}

	/**
	 * Get the last pointer in a line.
	 *
	 * @since 0.4.0
	 *
	 * @param integer $stackPtr The position of the current token in the stack passed
	 *                          in $tokens.
	 *
	 * @return integer Position of the last pointer on that line.
	 */
	protected function get_last_ptr_on_line( $stackPtr ) {

		$tokens = $this->tokens;
		$currentLine = $tokens[ $stackPtr ]['line'];
		$nextPtr = $stackPtr + 1;

		while ( isset( $tokens[ $nextPtr ] ) && $tokens[ $nextPtr ]['line'] === $currentLine ) {
			$nextPtr++;
			// Do nothing, we just want the last token of the line.
		}

		// We've made it to the next line, back up one to the last in the previous line.
		// We do this for micro-optimization of the above loop.
		$lastPtr = $nextPtr - 1;

		return $lastPtr;
	}

	/**
	 * Find whitelisting comment.
	 *
	 * Comment must be at the end of the line, and use // format.
	 * It can be prefixed or suffixed with anything e.g. "foobar" will match:
	 * ... // foobar okay
	 * ... // WPCS: foobar whitelist.
	 *
	 * There is an exception, and that is when PHP is being interspersed with HTML.
	 * In that case, the comment should come at the end of the statement (right
	 * before the closing tag, ?>). For example:
	 *
	 * <input type="text" id="<?php echo $id; // XSS OK ?>" />
	 *
	 * @since 0.4.0
	 *
	 * @param string  $comment  Comment to find.
	 * @param integer $stackPtr The position of the current token in the stack passed
	 *                          in $tokens.
	 *
	 * @return boolean True if whitelisting comment was found, false otherwise.
	 */
	protected function has_whitelist_comment( $comment, $stackPtr ) {

		$end_of_line = $lastPtr = $this->get_last_ptr_on_line( $stackPtr );

		// There is a findEndOfStatement() method, but it considers more tokens than
		// we need to here.
		$end_of_statement = $this->phpcsFile->findNext(
			array( T_CLOSE_TAG, T_SEMICOLON )
			, $stackPtr
		);

		// Check at the end of the statement if it comes before the end of the line.
		if ( $end_of_statement < $end_of_line ) {

			// If the statement was ended by a semicolon, we find the next non-
			// whitespace token. If the semicolon was left out and it was terminated
			// by an ending tag, we need to look backwards.
			if ( T_SEMICOLON === $this->tokens[ $end_of_statement ]['code'] ) {
				$lastPtr = $this->phpcsFile->findNext( T_WHITESPACE, $end_of_statement + 1, null, true );
			} else {
				$lastPtr = $this->phpcsFile->findPrevious( T_WHITESPACE, $end_of_statement - 1, null, true );
			}
		}

		$last = $this->tokens[ $lastPtr ];

		if ( T_COMMENT === $last['code'] ) {
			return preg_match( '#' . preg_quote( $comment ) . '#i', $last['content'] );
		} else {
			return false;
		}
	}

	/**
	 * Check if this variable is being assigned a value.
	 *
	 * E.g., $var = 'foo';
	 *
	 * Also handles array assignments to arbitrary depth:
	 *
	 * $array['key'][ $foo ][ something() ] = $bar;
	 *
	 * @since 0.4.0
	 *
	 * @param int $stackPtr The index of the token in the stack. This must points to
	 *                      either a T_VARIABLE or T_CLOSE_SQUARE_BRACKET token.
	 *
	 * @return bool Whether the token is a variable being assigned a value.
	 */
	protected function is_assignment( $stackPtr ) {

		$tokens = $this->phpcsFile->getTokens();

		// Must be a variable or closing square bracket (see below).
		if ( ! in_array( $tokens[ $stackPtr ]['code'], array( T_VARIABLE, T_CLOSE_SQUARE_BRACKET ) ) ) {
			return false;
		}

		$next_non_empty = $this->phpcsFile->findNext(
			PHP_CodeSniffer_Tokens::$emptyTokens
			, $stackPtr + 1
			, null
			, true
			, null
			, true
		);

		// No token found.
		if ( false === $next_non_empty ) {
			return false;
		}

		// If the next token is an assignment, that's all we need to know.
		if ( in_array( $tokens[ $next_non_empty ]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens ) ) {
			return true;
		}

		// Check if this is an array assignment, e.g., $var['key'] = 'val';
		if ( T_OPEN_SQUARE_BRACKET === $tokens[ $next_non_empty ]['code'] ) {
			return $this->is_assignment( $tokens[ $next_non_empty ]['bracket_closer'] );
		}

		return false;
	}

	/**
	 * Check if this token has an associated nonce check.
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The position of the current token in the stack of tokens.
	 *
	 * @return bool
	 */
	protected function has_nonce_check( $stackPtr ) {

		/**
		 * @var array {
		 *      A cache of the scope that we last checked for nonce verification in.
		 *
		 *      @var string $file  The name of the file.
		 *      @var int    $start The index of the token where the scope started.
		 *      @var int    $end   The index of the token where the scope ended.
		 *      @var bool|int $nonce_check The index of the token where an nonce
		 *                         check was found, or false if none was found.
		 * }
		 */
		static $last;

		$start = 0;
		$end = $stackPtr;

		$tokens = $this->phpcsFile->getTokens();

		// If we're in a function, only look inside of it.
		$f = $this->phpcsFile->getCondition( $stackPtr, T_FUNCTION );
		if ( $f ) {
			$start = $tokens[ $f ]['scope_opener'];
		}

		$in_isset = $this->is_in_isset_or_empty( $stackPtr );

		// We allow for isset( $_POST['var'] ) checks to come before the nonce check.
		// If this is inside an isset(), check after it as well, all the way to the
		// end of the scope.
		if ( $in_isset ) {
			$end = ( 0 === $start ) ? count( $tokens ) : $tokens[ $start ]['scope_closer'];
		}

		// Check if we've looked here before.
		$filename = $this->phpcsFile->getFilename();

		if (
			$filename === $last['file']
			&& $start === $last['start']
		) {

			if ( false !== $last['nonce_check'] ) {
				// If we have already found an nonce check in this scope, we just
				// need to check whether it comes before this token. It is OK if the
				// check is after the token though, if this was only a isset() check.
				return ( $in_isset || $last['nonce_check'] < $stackPtr );
			} elseif ( $end <= $last['end'] ) {
				// If not, we can still go ahead and return false if we've already
				// checked to the end of the search area.
				return false;
			}

			// We haven't checked this far yet, but we can still save work by
			// skipping over the part we've already checked.
			$start = $last['end'];
		} else {
			$last = array(
				'file'  => $filename,
				'start' => $start,
				'end'   => $end,
			);
		}

		// Loop through the tokens looking for nonce verification functions.
		for ( $i = $start; $i < $end; $i++ ) {

			// If this isn't a function name, skip it.
			if ( T_STRING !== $tokens[ $i ]['code'] ) {
				continue;
			}

			// If this is one of the nonce verification functions, we can bail out.
			if ( in_array( $tokens[ $i ]['content'], self::$nonceVerificationFunctions ) ) {
				$last['nonce_check'] = $i;
				return true;
			}
		}

		// We're still here, so no luck.
		$last['nonce_check'] = false;

		return false;
	}

	/**
	 * Check if a token is inside of an isset() or empty() statement.
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return bool Whether the token is inside an isset() or empty() statement.
	 */
	protected function is_in_isset_or_empty( $stackPtr ) {

		if ( ! isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			return false;
		}

		end( $this->tokens[ $stackPtr ]['nested_parenthesis'] );
		$open_parenthesis = key( $this->tokens[ $stackPtr ]['nested_parenthesis'] );
		reset( $this->tokens[ $stackPtr ]['nested_parenthesis'] );

		return in_array( $this->tokens[ $open_parenthesis - 1 ]['code'], array( T_ISSET, T_EMPTY ) );
	}

	/**
	 * Check if something is only being sanitized.
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return bool Whether the token is only within a sanitization.
	 */
	protected function is_only_sanitized( $stackPtr ) {

		// If it isn't being sanitized at all.
		if ( ! $this->is_sanitized( $stackPtr ) ) {
			return false;
		}

		// If this isn't set, we know the value must have only been casted, because
		// is_sanitized() would have returned false otherwise.
		if ( ! isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			return true;
		}

		// At this point we're expecting the value to have not been casted. If it
		// was, it wasn't *only* casted, because it's also in a function.
		if ( $this->is_safe_casted( $stackPtr ) ) {
			return false;
		}

		// The only parentheses should belong to the sanitizing function. If there's
		// more than one set, this isn't *only* sanitization.
		return ( count( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) === 1 );
	}

	/**
	 * Check if something is being casted to a safe value.
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return bool Whether the token being casted.
	 */
	protected function is_safe_casted( $stackPtr ) {

		// Get the last non-empty token.
		$prev = $this->phpcsFile->findPrevious(
			PHP_CodeSniffer_Tokens::$emptyTokens
			, $stackPtr - 1
			, null
			, true
		);

		// Check if it is a safe cast.
		return in_array(
			$this->tokens[ $prev ]['code']
			, array( T_INT_CAST, T_DOUBLE_CAST, T_BOOL_CAST )
		);
	}

	/**
	 * Check if something is being sanitized.
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return bool Whether the token being sanitized.
	 */
	protected function is_sanitized( $stackPtr ) {

		// First we check if it is being casted to a safe value.
		if ( $this->is_safe_casted( $stackPtr ) ) {
			return true;
		}

		// If this isn't within a function call, we know already that it's not safe.
		if ( ! isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			return false;
		}

		// Get the function that it's in.
		$function_closer = end( $this->tokens[ $stackPtr ]['nested_parenthesis'] );
		$function_opener = key( $this->tokens[ $stackPtr ]['nested_parenthesis'] );
		$functionPtr = $function_opener - 1;
		$function = $this->tokens[ $functionPtr ];

		// If it is just being unset, the value isn't used at all, so it's safe.
		if ( T_UNSET === $function['code'] ) {
			return true;
		}

		// If this isn't a call to a function, it sure isn't sanitizing function.
		if ( T_STRING !== $function['code'] ) {
			return false;
		}

		$functionName = $function['content'];

		// Arrays might be sanitized via array_map().
		if ( 'array_map' === $functionName ) {

			// Get the first parameter (name of function being used on the array).
			$mapped_function = $this->phpcsFile->findNext(
				PHP_CodeSniffer_Tokens::$emptyTokens
				, $function_opener + 1
				, $function_closer
				, true
			);

			// If we're able to resolve the function name, do so.
			if ( $mapped_function && T_CONSTANT_ENCAPSED_STRING === $this->tokens[ $mapped_function ]['code'] ) {
				$functionName = trim( $this->tokens[ $mapped_function ]['content'], '\'' );
			}
		}

		// Check if this is a sanitizing function.
		return in_array( $functionName, WordPress_Sniffs_XSS_EscapeOutputSniff::$sanitizingFunctions );
	}

	/**
	 * Get the index key of an array variable.
	 *
	 * E.g., "bar" in $foo['bar'].
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return string|false The array index key whose value is being accessed.
	 */
	protected function get_array_access_key( $stackPtr ) {

		// Find the next non-empty token.
		$open_bracket = $this->phpcsFile->findNext(
			PHP_CodeSniffer_Tokens::$emptyTokens
			, $stackPtr + 1
			, null
			, true
		);

		// If it isn't a bracket, this isn't an array-access.
		if ( T_OPEN_SQUARE_BRACKET !== $this->tokens[ $open_bracket ]['code'] ) {
			return false;
		}

		$key = $this->phpcsFile->getTokensAsString(
			$open_bracket + 1
			, $this->tokens[ $open_bracket ]['bracket_closer'] - $open_bracket - 1
		);

		return trim( $key );
	}

	/**
	 * Check if the existence of a variable is validated with isset() or empty().
	 *
	 * When $in_condition_only is false, (which is the default), this is considered
	 * valid:
	 *
	 * ```php
	 * if ( isset( $var ) ) {
	 *     // Do stuff, like maybe return or exit (but could be anything)
	 * }
	 *
	 * foo( $var );
	 * ```
	 *
	 * When it is true, that would be invalid, the use of the variable must be within
	 * the scope of the validating condition, like this:
	 *
	 * ```php
	 * if ( isset( $var ) ) {
	 *     foo( $var );
	 * }
	 * ```
	 *
	 * @since 0.5.0
	 *
	 * @param int    $stackPtr          The index of this token in the stack.
	 * @param string $array_key         An array key to check for ("bar" in $foo['bar']).
	 * @param bool   $in_condition_only Whether to require that this use of the
	 *                                  variable occur within the scope of the
	 *                                  validating condition, or just in the same
	 *                                  scope as it (default).
	 *
	 * @return bool Whether the var is validated.
	 */
	protected function is_validated( $stackPtr, $array_key = null, $in_condition_only = false ) {

		if ( $in_condition_only ) {

			// This is a stricter check, requiring the variable to be used only
			// within the validation condition.

			// If there are no conditions, there's no validation.
			if ( empty( $this->tokens[ $stackPtr ]['conditions'] ) ) {
				return false;
			}

			$conditions = $this->tokens[ $stackPtr ]['conditions'];
			end( $conditions ); // Get closest condition
			$conditionPtr = key( $conditions );
			$condition = $this->tokens[ $conditionPtr ];

			if ( ! isset( $condition['parenthesis_opener'] ) ) {

				$this->phpcsFile->addError(
					'Possible parse error, condition missing open parenthesis.'
					, $conditionPtr
					, 'IsValidatedMissingConditionOpener'
				);

				return false;
			}

			$scope_start = $condition['parenthesis_opener'];
			$scope_end   = $condition['parenthesis_closer'];

		} else {

			// We are are more loose, requiring only that the variable be validated
			// in the same function/file scope as it is used.

			// Check if we are in a function.
			$function = $this->phpcsFile->findPrevious( T_FUNCTION, $stackPtr );

			// If so, we check only within the function, otherwise the whole file.
			if ( false !== $function && $stackPtr < $this->tokens[ $function ]['scope_closer'] ) {
				$scope_start = $this->tokens[ $function ]['scope_opener'];
			} else {
				$scope_start = 0;
			}

			$scope_end = $stackPtr;
		}

		for ( $i = $scope_start + 1; $i < $scope_end; $i++ ) {

			if ( ! in_array( $this->tokens[ $i ]['code'], array( T_ISSET, T_EMPTY, T_UNSET ) ) ) {
				continue;
			}

			$issetOpener = $this->phpcsFile->findNext( T_OPEN_PARENTHESIS, $i );
			$issetCloser = $this->tokens[ $issetOpener ]['parenthesis_closer'];

			// Look for this variable. We purposely stomp $i from the parent loop.
			for ( $i = $issetOpener + 1; $i < $issetCloser; $i++ ) {

				if ( T_VARIABLE !== $this->tokens[ $i ]['code'] ) {
					continue;
				}

				// If we're checking for a specific array key (ex: 'hello' in
				// $_POST['hello']), that mush match too.
				if ( $array_key && $this->get_array_access_key( $i ) !== $array_key ) {
					continue;
				}

				return true;
			}
		}

	}
}

// EOF
