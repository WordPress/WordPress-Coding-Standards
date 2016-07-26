<?php
/**
 * Enforces spacing around logical operators and assignments, based upon Squiz code.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   John Godley <john@urbangiraffe.com>
 * @author   Greg Sherwood <gsherwood@squiz.net>
 * @author   Marc McIntyre <mmcintyre@squiz.net>
 */

/**
 * Squiz_Sniffs_WhiteSpace_ControlStructureSpacingSniff.
 *
 * Checks that any array declarations are lower case.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   John Godley <john@urbangiraffe.com>
 * @author   Greg Sherwood <gsherwood@squiz.net>
 * @author   Marc McIntyre <mmcintyre@squiz.net>
 */
class WordPress_Sniffs_WhiteSpace_ControlStructureSpacingSniff extends WordPress_Sniff {

	/**
	 * A list of tokenizers this sniff supports.
	 *
	 * @var array
	 */
	public $supportedTokenizers = array( 'PHP' );

	/**
	 * Check for blank lines on start/end of control structures.
	 *
	 * @var boolean
	 */
	public $blank_line_check = false;

	/**
	 * Check for blank lines after control structures.
	 *
	 * @var boolean
	 */
	public $blank_line_after_check = true;

	/**
	 * Require for space before T_COLON when using the alternative syntax for control structures.
	 *
	 * @var string one of 'required', 'forbidden', optional'
	 */
	public $space_before_colon = 'required';

