<?php
/**
 * Enforces WordPress array format, based upon Squiz code
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   John Godley <john@urbangiraffe.com>
 * @author   Greg Sherwood <gsherwood@squiz.net>
 * @author   Marc McIntyre <mmcintyre@squiz.net>
 */

/**
 * Enforces WordPress array format
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   John Godley <john@urbangiraffe.com>
 * @author   Greg Sherwood <gsherwood@squiz.net>
 * @author   Marc McIntyre <mmcintyre@squiz.net>
 */
class WordPress_Sniffs_Arrays_ArrayDeclarationSniff extends Squiz_Sniffs_Arrays_ArrayDeclarationSniff {

	/**
	 * @since 0.5.0
	 */
	public function processSingleLineArray( PHP_CodeSniffer_File $phpcsFile, $stackPtr, $arrayStart, $arrayEnd ) {

		parent::processSingleLineArray( $phpcsFile, $stackPtr, $arrayStart, $arrayEnd );

		// This array is empty, so the below checks aren't necesary.
		if ( $arrayStart + 1 === $arrayEnd ) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		// Check that there is a single space after the array opener.
		if ( T_WHITESPACE !== $tokens[ $arrayStart + 1 ]['code'] ) {

			$warning = 'Missing space after array opener.';
			$fix = $phpcsFile->addFixableError( $warning, $arrayStart, 'NoSpaceAfterOpenParenthesis' );

			if ( $fix ) {
				$phpcsFile->fixer->addContent( $arrayStart, ' ' );
			}

		} elseif ( ' ' !== $tokens[ $arrayStart + 1 ]['content'] ) {

			$fix = $phpcsFile->addFixableError(
				'Expected 1 space after array opener, found %s.',
				$arrayStart,
				'SpaceAfterArrayOpener',
				strlen( $tokens[ $arrayStart + 1 ]['content'] )
			);

			if ( $fix ) {
				$phpcsFile->fixer->replaceToken( $arrayStart + 1, ' ' );
			}
		}

		if ( T_WHITESPACE !== $tokens[ $arrayEnd - 1 ]['code'] ) {

			$warning = 'Missing space before array closer.';
			$fix = $phpcsFile->addFixableError( $warning, $arrayEnd, 'NoSpaceAfterOpenParenthesis' );

			if ( $fix ) {
				$phpcsFile->fixer->addContentBefore( $arrayEnd, ' ' );
			}

		} elseif ( ' ' !== $tokens[ $arrayEnd - 1 ]['content'] ) {

			$fix = $phpcsFile->addFixableError(
				'Expected 1 space before array closer, found %s.',
				$arrayEnd,
				'SpaceAfterArrayCloser',
				strlen( $tokens[ $arrayEnd - 1 ]['content'] )
			);

			if ( $fix ) {
				$phpcsFile->fixer->replaceToken( $arrayEnd - 1, ' ' );
			}
		}
	}


	/**
	 * @since 0.5.0
	 */
	public function processMultiLineArray( PHP_CodeSniffer_File $phpcsFile, $stackPtr, $arrayStart, $arrayEnd ) {
		$tokens       = $phpcsFile->getTokens();
		$keywordStart = $tokens[$stackPtr]['column'];

		// Check the closing bracket is on a new line.
		$lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($arrayEnd - 1), $arrayStart, true);
		if ($tokens[$lastContent]['line'] === $tokens[$arrayEnd]['line']) {
			$error = 'Closing parenthesis of array declaration must be on a new line';
			$fix   = $phpcsFile->addFixableError($error, $arrayEnd, 'CloseBraceNewLine');
			if ($fix === true) {
				$phpcsFile->fixer->addNewlineBefore($arrayEnd);
			}
			/*
		} else if ($tokens[$arrayEnd]['column'] !== $keywordStart) {
			// Check the closing bracket is lined up under the "a" in array.
			$expected = ($keywordStart - 1);
			$found    = ($tokens[$arrayEnd]['column'] - 1);
			$error    = 'Closing parenthesis not aligned correctly; expected %s space(s) but found %s';
			$data     = array(
				$expected,
				$found,
			);

			$fix = $phpcsFile->addFixableError($error, $arrayEnd, 'CloseBraceNotAligned', $data);
			if ($fix === true) {
				if ($found === 0) {
					$phpcsFile->fixer->addContent(($arrayEnd - 1), str_repeat(' ', $expected));
				} else {
					$phpcsFile->fixer->replaceToken(($arrayEnd - 1), str_repeat(' ', $expected));
				}
			}
			*/
		}//end if

