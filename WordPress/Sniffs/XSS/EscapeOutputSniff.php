<?php
/**
 * Squiz_Sniffs_XSS_EscapeOutputSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Weston Ruter <weston@x-team.com>
 */

/**
 * Verifies that all outputted strings are escaped.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Weston Ruter <weston@x-team.com>
 * @link     http://codex.wordpress.org/Data_Validation Data Validation on WordPress Codex
 */
class WordPress_Sniffs_XSS_EscapeOutputSniff extends WordPress_Sniff {

	/**
	 * Custom list of functions which escape values for output.
	 *
	 * @since 0.5.0
	 *
	 * @var string[]
	 */
	public $customEscapingFunctions = array();

	/**
	 * Custom list of functions whose return values are pre-escaped for output.
	 *
	 * @since 0.3.0
	 *
	 * @var string[]
	 */
	public $customAutoEscapedFunctions = array();

	/**
	 * Custom list of functions which escape values for output.
	 *
	 * @since 0.3.0
	 * @deprecated 0.5.0 Use $customEscapingFunctions instead.
	 *
	 * @var string[]
	 */
	public $customSanitizingFunctions = array();

	/**
	 * Custom list of functions which print output incorporating the passed values.
	 *
	 * @since 0.4.0
	 *
	 * @var string[]
	 */
	public $customPrintingFunctions = array();

	/**
	 * Printing functions that incorporate unsafe values.
	 *
	 * @since 0.4.0
	 *
	 * @var array
	 */
	public static $unsafePrintingFunctions = array(
		'_e'  => 'esc_html_e() or esc_attr_e()',
		'_ex' => 'esc_html_ex() or esc_attr_ex()',
	);