	/**
	 * How many spaces should be between a T_CLOSURE and T_OPEN_PARENTHESIS.
	 *
	 * function[*]() {...}
	 *
	 * @since 0.7.0
	 *
	 * @var int
	 */
	public $spaces_before_closure_open_paren = -1;

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_IF,
			T_WHILE,
			T_FOREACH,
			T_FOR,
			T_SWITCH,
			T_DO,
			T_ELSE,
			T_ELSEIF,
			T_FUNCTION,
			T_CLOSURE,
			T_USE,
		);

	} // end register()

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
		$this->blank_line_check 	  = (bool) $this->blank_line_check;
		$this->blank_line_after_check = (bool) $this->blank_line_after_check;

		$tokens = $phpcsFile->getTokens();

		$this->init( $phpcsFile );

		if ( isset( $tokens[ ( $stackPtr + 1 ) ] ) && T_WHITESPACE !== $tokens[ ( $stackPtr + 1 ) ]['code']
			&& ! ( T_ELSE === $tokens[ $stackPtr ]['code'] && T_COLON === $tokens[ ( $stackPtr + 1 ) ]['code'] )
			&& ! (
				T_CLOSURE === $tokens[ $stackPtr ]['code']
				&& (
					0 === (int) $this->spaces_before_closure_open_paren
					|| -1 === (int) $this->spaces_before_closure_open_paren
				)
			)
		) {
			$error = 'Space after opening control structure is required';
			if ( isset( $phpcsFile->fixer ) ) {
				$fix = $phpcsFile->addFixableError( $error, $stackPtr, 'NoSpaceAfterStructureOpen' );
				if ( true === $fix ) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->addContent( $stackPtr, ' ' );
					$phpcsFile->fixer->endChangeset();
				}
			} else {
				$phpcsFile->addError( $error, $stackPtr, 'NoSpaceAfterStructureOpen' );
			}
		}

		if ( ! isset( $tokens[ $stackPtr ]['scope_closer'] ) ) {

			if ( T_USE === $tokens[ $stackPtr ]['code'] && 'closure' === $this->get_use_type( $stackPtr ) ) {
				$scopeOpener = $phpcsFile->findNext( T_OPEN_CURLY_BRACKET, ( $stackPtr + 1 ) );
				$scopeCloser = $tokens[ $scopeOpener ]['scope_closer'];
			} else {
				return;
			}
		} else {
			$scopeOpener = $tokens[ $stackPtr ]['scope_opener'];
			$scopeCloser = $tokens[ $stackPtr ]['scope_closer'];
		}

		// Alternative syntax.
		if ( T_COLON === $tokens[ $scopeOpener ]['code'] ) {

			if ( 'required' === $this->space_before_colon ) {

				if ( T_WHITESPACE !== $tokens[ ( $scopeOpener - 1 ) ]['code'] ) {
					$error = 'Space between opening control structure and T_COLON is required';

					if ( isset( $phpcsFile->fixer ) ) {
						$fix = $phpcsFile->addFixableError( $error, $scopeOpener, 'NoSpaceBetweenStructureColon' );

						if ( true === $fix ) {
							$phpcsFile->fixer->beginChangeset();
							$phpcsFile->fixer->addContentBefore( $scopeOpener, ' ' );
							$phpcsFile->fixer->endChangeset();
						}
					} else {
						$phpcsFile->addError( $error, $stackPtr, 'NoSpaceBetweenStructureColon' );
					}
				}
			} elseif ( 'forbidden' === $this->space_before_colon ) {

				if ( T_WHITESPACE === $tokens[ ( $scopeOpener - 1 ) ]['code'] ) {
					$error = 'Extra space between opening control structure and T_COLON found';

					if ( isset( $phpcsFile->fixer ) ) {
						$fix = $phpcsFile->addFixableError( $error, ( $scopeOpener - 1 ), 'SpaceBetweenStructureColon' );

						if ( true === $fix ) {
							$phpcsFile->fixer->beginChangeset();
							$phpcsFile->fixer->replaceToken( ( $scopeOpener - 1 ), '' );
							$phpcsFile->fixer->endChangeset();
						}
					} else {
						$phpcsFile->addError( $error, $stackPtr, 'SpaceBetweenStructureColon' );
					}
				}
			}
		}

		$parenthesisOpener = $phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );

		// If this is a function declaration.
		if ( T_FUNCTION === $tokens[ $stackPtr ]['code'] ) {

			if ( T_STRING === $tokens[ $parenthesisOpener ]['code'] ) {

				$function_name_ptr = $parenthesisOpener;

			} elseif ( T_BITWISE_AND === $tokens[ $parenthesisOpener ]['code'] ) {

				// This function returns by reference (function &function_name() {}).
				$function_name_ptr = $parenthesisOpener = $phpcsFile->findNext(
					PHP_CodeSniffer_Tokens::$emptyTokens,
					( $parenthesisOpener + 1 ),
					null,
					true
				);
			}

			$parenthesisOpener = $phpcsFile->findNext(
				PHP_CodeSniffer_Tokens::$emptyTokens,
				( $parenthesisOpener + 1 ),
				null,
				true
			);

			// Checking this: function my_function[*](...) {}.
			if ( ( $function_name_ptr + 1 ) !== $parenthesisOpener ) {

				$error = 'Space between function name and opening parenthesis is prohibited.';
				$fix   = $phpcsFile->addFixableError(
					$error,
					$stackPtr,
					'SpaceBeforeFunctionOpenParenthesis',
					$tokens[ ( $function_name_ptr + 1 ) ]['content']
				);

				if ( true === $fix ) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->replaceToken( ( $function_name_ptr + 1 ), '' );
					$phpcsFile->fixer->endChangeset();
				}
			}
		} elseif ( T_CLOSURE === $tokens[ $stackPtr ]['code'] ) {

			// Check if there is a use () statement.
			if ( isset( $tokens[ $parenthesisOpener ]['parenthesis_closer'] ) ) {

				$usePtr = $phpcsFile->findNext(
					PHP_CodeSniffer_Tokens::$emptyTokens,
					( $tokens[ $parenthesisOpener ]['parenthesis_closer'] + 1 ),
					null,
					true,
					null,
					true
				);

				// If it is, we set that as the "scope opener".
				if ( T_USE === $tokens[ $usePtr ]['code'] ) {
					$scopeOpener = $usePtr;
				}
			}
		}

		if (
			T_COLON !== $tokens[ $parenthesisOpener ]['code']
			&& T_FUNCTION !== $tokens[ $stackPtr ]['code']
		) {

			if (
				T_CLOSURE === $tokens[ $stackPtr ]['code']
				&& 0 === (int) $this->spaces_before_closure_open_paren
			) {

				if ( ( $stackPtr + 1) !== $parenthesisOpener ) {
					// Checking this: function[*](...) {}.
					$error = 'Space before closure opening parenthesis is prohibited';
					$fix   = $phpcsFile->addFixableError( $error, $stackPtr, 'SpaceBeforeClosureOpenParenthesis' );
					if ( true === $fix ) {
						$phpcsFile->fixer->beginChangeset();
						$phpcsFile->fixer->replaceToken( ( $stackPtr + 1 ), '' );
						$phpcsFile->fixer->endChangeset();
					}
				}
			} elseif (
				(
					T_CLOSURE !== $tokens[ $stackPtr ]['code']
					|| 1 === (int) $this->spaces_before_closure_open_paren
				)
				&& ( $stackPtr + 1 ) === $parenthesisOpener
			) {

				// Checking this: if[*](...) {}.
				$error = 'No space before opening parenthesis is prohibited';
				if ( isset( $phpcsFile->fixer ) ) {
					$fix = $phpcsFile->addFixableError( $error, $stackPtr, 'NoSpaceBeforeOpenParenthesis' );
					if ( true === $fix ) {
						$phpcsFile->fixer->beginChangeset();
						$phpcsFile->fixer->addContent( $stackPtr, ' ' );
						$phpcsFile->fixer->endChangeset();
					}
				} else {
					$phpcsFile->addError( $error, $stackPtr, 'NoSpaceBeforeOpenParenthesis' );
				}
			}
		}

		if (
			T_WHITESPACE === $tokens[ ( $stackPtr + 1 ) ]['code']
			&& ' ' !== $tokens[ ( $stackPtr + 1 ) ]['content']
		) {
			// Checking this: if [*](...) {}.
			$error = 'Expected exactly one space before opening parenthesis; "%s" found.';
			$fix = $phpcsFile->addFixableError(
				$error,
				$stackPtr,
				'ExtraSpaceBeforeOpenParenthesis',
				$tokens[ ( $stackPtr + 1 ) ]['content']
			);

			if ( true === $fix ) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken( ( $stackPtr + 1 ), ' ' );
				$phpcsFile->fixer->endChangeset();
			}
		}

		if ( T_WHITESPACE !== $tokens[ ( $parenthesisOpener + 1) ]['code']
			&& T_CLOSE_PARENTHESIS !== $tokens[ ( $parenthesisOpener + 1) ]['code']
		) {
			// Checking this: $value = my_function([*]...).
			$error = 'No space after opening parenthesis is prohibited';
			if ( isset( $phpcsFile->fixer ) ) {
				$fix = $phpcsFile->addFixableError( $error, $stackPtr, 'NoSpaceAfterOpenParenthesis' );
				if ( true === $fix ) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->addContent( $parenthesisOpener, ' ' );
					$phpcsFile->fixer->endChangeset();
				}
			} else {
				$phpcsFile->addError( $error, $stackPtr, 'NoSpaceAfterOpenParenthesis' );
			}
		}

		if ( isset( $tokens[ $parenthesisOpener ]['parenthesis_closer'] ) ) {

			$parenthesisCloser = $tokens[ $parenthesisOpener ]['parenthesis_closer'];

			if ( T_CLOSE_PARENTHESIS !== $tokens[ ( $parenthesisOpener + 1 ) ]['code'] ) {

				if ( T_WHITESPACE !== $tokens[ ( $parenthesisCloser - 1 ) ]['code'] ) {
					$error = 'No space before closing parenthesis is prohibited';
					if ( isset( $phpcsFile->fixer ) ) {
						$fix = $phpcsFile->addFixableError( $error, $parenthesisCloser, 'NoSpaceBeforeCloseParenthesis' );
						if ( true === $fix ) {
							$phpcsFile->fixer->beginChangeset();
							$phpcsFile->fixer->addContentBefore( $parenthesisCloser, ' ' );
							$phpcsFile->fixer->endChangeset();
						}
					} else {
						$phpcsFile->addError( $error, $parenthesisCloser, 'NoSpaceBeforeCloseParenthesis' );
					}
				}

				if (
					T_WHITESPACE !== $tokens[ ( $parenthesisCloser + 1 ) ]['code']
					&& T_COLON !== $tokens[ $scopeOpener ]['code']
				) {
					$error = 'Space between opening control structure and closing parenthesis is required';

					if ( isset( $phpcsFile->fixer ) ) {
						$fix = $phpcsFile->addFixableError( $error, $scopeOpener, 'NoSpaceAfterCloseParenthesis' );

						if ( true === $fix ) {
							$phpcsFile->fixer->beginChangeset();
							$phpcsFile->fixer->addContentBefore( $scopeOpener, ' ' );
							$phpcsFile->fixer->endChangeset();
						}
					} else {
						$phpcsFile->addError( $error, $stackPtr, 'NoSpaceAfterCloseParenthesis' );
					}
				}
			}

			if ( isset( $tokens[ $parenthesisOpener ]['parenthesis_owner'] )
				&& $tokens[ $parenthesisCloser ]['line'] !== $tokens[ $scopeOpener ]['line']
			) {
				$error = 'Opening brace should be on the same line as the declaration';
				if ( isset( $phpcsFile->fixer ) ) {
					$fix = $phpcsFile->addFixableError( $error, $parenthesisOpener, 'OpenBraceNotSameLine' );
					if ( true === $fix ) {
						$phpcsFile->fixer->beginChangeset();

						for ( $i = ( $parenthesisCloser + 1 ); $i < $scopeOpener; $i++ ) {
							$phpcsFile->fixer->replaceToken( $i, '' );
						}

						$phpcsFile->fixer->addContent( $parenthesisCloser, ' ' );
						$phpcsFile->fixer->endChangeset();
					}
				} else {
					$phpcsFile->addError( $error, $parenthesisOpener, 'OpenBraceNotSameLine' );
				} // end if
				return;

			} elseif (
				T_WHITESPACE === $tokens[ ( $parenthesisCloser + 1 ) ]['code']
				&& ' ' !== $tokens[ ( $parenthesisCloser + 1 ) ]['content']
			) {

				// Checking this: if (...) [*]{}.
				$error = 'Expected exactly one space between closing parenthesis and opening control structure; "%s" found.';
				$fix = $phpcsFile->addFixableError(
					$error,
					$stackPtr,
					'ExtraSpaceAfterCloseParenthesis',
					$tokens[ ( $parenthesisCloser + 1 ) ]['content']
				);

				if ( true === $fix ) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->replaceToken( ( $parenthesisCloser + 1 ), ' ' );
					$phpcsFile->fixer->endChangeset();
				}
			}
		} // end if

		if ( true === $this->blank_line_check ) {
			$firstContent = $phpcsFile->findNext( T_WHITESPACE, ( $scopeOpener + 1 ), null, true );
			if ( $tokens[ $firstContent ]['line'] > ( $tokens[ $scopeOpener ]['line'] + 1 )
				&& false === in_array( $tokens[ $firstContent ]['code'], array( T_CLOSE_TAG, T_COMMENT ), true )
			) {
				$error = 'Blank line found at start of control structure';
				if ( isset( $phpcsFile->fixer ) ) {
					$fix = $phpcsFile->addFixableError( $error, $scopeOpener, 'BlankLineAfterStart' );
					if ( true === $fix ) {
						$phpcsFile->fixer->beginChangeset();

						for ( $i = ( $scopeOpener + 1 ); $i < $firstContent; $i++ ) {
							$phpcsFile->fixer->replaceToken( $i, '' );
						}

						$phpcsFile->fixer->addNewline( $scopeOpener );
						$phpcsFile->fixer->endChangeset();
					}
				} else {
					$phpcsFile->addError( $error, $scopeOpener, 'BlankLineAfterStart' );
				}
			}

			$lastContent = $phpcsFile->findPrevious( T_WHITESPACE, ( $scopeCloser - 1 ), null, true );
			if ( ( $tokens[ $scopeCloser ]['line'] - 1 ) !== $tokens[ $lastContent ]['line'] ) {
				$errorToken = $scopeCloser;
				for ( $i = ( $scopeCloser - 1 ); $i > $lastContent; $i-- ) {
					if ( $tokens[ $i ]['line'] < $tokens[ $scopeCloser ]['line']
						&& T_OPEN_TAG !== $tokens[ $firstContent ]['code']
					) {
						// TODO: Reporting error at empty line won't highlight it in IDE.
						$error = 'Blank line found at end of control structure';
						if ( isset( $phpcsFile->fixer ) ) {
							$fix = $phpcsFile->addFixableError( $error, $i, 'BlankLineBeforeEnd' );
							if ( true === $fix ) {
								$phpcsFile->fixer->beginChangeset();

								for ( $j = ( $lastContent + 1 ); $j < $scopeCloser; $j++ ) {
									$phpcsFile->fixer->replaceToken( $j, '' );
								}

								$phpcsFile->fixer->addNewlineBefore( $scopeCloser );
								$phpcsFile->fixer->endChangeset();
							}
						} else {
							$phpcsFile->addError( $error, $i, 'BlankLineBeforeEnd' );
						} // end if
						break;
					} // end if
				} // end for
			} // end if
		} // end if

		$trailingContent = $phpcsFile->findNext( T_WHITESPACE, ( $scopeCloser + 1 ), null, true );
		if ( T_ELSE === $tokens[ $trailingContent ]['code'] ) {
			if ( T_IF === $tokens[ $stackPtr ]['code'] ) {
				// IF with ELSE.
				return;
			}
		}

		if ( T_COMMENT === $tokens[ $trailingContent ]['code'] ) {
			if ( $tokens[ $trailingContent ]['line'] === $tokens[ $scopeCloser ]['line'] ) {
				if ( '//end' === substr( $tokens[ $trailingContent ]['content'], 0, 5 ) ) {
					// There is an end comment, so we have to get the next piece
					// of content.
					$trailingContent = $phpcsFile->findNext( T_WHITESPACE, ( $trailingContent + 1), null, true );
				}
			}
		}

		if ( T_BREAK === $tokens[ $trailingContent ]['code'] ) {
			// If this BREAK is closing a CASE, we don't need the
			// blank line after this control structure.
			if ( isset( $tokens[ $trailingContent ]['scope_condition'] ) ) {
				$condition = $tokens[ $trailingContent ]['scope_condition'];
				if ( T_CASE === $tokens[ $condition ]['code'] || T_DEFAULT === $tokens[ $condition ]['code'] ) {
					return;
				}
			}
		}

		if ( T_CLOSE_TAG === $tokens[ $trailingContent ]['code'] ) {
			// At the end of the script or embedded code.
			return;
		}

		if ( T_CLOSE_CURLY_BRACKET === $tokens[ $trailingContent ]['code'] ) {
			// Another control structure's closing brace.
			if ( isset( $tokens[ $trailingContent ]['scope_condition'] ) ) {
				$owner = $tokens[ $trailingContent ]['scope_condition'];
				if ( in_array( $tokens[ $owner ]['code'], array( T_FUNCTION, T_CLASS, T_INTERFACE, T_TRAIT ), true ) ) {
					// The next content is the closing brace of a function, class, interface or trait
					// so normal function/class rules apply and we can ignore it.
					return;
				}
			}

			if ( true === $this->blank_line_after_check
				&& ( $tokens[ $scopeCloser ]['line'] + 1 ) !== $tokens[ $trailingContent ]['line']
			) {
				// TODO: Won't cover following case: "} echo 'OK';".
				$error = 'Blank line found after control structure';
				if ( isset( $phpcsFile->fixer ) ) {
					$fix = $phpcsFile->addFixableError( $error, $scopeCloser, 'BlankLineAfterEnd' );
					if ( true === $fix ) {
						$phpcsFile->fixer->beginChangeset();

						for ( $i = ( $scopeCloser + 1 ); $i < $trailingContent; $i++ ) {
							$phpcsFile->fixer->replaceToken( $i, '' );
						}

						// TODO: Instead a separate error should be triggered when content comes right after closing brace.
						$phpcsFile->fixer->addNewlineBefore( $trailingContent );
						$phpcsFile->fixer->endChangeset();
					}
				} else {
					$phpcsFile->addError( $error, $scopeCloser, 'BlankLineAfterEnd' );
				}
			}
		} // end if

	} // end process()

} // end class