		$nextToken  = $stackPtr;
		$keyUsed    = false;
		$singleUsed = false;
		$indices    = array();
		$maxLength  = 0;

		if ($tokens[$stackPtr]['code'] === T_ARRAY) {
			$lastToken = $tokens[$stackPtr]['parenthesis_opener'];
		} else {
			$lastToken = $stackPtr;
		}

		// Find all the double arrows that reside in this scope.
		for ($nextToken = ($stackPtr + 1); $nextToken < $arrayEnd; $nextToken++) {
			// Skip bracketed statements, like function calls.
			if ($tokens[$nextToken]['code'] === T_OPEN_PARENTHESIS
			    && (isset($tokens[$nextToken]['parenthesis_owner']) === false
			        || $tokens[$nextToken]['parenthesis_owner'] !== $stackPtr)
			) {
				$nextToken = $tokens[$nextToken]['parenthesis_closer'];
				continue;
			}

			if ($tokens[$nextToken]['code'] === T_ARRAY) {
				// Let subsequent calls of this test handle nested arrays.
				if ($tokens[$lastToken]['code'] !== T_DOUBLE_ARROW) {
					$indices[] = array('value' => $nextToken);
					$lastToken = $nextToken;
				}

				$nextToken = $tokens[$tokens[$nextToken]['parenthesis_opener']]['parenthesis_closer'];
				$nextToken = $phpcsFile->findNext(T_WHITESPACE, ($nextToken + 1), null, true);
				if ($tokens[$nextToken]['code'] !== T_COMMA) {
					$nextToken--;
				} else {
					$lastToken = $nextToken;
				}

				continue;
			}

			if ($tokens[$nextToken]['code'] === T_OPEN_SHORT_ARRAY) {
				// Let subsequent calls of this test handle nested arrays.
				if ($tokens[$lastToken]['code'] !== T_DOUBLE_ARROW) {
					$indices[] = array('value' => $nextToken);
					$lastToken = $nextToken;
				}

				$nextToken = $tokens[$nextToken]['bracket_closer'];
				$nextToken = $phpcsFile->findNext(T_WHITESPACE, ($nextToken + 1), null, true);
				if ($tokens[$nextToken]['code'] !== T_COMMA) {
					$nextToken--;
				} else {
					$lastToken = $nextToken;
				}

				continue;
			}

			if ($tokens[$nextToken]['code'] === T_CLOSURE) {
				if ($tokens[$lastToken]['code'] !== T_DOUBLE_ARROW) {
					$indices[] = array('value' => $nextToken);
					$lastToken = $nextToken;
				}

				$nextToken = $tokens[$nextToken]['scope_closer'];
				$nextToken = $phpcsFile->findNext(T_WHITESPACE, ($nextToken + 1), null, true);
				if ($tokens[$nextToken]['code'] !== T_COMMA) {
					$nextToken--;
				} else {
					$lastToken = $nextToken;
				}

				continue;
			}

			if ($tokens[$nextToken]['code'] !== T_DOUBLE_ARROW
			    && $tokens[$nextToken]['code'] !== T_COMMA
			) {
				continue;
			}

			$currentEntry = array();

			if ($tokens[$nextToken]['code'] === T_COMMA) {
				$stackPtrCount = 0;
				if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
					$stackPtrCount = count($tokens[$stackPtr]['nested_parenthesis']);
				}

				$commaCount = 0;
				if (isset($tokens[$nextToken]['nested_parenthesis']) === true) {
					$commaCount = count($tokens[$nextToken]['nested_parenthesis']);
					if ($tokens[$stackPtr]['code'] === T_ARRAY) {
						// Remove parenthesis that are used to define the array.
						$commaCount--;
					}
				}

				if ($commaCount > $stackPtrCount) {
					// This comma is inside more parenthesis than the ARRAY keyword,
					// then there it is actually a comma used to separate arguments
					// in a function call.
					continue;
				}

				/*
				if ($keyUsed === true && $tokens[$lastToken]['code'] === T_COMMA) {
					$error = 'No key specified for array entry; first entry specifies key';
					$phpcsFile->addError($error, $nextToken, 'NoKeySpecified');
					return;
				}
				*/

				if ($keyUsed === false) {
					if ($tokens[($nextToken - 1)]['code'] === T_WHITESPACE) {
						$content = $tokens[($nextToken - 2)]['content'];
						if ($tokens[($nextToken - 1)]['content'] === $phpcsFile->eolChar) {
							$spaceLength = 'newline';
						} else {
							$spaceLength = $tokens[($nextToken - 1)]['length'];
						}

						$error = 'Expected 0 spaces between "%s" and comma; %s found';
						$data  = array(
							$content,
							$spaceLength,
						);

						$fix = $phpcsFile->addFixableError($error, $nextToken, 'SpaceBeforeComma', $data);
						if ($fix === true) {
							$phpcsFile->fixer->replaceToken(($nextToken - 1), '');
						}
					}

					$valueContent = $phpcsFile->findNext(
						PHP_CodeSniffer_Tokens::$emptyTokens,
						($lastToken + 1),
						$nextToken,
						true
					);

					$indices[]  = array('value' => $valueContent);
					$singleUsed = true;
				}//end if

				$lastToken = $nextToken;
				continue;
			}//end if


