<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

if ( ! class_exists( 'Squiz_Sniffs_Arrays_ArrayDeclarationSniff', true ) ) {
	throw new PHP_CodeSniffer_Exception( 'Class Squiz_Sniffs_Arrays_ArrayDeclarationSniff not found' );
}

/**
 * Enforces WordPress array format, based upon Squiz code.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#indentation
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.1.0
 * @since   0.5.0 Now extends `Squiz_Sniffs_Arrays_ArrayDeclarationSniff`.
 *
 * @todo    Check whether the upstream PRs have been merged and released and if possible,
 *          remove duplicate logic.
 *          Ref: commit 3ea49d2b56f248d83bed890f9f5246d67c775d54
 *          "The upstream version is similar, except that we exclude a few errors.
 *          Unfortunately we have to actually comment out the code rather than just
 *          using the upstream sniff and `<exclude>` in our ruleset, due to a bug
 *          (squizlabs/PHP_CodeSniffer#582). (I've also included a fix for another
 *          bug, squizlabs/PHP_CodeSniffer#584.) Because of this, we cannot yet
 *          eliminate duplicated logic from this child sniff."
 *
 * Last synced with parent class ?[unknown date]? at commit ?[unknown commit]?.
 * @link    https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/Squiz/Sniffs/Arrays/ArrayDeclarationSniff.php
 */
class WordPress_Sniffs_Arrays_ArrayDeclarationSniff extends Squiz_Sniffs_Arrays_ArrayDeclarationSniff {

	/**
	 * Process a single line array.
	 *
	 * @since 0.5.0
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile  The file being scanned.
	 * @param int                  $stackPtr   The position of the current token
	 *                                         in the stack passed in $tokens.
	 * @param int                  $arrayStart Position of the array opener in the token stack.
	 * @param int                  $arrayEnd   Position of the array closer in the token stack.
	 */
	public function processSingleLineArray( PHP_CodeSniffer_File $phpcsFile, $stackPtr, $arrayStart, $arrayEnd ) {

		parent::processSingleLineArray( $phpcsFile, $stackPtr, $arrayStart, $arrayEnd );

		// This array is empty, so the below checks aren't necessary.
		if ( ( $arrayStart + 1 ) === $arrayEnd ) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		// Check that there is a single space after the array opener.
		if ( T_WHITESPACE !== $tokens[ ( $arrayStart + 1 ) ]['code'] ) {

			$warning = 'Missing space after array opener.';
			$fix     = $phpcsFile->addFixableError( $warning, $arrayStart, 'NoSpaceAfterOpenParenthesis' );

			if ( true === $fix ) {
				$phpcsFile->fixer->addContent( $arrayStart, ' ' );
			}
		} elseif ( ' ' !== $tokens[ ( $arrayStart + 1 ) ]['content'] ) {

			$fix = $phpcsFile->addFixableError(
				'Expected 1 space after array opener, found %s.',
				$arrayStart,
				'SpaceAfterArrayOpener',
				strlen( $tokens[ ( $arrayStart + 1 ) ]['content'] )
			);

			if ( true === $fix ) {
				$phpcsFile->fixer->replaceToken( ( $arrayStart + 1 ), ' ' );
			}
		}

		if ( T_WHITESPACE !== $tokens[ ( $arrayEnd - 1 ) ]['code'] ) {

			$warning = 'Missing space before array closer.';
			$fix     = $phpcsFile->addFixableError( $warning, $arrayEnd, 'NoSpaceAfterOpenParenthesis' );

			if ( true === $fix ) {
				$phpcsFile->fixer->addContentBefore( $arrayEnd, ' ' );
			}
		} elseif ( ' ' !== $tokens[ ( $arrayEnd - 1 ) ]['content'] ) {

			$fix = $phpcsFile->addFixableError(
				'Expected 1 space before array closer, found %s.',
				$arrayEnd,
				'SpaceAfterArrayCloser',
				strlen( $tokens[ ( $arrayEnd - 1 ) ]['content'] )
			);

			if ( true === $fix ) {
				$phpcsFile->fixer->replaceToken( ( $arrayEnd - 1 ), ' ' );
			}
		}
	}