	/**
	 * Whether the custom functions were added to the default lists yet.
	 *
	 * @var bool
	 */
	public static $addedCustomFunctions = false;

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_ECHO,
			T_PRINT,
			T_EXIT,
			T_STRING,
		);

	} // end register()

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @return int|void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		// Merge any custom functions with the defaults, if we haven't already.
		if ( ! self::$addedCustomFunctions ) {
			WordPress_Sniff::$escapingFunctions    = array_merge( WordPress_Sniff::$escapingFunctions, array_flip( $this->customEscapingFunctions ) );
			WordPress_Sniff::$autoEscapedFunctions = array_merge( WordPress_Sniff::$autoEscapedFunctions, array_flip( $this->customAutoEscapedFunctions ) );
			WordPress_Sniff::$printingFunctions    = array_merge( WordPress_Sniff::$printingFunctions, array_flip( $this->customPrintingFunctions ) );

			if ( ! empty( $this->customSanitizingFunctions ) ) {
				WordPress_Sniff::$escapingFunctions = array_merge( WordPress_Sniff::$escapingFunctions, array_flip( $this->customSanitizingFunctions ) );
				$phpcsFile->addWarning( 'The customSanitizingFunctions property is deprecated in favor of customEscapingFunctions.', 0, 'DeprecatedCustomSanitizingFunctions' );
			}

			self::$addedCustomFunctions = true;
		}

		$this->init( $phpcsFile );
		$tokens = $phpcsFile->getTokens();

		$function = $tokens[ $stackPtr ]['content'];

		// Find the opening parenthesis (if present; T_ECHO might not have it).
		$open_paren = $phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );

		// If function, not T_ECHO nor T_PRINT.
		if ( T_STRING === $tokens[ $stackPtr ]['code'] ) {
			// Skip if it is a function but is not of the printing functions.
			if ( ! isset( self::$printingFunctions[ $tokens[ $stackPtr ]['content'] ] ) ) {
				return;
			}

			if ( isset( $tokens[ $open_paren ]['parenthesis_closer'] ) ) {
				$end_of_statement = $tokens[ $open_paren ]['parenthesis_closer'];
			}

			// These functions only need to have the first argument escaped.
			if ( in_array( $function, array( 'trigger_error', 'user_error' ), true ) ) {
				$end_of_statement = $phpcsFile->findEndOfStatement( $open_paren + 1 );
			}
		}

		// Checking for the ignore comment, ex: //xss ok.
		if ( $this->has_whitelist_comment( 'xss', $stackPtr ) ) {
			return;
		}

		if ( isset( $end_of_statement, self::$unsafePrintingFunctions[ $function ] ) ) {
			$error = $phpcsFile->addError( "Expected next thing to be an escaping function (like %s), not '%s'", $stackPtr, 'UnsafePrintingFunction', array( self::$unsafePrintingFunctions[ $function ], $function ) );

			// If the error was reported, don't bother checking the function's arguments.
			if ( $error ) {
				return $end_of_statement;
			}
		}

		$ternary = false;

		// This is already determined if this is a function and not T_ECHO.
		if ( ! isset( $end_of_statement ) ) {

			$end_of_statement = $phpcsFile->findNext( array( T_SEMICOLON, T_CLOSE_TAG ), $stackPtr );
			$last_token       = $phpcsFile->findPrevious( PHP_CodeSniffer_Tokens::$emptyTokens, ( $end_of_statement - 1 ), null, true );

			// Check for the ternary operator. We only need to do this here if this
			// echo is lacking parenthesis. Otherwise it will be handled below.
			if ( T_OPEN_PARENTHESIS !== $tokens[ $open_paren ]['code'] || T_CLOSE_PARENTHESIS !== $tokens[ $last_token ]['code'] ) {

				$ternary = $phpcsFile->findNext( T_INLINE_THEN, $stackPtr, $end_of_statement );

				// If there is a ternary skip over the part before the ?. However, if
				// the ternary is within parentheses, it will be handled in the loop.
				if ( $ternary && empty( $tokens[ $ternary ]['nested_parenthesis'] ) ) {
					$stackPtr = $ternary;
				}
			}
		}

		// Ignore the function itself.
		$stackPtr++;

		$in_cast = false;

		// Looping through echo'd components.
		$watch = true;
		for ( $i = $stackPtr; $i < $end_of_statement; $i++ ) {

			// Ignore whitespaces and comments.
			if ( in_array( $tokens[ $i ]['code'], array( T_WHITESPACE, T_COMMENT ), true ) ) {
				continue;
			}

			if ( T_OPEN_PARENTHESIS === $tokens[ $i ]['code'] ) {

				if ( $in_cast ) {

					// Skip to the end of a function call if it has been casted to a safe value.
					$i       = $tokens[ $i ]['parenthesis_closer'];
					$in_cast = false;

				} else {

					// Skip over the condition part of a ternary (i.e., to after the ?).
					$ternary = $phpcsFile->findNext( T_INLINE_THEN, $i, $tokens[ $i ]['parenthesis_closer'] );

					if ( $ternary ) {

						$next_paren = $phpcsFile->findNext( T_OPEN_PARENTHESIS, ( $i + 1 ), $tokens[ $i ]['parenthesis_closer'] );

						// We only do it if the ternary isn't within a subset of parentheses.
						if ( ! $next_paren || $ternary > $tokens[ $next_paren ]['parenthesis_closer'] ) {
							$i = $ternary;
						}
					}
				}

				continue;
			}

			// Handle arrays for those functions that accept them.
			if ( T_ARRAY === $tokens[ $i ]['code'] ) {
				$i++; // Skip the opening parenthesis.
				continue;
			}

			if ( in_array( $tokens[ $i ]['code'], array( T_DOUBLE_ARROW, T_CLOSE_PARENTHESIS ), true ) ) {
				continue;
			}

			// Handle magic constants for debug functions.
			if ( in_array( $tokens[ $i ]['code'], array( T_METHOD_C, T_FUNC_C, T_FILE, T_CLASS_C ), true ) ) {
				continue;
			}

			// Wake up on concatenation characters, another part to check.
			if ( in_array( $tokens[ $i ]['code'], array( T_STRING_CONCAT ), true ) ) {
				$watch = true;
				continue;
			}

			// Wake up after a ternary else (:).
			if ( $ternary && in_array( $tokens[ $i ]['code'], array( T_INLINE_ELSE ), true ) ) {
				$watch = true;
				continue;
			}

			// Wake up for commas.
			if ( T_COMMA === $tokens[ $i ]['code'] ) {
				$in_cast = false;
				$watch   = true;
				continue;
			}

			if ( false === $watch ) {
				continue;
			}

			// Allow T_CONSTANT_ENCAPSED_STRING eg: echo 'Some String';
			// Also T_LNUMBER, e.g.: echo 45; exit -1; and booleans.
			if ( in_array( $tokens[ $i ]['code'], array( T_CONSTANT_ENCAPSED_STRING, T_LNUMBER, T_MINUS, T_TRUE, T_FALSE, T_NULL ), true ) ) {
				continue;
			}

			$watch = false;

			// Allow int/double/bool casted variables.
			if ( in_array( $tokens[ $i ]['code'], array( T_INT_CAST, T_DOUBLE_CAST, T_BOOL_CAST ), true ) ) {
				$in_cast = true;
				continue;
			}

			// Now check that next token is a function call.
			if ( T_STRING === $this->tokens[ $i ]['code'] ) {

				$ptr                    = $i;
				$functionName           = $this->tokens[ $i ]['content'];
				$function_opener        = $this->phpcsFile->findNext( array( T_OPEN_PARENTHESIS ), ( $i + 1 ), null, null, null, true );
				$is_formatting_function = isset( self::$formattingFunctions[ $functionName ] );

				if ( $function_opener ) {

					if ( 'array_map' === $functionName ) {

						// Get the first parameter (name of function being used on the array).
						$mapped_function = $this->phpcsFile->findNext(
							PHP_CodeSniffer_Tokens::$emptyTokens,
							( $function_opener + 1 ),
							$tokens[ $function_opener ]['parenthesis_closer'],
							true
						);

						// If we're able to resolve the function name, do so.
						if ( $mapped_function && T_CONSTANT_ENCAPSED_STRING === $this->tokens[ $mapped_function ]['code'] ) {
							$functionName = trim( $this->tokens[ $mapped_function ]['content'], '\'' );
							$ptr = $mapped_function;
						}
					}

					// Skip pointer to after the function.
					// If this is a formatting function we just skip over the opening
					// parenthesis. Otherwise we skip all the way to the closing.
					if ( $is_formatting_function ) {
						$i     = ( $function_opener + 1 );
						$watch = true;
					} else {
						$i = $this->tokens[ $function_opener ]['parenthesis_closer'];
					}
				}

				// If this is a safe function, we don't flag it.
				if (
					$is_formatting_function
					|| isset( self::$autoEscapedFunctions[ $functionName ] )
					|| isset( self::$escapingFunctions[ $functionName ] )
				) {
					continue;
				}

				$content = $functionName;

			} else {
				$content = $this->tokens[ $i ]['content'];
				$ptr     = $i;
			}

			$this->phpcsFile->addError(
				"Expected next thing to be an escaping function (see Codex for 'Data Validation'), not '%s'",
				$ptr,
				'OutputNotEscaped',
				$content
			);
		}

		return $end_of_statement;

	} // end process()

} // end class