			if ($tokens[$nextToken]['code'] === T_DOUBLE_ARROW) {
				/*
				if ($singleUsed === true) {
					$error = 'Key specified for array entry; first entry has no key';
					$phpcsFile->addError($error, $nextToken, 'KeySpecified');
					return;
				}
				*/

				$currentEntry['arrow'] = $nextToken;
				$keyUsed = true;

				// Find the start of index that uses this double arrow.
				$indexEnd   = $phpcsFile->findPrevious(T_WHITESPACE, ($nextToken - 1), $arrayStart, true);
				$indexStart = $phpcsFile->findStartOfStatement($indexEnd);

				if ($indexStart === $indexEnd) {
					$currentEntry['index']         = $indexEnd;
					$currentEntry['index_content'] = $tokens[$indexEnd]['content'];
				} else {
					$currentEntry['index']         = $indexStart;
					$currentEntry['index_content'] = $phpcsFile->getTokensAsString($indexStart, ($indexEnd - $indexStart + 1));
				}

				$indexLength = strlen($currentEntry['index_content']);
				if ($maxLength < $indexLength) {
					$maxLength = $indexLength;
				}

				// Find the value of this index.
				$nextContent = $phpcsFile->findNext(
					PHP_CodeSniffer_Tokens::$emptyTokens,
					($nextToken + 1),
					$arrayEnd,
					true
				);

				$currentEntry['value'] = $nextContent;
				$indices[] = $currentEntry;
				$lastToken = $nextToken;
			}//end if
		}//end for

		// Check for mutli-line arrays that should be single-line.
		$singleValue = false;

		if (empty($indices) === true) {
			$singleValue = true;
		} else if (count($indices) === 1 && $tokens[$lastToken]['code'] === T_COMMA) {
			// There may be another array value without a comma.
			$exclude     = PHP_CodeSniffer_Tokens::$emptyTokens;
			$exclude[]   = T_COMMA;
			$nextContent = $phpcsFile->findNext($exclude, ($indices[0]['value'] + 1), $arrayEnd, true);
			if ($nextContent === false) {
				$singleValue = true;
			}
		}

		/*
		if ($singleValue === true) {
			// Array cannot be empty, so this is a multi-line array with
			// a single value. It should be defined on single line.
			$error = 'Multi-line array contains a single value; use single-line array instead';
			$fix   = $phpcsFile->addFixableError($error, $stackPtr, 'MultiLineNotAllowed');

			if ($fix === true) {
				$phpcsFile->fixer->beginChangeset();
				for ($i = ($arrayStart + 1); $i < $arrayEnd; $i++) {
					if ($tokens[$i]['code'] !== T_WHITESPACE) {
						break;
					}

					$phpcsFile->fixer->replaceToken($i, '');
				}

				for ($i = ($arrayEnd - 1); $i > $arrayStart; $i--) {
					if ($tokens[$i]['code'] !== T_WHITESPACE) {
						break;
					}

					$phpcsFile->fixer->replaceToken($i, '');
				}

				$phpcsFile->fixer->endChangeset();
			}

			return;
		}//end if
		*/

		/*
			This section checks for arrays that don't specify keys.

			Arrays such as:
			   array(
				'aaa',
				'bbb',
				'd',
			   );
		*/

		if ($keyUsed === false && empty($indices) === false) {
			$count     = count($indices);
			$lastIndex = $indices[($count - 1)]['value'];

			$trailingContent = $phpcsFile->findPrevious(
				PHP_CodeSniffer_Tokens::$emptyTokens,
				($arrayEnd - 1),
				$lastIndex,
				true
			);

			if ($tokens[$trailingContent]['code'] !== T_COMMA) {
				$phpcsFile->recordMetric($stackPtr, 'Array end comma', 'no');
				$error = 'Comma required after last value in array declaration';
				$fix   = $phpcsFile->addFixableError($error, $trailingContent, 'NoCommaAfterLast');
				if ($fix === true) {
					$phpcsFile->fixer->addContent($trailingContent, ',');
				}
			} else {
				$phpcsFile->recordMetric($stackPtr, 'Array end comma', 'yes');
			}

			$lastValueLine = false;
			foreach ($indices as $value) {
				if (empty($value['value']) === true) {
					// Array was malformed and we couldn't figure out
					// the array value correctly, so we have to ignore it.
					// Other parts of this sniff will correct the error.
					continue;
				}

				if ($lastValueLine !== false && $tokens[$value['value']]['line'] === $lastValueLine) {
					$error = 'Each value in a multi-line array must be on a new line';
					$fix   = $phpcsFile->addFixableError($error, $value['value'], 'ValueNoNewline');
					if ($fix === true) {
						if ($tokens[($value['value'] - 1)]['code'] === T_WHITESPACE) {
							$phpcsFile->fixer->replaceToken(($value['value'] - 1), '');
						}

						$phpcsFile->fixer->addNewlineBefore($value['value']);
					}
					/*
				} else if ($tokens[($value['value'] - 1)]['code'] === T_WHITESPACE) {
					$expected = $keywordStart;

					$first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $value['value'], true);
					$found = ($tokens[$first]['column'] - 1);
					if ($found !== $expected) {
						$error = 'Array value not aligned correctly; expected %s spaces but found %s';
						$data  = array(
							$expected,
							$found,
						);

						$fix = $phpcsFile->addFixableError($error, $value['value'], 'ValueNotAligned', $data);
						if ($fix === true) {
							if ($found === 0) {
								$phpcsFile->fixer->addContent(($value['value'] - 1), str_repeat(' ', $expected));
							} else {
								$phpcsFile->fixer->replaceToken(($value['value'] - 1), str_repeat(' ', $expected));
							}
						}
					}
					*/
				}//end if

				$lastValueLine = $tokens[$value['value']]['line'];
			}//end foreach
		}//end if

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

		$numValues = count($indices);

		$indicesStart  = ($keywordStart + 1);
		$arrowStart    = ($indicesStart + $maxLength + 1);
		$valueStart    = ($arrowStart + 3);
		$indexLine     = $tokens[$stackPtr]['line'];
		$lastIndexLine = null;
		foreach ($indices as $index) {
			if (isset($index['index']) === false) {
				// Array value only.
				if ($tokens[$index['value']]['line'] === $tokens[$stackPtr]['line'] && $numValues > 1) {
					$error = 'The first value in a multi-value array must be on a new line';
					$fix   = $phpcsFile->addFixableError($error, $stackPtr, 'FirstValueNoNewline');
					if ($fix === true) {
						$phpcsFile->fixer->addNewlineBefore($index['value']);
					}
				}

				continue;
			}

			$lastIndexLine = $indexLine;
			$indexLine     = $tokens[$index['index']]['line'];

			if ($indexLine === $tokens[$stackPtr]['line']) {
				$error = 'The first index in a multi-value array must be on a new line';
				$fix   = $phpcsFile->addFixableError($error, $index['index'], 'FirstIndexNoNewline');
				if ($fix === true) {
					$phpcsFile->fixer->addNewlineBefore($index['index']);
				}

				continue;
			}

			if ($indexLine === $lastIndexLine) {
				$error = 'Each index in a multi-line array must be on a new line';
				$fix   = $phpcsFile->addFixableError($error, $index['index'], 'IndexNoNewline');
				if ($fix === true) {
					if ($tokens[($index['index'] - 1)]['code'] === T_WHITESPACE) {
						$phpcsFile->fixer->replaceToken(($index['index'] - 1), '');
					}

					$phpcsFile->fixer->addNewlineBefore($index['index']);
				}

				continue;
			}

			/*
			if ($tokens[$index['index']]['column'] !== $indicesStart) {
				$expected = ($indicesStart - 1);
				$found    = ($tokens[$index['index']]['column'] - 1);
				$error    = 'Array key not aligned correctly; expected %s spaces but found %s';
				$data     = array(
					$expected,
					$found,
				);

				$fix = $phpcsFile->addFixableError($error, $index['index'], 'KeyNotAligned', $data);
				if ($fix === true) {
					if ($found === 0) {
						$phpcsFile->fixer->addContent(($index['index'] - 1), str_repeat(' ', $expected));
					} else {
						$phpcsFile->fixer->replaceToken(($index['index'] - 1), str_repeat(' ', $expected));
					}
				}

				continue;
			}

			if ($tokens[$index['arrow']]['column'] !== $arrowStart) {
				$expected = ($arrowStart - (strlen($index['index_content']) + $tokens[$index['index']]['column']));
				$found    = ($tokens[$index['arrow']]['column'] - (strlen($index['index_content']) + $tokens[$index['index']]['column']));
				$error    = 'Array double arrow not aligned correctly; expected %s space(s) but found %s';
				$data     = array(
					$expected,
					$found,
				);

				$fix = $phpcsFile->addFixableError($error, $index['arrow'], 'DoubleArrowNotAligned', $data);
				if ($fix === true) {
					if ($found === 0) {
						$phpcsFile->fixer->addContent(($index['arrow'] - 1), str_repeat(' ', $expected));
					} else {
						$phpcsFile->fixer->replaceToken(($index['arrow'] - 1), str_repeat(' ', $expected));
					}
				}

				continue;
			}

			if ($tokens[$index['value']]['column'] !== $valueStart) {
				$expected = ($valueStart - ($tokens[$index['arrow']]['length'] + $tokens[$index['arrow']]['column']));
				$found    = ($tokens[$index['value']]['column'] - ($tokens[$index['arrow']]['length'] + $tokens[$index['arrow']]['column']));
				if ($found < 0) {
					$found = 'newline';
				}

				$error = 'Array value not aligned correctly; expected %s space(s) but found %s';
				$data  = array(
					$expected,
					$found,
				);

				$fix = $phpcsFile->addFixableError($error, $index['arrow'], 'ValueNotAligned', $data);
				if ($fix === true) {
					if ($found === 'newline') {
						$prev = $phpcsFile->findPrevious(T_WHITESPACE, ($index['value'] - 1), null, true);
						$phpcsFile->fixer->beginChangeset();
						for ($i = ($prev + 1); $i < $index['value']; $i++) {
							$phpcsFile->fixer->replaceToken($i, '');
						}

						$phpcsFile->fixer->replaceToken(($index['value'] - 1), str_repeat(' ', $expected));
						$phpcsFile->fixer->endChangeset();
					} else if ($found === 0) {
						$phpcsFile->fixer->addContent(($index['value'] - 1), str_repeat(' ', $expected));
					} else {
						$phpcsFile->fixer->replaceToken(($index['value'] - 1), str_repeat(' ', $expected));
					}
				}
			}//end if
			*/

			// Check each line ends in a comma.
			$valueLine = $tokens[$index['value']]['line'];
			$nextComma = false;
			for ($i = $index['value']; $i < $arrayEnd; $i++) {
				// Skip bracketed statements, like function calls.
				if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
					$i         = $tokens[$i]['parenthesis_closer'];
					$valueLine = $tokens[$i]['line'];
					continue;
				}

				if ($tokens[$i]['code'] === T_ARRAY) {
					$i         = $tokens[$tokens[$i]['parenthesis_opener']]['parenthesis_closer'];
					$valueLine = $tokens[$i]['line'];
					continue;
				}

				if ($tokens[$i]['code'] === T_OPEN_SHORT_ARRAY) {
					$i         = $tokens[$i]['bracket_closer'];
					$valueLine = $tokens[$i]['line'];
					continue;
				}

				if ($tokens[$i]['code'] === T_CLOSURE) {
					$i         = $tokens[$i]['scope_closer'];
					$valueLine = $tokens[$i]['line'];
					continue;
				}

				if ($tokens[$i]['code'] === T_COMMA) {
					$nextComma = $i;
					break;
				}
			}//end for

			//if ($nextComma === false || ($tokens[$nextComma]['line'] !== $valueLine)) {
			if ( $nextComma === false ) {
				$error = 'Each line in an array declaration must end in a comma';
				$fix   = $phpcsFile->addFixableError($error, $index['value'], 'NoComma');

				if ($fix === true) {
					// Find the end of the line and put a comma there.
					for ($i = ($index['value'] + 1); $i < $phpcsFile->numTokens; $i++) {
						if ($tokens[$i]['line'] > $valueLine) {
							break;
						}
					}

					$phpcsFile->fixer->addContentBefore(($i - 1), ',');
				}
			}

			// Check that there is no space before the comma.
			if ($nextComma !== false && $tokens[($nextComma - 1)]['code'] === T_WHITESPACE) {
				$content     = $tokens[($nextComma - 2)]['content'];
				$spaceLength = $tokens[($nextComma - 1)]['length'];
				$error       = 'Expected 0 spaces between "%s" and comma; %s found';
				$data        = array(
					$content,
					$spaceLength,
				);

				$fix = $phpcsFile->addFixableError($error, $nextComma, 'SpaceBeforeComma', $data);
				if ($fix === true) {
					$phpcsFile->fixer->replaceToken(($nextComma - 1), '');
				}
			}
		}//end foreach

	}//end processMultiLineArray()

}//end class