	/**
	 * Process a multi-line array.
	 *
	 * @since 0.5.0
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile  The file being scanned.
	 * @param int                  $stackPtr   The position of the current token
	 *                                         in the stack passed in $tokens.
	 * @param int                  $arrayStart Position of the array opener in the token stack.
	 * @param int                  $arrayEnd   Position of the array closer in the token stack.
	 */
	public function processMultiLineArray( PHP_CodeSniffer_File $phpcsFile, $stackPtr, $arrayStart, $arrayEnd ) {
		$tokens       = $phpcsFile->getTokens();
		$keywordStart = $tokens[ $stackPtr ]['column'];

		// Check the closing bracket is on a new line.
		$lastContent = $phpcsFile->findPrevious( T_WHITESPACE, ( $arrayEnd - 1 ), $arrayStart, true );
		if ( $tokens[ $lastContent ]['line'] === $tokens[ $arrayEnd ]['line'] ) {
			$error = 'Closing parenthesis of array declaration must be on a new line';
			$fix   = $phpcsFile->addFixableError( $error, $arrayEnd, 'CloseBraceNewLine' );
			if ( true === $fix ) {
				$phpcsFile->fixer->addNewlineBefore( $arrayEnd );
			}
		} // end if

		$nextToken  = $stackPtr;
		$keyUsed    = false;
		$singleUsed = false;
		$indices    = array();
		$maxLength  = 0;

		if ( T_ARRAY === $tokens[ $stackPtr ]['code'] ) {
			$lastToken = $tokens[ $stackPtr ]['parenthesis_opener'];
		} else {
			$lastToken = $stackPtr;
		}

		// Find all the double arrows that reside in this scope.
		for ( $nextToken = ( $stackPtr + 1 ); $nextToken < $arrayEnd; $nextToken++ ) {
			// Skip bracketed statements, like function calls.
			if ( T_OPEN_PARENTHESIS === $tokens[ $nextToken ]['code']
			    && ( ! isset( $tokens[ $nextToken ]['parenthesis_owner'] )
			        || $tokens[ $nextToken ]['parenthesis_owner'] !== $stackPtr )
			) {
				$nextToken = $tokens[ $nextToken ]['parenthesis_closer'];
				continue;
			}

			if ( T_ARRAY === $tokens[ $nextToken ]['code'] ) {
				// Let subsequent calls of this test handle nested arrays.
				if ( T_DOUBLE_ARROW !== $tokens[ $lastToken ]['code'] ) {
					$indices[] = array( 'value' => $nextToken );
					$lastToken = $nextToken;
				}

				$nextToken = $tokens[ $tokens[ $nextToken ]['parenthesis_opener'] ]['parenthesis_closer'];
				$nextToken = $phpcsFile->findNext( T_WHITESPACE, ( $nextToken + 1 ), null, true );
				if ( T_COMMA !== $tokens[ $nextToken ]['code'] ) {
					$nextToken--;
				} else {
					$lastToken = $nextToken;
				}

				continue;
			}

			if ( T_OPEN_SHORT_ARRAY === $tokens[ $nextToken ]['code'] ) {
				// Let subsequent calls of this test handle nested arrays.
				if ( T_DOUBLE_ARROW !== $tokens[ $lastToken ]['code'] ) {
					$indices[] = array( 'value' => $nextToken );
					$lastToken = $nextToken;
				}

				$nextToken = $tokens[ $nextToken ]['bracket_closer'];
				$nextToken = $phpcsFile->findNext( T_WHITESPACE, ( $nextToken + 1 ), null, true );
				if ( T_COMMA !== $tokens[ $nextToken ]['code'] ) {
					$nextToken--;
				} else {
					$lastToken = $nextToken;
				}

				continue;
			}

			if ( T_CLOSURE === $tokens[ $nextToken ]['code'] ) {
				if ( T_DOUBLE_ARROW !== $tokens[ $lastToken ]['code'] ) {
					$indices[] = array( 'value' => $nextToken );
					$lastToken = $nextToken;
				}

				$nextToken = $tokens[ $nextToken ]['scope_closer'];
				$nextToken = $phpcsFile->findNext( T_WHITESPACE, ( $nextToken + 1 ), null, true );
				if ( T_COMMA !== $tokens[ $nextToken ]['code'] ) {
					$nextToken--;
				} else {
					$lastToken = $nextToken;
				}

				continue;
			}

			if ( T_DOUBLE_ARROW !== $tokens[ $nextToken ]['code'] && T_COMMA !== $tokens[ $nextToken ]['code'] ) {
				continue;
			}

			$currentEntry = array();

			if ( T_COMMA === $tokens[ $nextToken ]['code'] ) {
				$stackPtrCount = 0;
				if ( isset( $tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
					$stackPtrCount = count( $tokens[ $stackPtr ]['nested_parenthesis'] );
				}

				$commaCount = 0;
				if ( isset( $tokens[ $nextToken ]['nested_parenthesis'] ) ) {
					$commaCount = count( $tokens[ $nextToken ]['nested_parenthesis'] );
					if ( T_ARRAY === $tokens[ $stackPtr ]['code'] ) {
						// Remove parenthesis that are used to define the array.
						$commaCount--;
					}
				}

				if ( $commaCount > $stackPtrCount ) {
					// This comma is inside more parenthesis than the ARRAY keyword,
					// then there it is actually a comma used to separate arguments
					// in a function call.
					continue;
				}

				if ( false === $keyUsed ) {
					if ( T_WHITESPACE === $tokens[ ( $nextToken - 1 ) ]['code'] ) {
						$content = $tokens[ ( $nextToken - 2 ) ]['content'];
						if ( $tokens[ ( $nextToken - 1 ) ]['content'] === $phpcsFile->eolChar ) {
							$spaceLength = 'newline';
						} else {
							$spaceLength = $tokens[ ( $nextToken - 1 ) ]['length'];
						}

						$error = 'Expected 0 spaces between "%s" and comma; %s found';
						$data  = array(
							$content,
							$spaceLength,
						);

						$fix = $phpcsFile->addFixableError( $error, $nextToken, 'SpaceBeforeComma', $data );
						if ( true === $fix ) {
							$phpcsFile->fixer->replaceToken( ( $nextToken - 1 ), '' );
						}
					}

					$valueContent = $phpcsFile->findNext(
						PHP_CodeSniffer_Tokens::$emptyTokens,
						( $lastToken + 1 ),
						$nextToken,
						true
					);

					$indices[]  = array( 'value' => $valueContent );
					$singleUsed = true;
				} // end if

				$lastToken = $nextToken;
				continue;
			} // end if

			if ( T_DOUBLE_ARROW === $tokens[ $nextToken ]['code'] ) {

				$currentEntry['arrow'] = $nextToken;
				$keyUsed               = true;

				// Find the start of index that uses this double arrow.
				$indexEnd   = $phpcsFile->findPrevious( T_WHITESPACE, ( $nextToken - 1 ), $arrayStart, true );
				$indexStart = $phpcsFile->findStartOfStatement( $indexEnd );

				if ( $indexStart === $indexEnd ) {
					$currentEntry['index']         = $indexEnd;
					$currentEntry['index_content'] = $tokens[ $indexEnd ]['content'];
				} else {
					$currentEntry['index']         = $indexStart;
					$currentEntry['index_content'] = $phpcsFile->getTokensAsString( $indexStart, ( $indexEnd - $indexStart + 1 ) );
				}

				$indexLength = strlen( $currentEntry['index_content'] );
				if ( $maxLength < $indexLength ) {
					$maxLength = $indexLength;
				}

				// Find the value of this index.
				$nextContent = $phpcsFile->findNext(
					PHP_CodeSniffer_Tokens::$emptyTokens,
					( $nextToken + 1 ),
					$arrayEnd,
					true
				);

				$currentEntry['value'] = $nextContent;
				$indices[]             = $currentEntry;
				$lastToken             = $nextToken;
			} // end if
		} // end for

		// Check for mutli-line arrays that should be single-line.
		$singleValue = false;

		if ( empty( $indices ) ) {
			$singleValue = true;
		} elseif ( 1 === count( $indices ) && T_COMMA === $tokens[ $lastToken ]['code'] ) {
			// There may be another array value without a comma.
			$exclude     = PHP_CodeSniffer_Tokens::$emptyTokens;
			$exclude[]   = T_COMMA;
			$nextContent = $phpcsFile->findNext( $exclude, ( $indices[0]['value'] + 1 ), $arrayEnd, true );
			if ( false === $nextContent ) {
				$singleValue = true;
			}
		}

		/*
			This section checks for arrays that don't specify keys.

			Arrays such as:
			   array(
				'aaa',
				'bbb',
				'd',
			   );
		*/

		if ( false === $keyUsed && ! empty( $indices ) ) {
			$count     = count( $indices );
			$lastIndex = $indices[ ( $count - 1 ) ]['value'];

			$trailingContent = $phpcsFile->findPrevious(
				PHP_CodeSniffer_Tokens::$emptyTokens,
				( $arrayEnd - 1 ),
				$lastIndex,
				true
			);

			if ( T_COMMA !== $tokens[ $trailingContent ]['code'] ) {
				$phpcsFile->recordMetric( $stackPtr, 'Array end comma', 'no' );
				$error = 'Comma required after last value in array declaration';
				$fix   = $phpcsFile->addFixableError( $error, $trailingContent, 'NoCommaAfterLast' );
				if ( true === $fix ) {
					$phpcsFile->fixer->addContent( $trailingContent, ',' );
				}
			} else {
				$phpcsFile->recordMetric( $stackPtr, 'Array end comma', 'yes' );
			}

			$lastValueLine = false;
			foreach ( $indices as $value ) {
				if ( empty( $value['value'] ) ) {
					// Array was malformed and we couldn't figure out
					// the array value correctly, so we have to ignore it.
					// Other parts of this sniff will correct the error.
					continue;
				}

				if ( false !== $lastValueLine && $tokens[ $value['value'] ]['line'] === $lastValueLine ) {
					$error = 'Each value in a multi-line array must be on a new line';
					$fix   = $phpcsFile->addFixableError( $error, $value['value'], 'ValueNoNewline' );
					if ( true === $fix ) {
						if ( T_WHITESPACE === $tokens[ ( $value['value'] - 1 ) ]['code'] ) {
							$phpcsFile->fixer->replaceToken( ( $value['value'] - 1 ), '' );
						}

						$phpcsFile->fixer->addNewlineBefore( $value['value'] );
					}
				} // end if

				$lastValueLine = $tokens[ $value['value'] ]['line'];
			} // end foreach
		} // end if

		/*
			Below the actual indentation of the array is checked.
			Errors will be thrown when a key is not aligned, when
			a double arrow is not aligned, and when a value is not
			aligned correctly.
			If an error is found in one of the above areas, then errors
			are not reported for the rest of the line to avoid reporting
			spaces and columns incorrectly. Often fixing the first
			problem will fix the other 2 anyway.

			For example:

			$a = array(
				  'index'  => '2',
				 );

			or

			$a = [
				  'index'  => '2',
				 ];

			In this array, the double arrow is indented too far, but this
			will also cause an error in the value's alignment. If the arrow were
			to be moved back one space however, then both errors would be fixed.
		 */

		$numValues = count( $indices );

		$indicesStart  = ( $keywordStart + 1 );
		$arrowStart    = ( $indicesStart + $maxLength + 1 );
		$valueStart    = ( $arrowStart + 3 );
		$indexLine     = $tokens[ $stackPtr ]['line'];
		$lastIndexLine = null;
		foreach ( $indices as $index ) {
			if ( ! isset( $index['index'] ) ) {
				// Array value only.
				if ( $tokens[ $index['value'] ]['line'] === $tokens[ $stackPtr ]['line'] && $numValues > 1 ) {
					$error = 'The first value in a multi-value array must be on a new line';
					$fix   = $phpcsFile->addFixableError( $error, $stackPtr, 'FirstValueNoNewline' );
					if ( true === $fix ) {
						$phpcsFile->fixer->addNewlineBefore( $index['value'] );
					}
				}

				continue;
			}

			$lastIndexLine = $indexLine;
			$indexLine     = $tokens[ $index['index'] ]['line'];

			if ( $indexLine === $tokens[ $stackPtr ]['line'] ) {
				$error = 'The first index in a multi-value array must be on a new line';
				$fix   = $phpcsFile->addFixableError( $error, $index['index'], 'FirstIndexNoNewline' );
				if ( true === $fix ) {
					$phpcsFile->fixer->addNewlineBefore( $index['index'] );
				}

				continue;
			}

			if ( $indexLine === $lastIndexLine ) {
				$error = 'Each index in a multi-line array must be on a new line';
				$fix   = $phpcsFile->addFixableError( $error, $index['index'], 'IndexNoNewline' );
				if ( true === $fix ) {
					if ( T_WHITESPACE === $tokens[ ( $index['index'] - 1 ) ]['code'] ) {
						$phpcsFile->fixer->replaceToken( ( $index['index'] - 1 ), '' );
					}

					$phpcsFile->fixer->addNewlineBefore( $index['index'] );
				}

				continue;
			}

			// Check each line ends in a comma.
			$valueLine = $tokens[ $index['value'] ]['line'];
			$nextComma = false;
			for ( $i = $index['value']; $i < $arrayEnd; $i++ ) {
				// Skip bracketed statements, like function calls.
				if ( T_OPEN_PARENTHESIS === $tokens[ $i ]['code'] ) {
					$i         = $tokens[ $i ]['parenthesis_closer'];
					$valueLine = $tokens[ $i ]['line'];
					continue;
				}

				if ( T_ARRAY === $tokens[ $i ]['code'] ) {
					$i         = $tokens[ $tokens[ $i ]['parenthesis_opener'] ]['parenthesis_closer'];
					$valueLine = $tokens[ $i ]['line'];
					continue;
				}

				if ( T_OPEN_SHORT_ARRAY === $tokens[ $i ]['code'] ) {
					$i         = $tokens[ $i ]['bracket_closer'];
					$valueLine = $tokens[ $i ]['line'];
					continue;
				}

				if ( T_CLOSURE === $tokens[ $i ]['code'] ) {
					$i         = $tokens[ $i ]['scope_closer'];
					$valueLine = $tokens[ $i ]['line'];
					continue;
				}

				if ( T_COMMA === $tokens[ $i ]['code'] ) {
					$nextComma = $i;
					break;
				}
			} // End for.

			if ( false === $nextComma ) {
				$error = 'Each line in an array declaration must end in a comma';
				$fix   = $phpcsFile->addFixableError( $error, $index['value'], 'NoComma' );

				if ( true === $fix ) {
					// Find the end of the line and put a comma there.
					for ( $i = ( $index['value'] + 1 ); $i < $phpcsFile->numTokens; $i++ ) {
						if ( $tokens[ $i ]['line'] > $valueLine ) {
							break;
						}
					}

					$phpcsFile->fixer->addContentBefore( ( $i - 1 ), ',' );
				}
			}

			// Check that there is no space before the comma.
			if ( false !== $nextComma && T_WHITESPACE === $tokens[ ( $nextComma - 1 ) ]['code'] ) {
				$content     = $tokens[ ( $nextComma - 2 ) ]['content'];
				$spaceLength = $tokens[ ( $nextComma - 1 ) ]['length'];
				$error       = 'Expected 0 spaces between "%s" and comma; %s found';
				$data        = array(
					$content,
					$spaceLength,
				);

				$fix = $phpcsFile->addFixableError( $error, $nextComma, 'SpaceBeforeComma', $data );
				if ( true === $fix ) {
					$phpcsFile->fixer->replaceToken( ( $nextComma - 1 ), '' );
				}
			}
		} // end foreach

	} // end processMultiLineArray()

} // End class.
