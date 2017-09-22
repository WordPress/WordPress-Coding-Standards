<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\Formatting;

use WordPress\Sniff;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Enforce ordering of namespace `use` statements as per the draft PSR-12.
 *
 * "If present, each of the blocks below MUST be separated by a single blank line,
 *  and MUST NOT contain a blank line.
 *  Each block MUST be in the order listed below, although blocks that are
 *  not relevant may be omitted."
 *
 * - One or more class-based use import statements.
 * - One or more function-based use import statements.
 * - One or more constant-based use import statements.
 *
 * @link    https://github.com/php-fig/fig-standards/blob/master/proposed/extended-coding-style-guide.md
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 *
 * {@internal Note: all the order related error messages have the same error code.
 *            As the fixer has to batch fix everything in one go as the new positions
 *            of statements can't yet be determined at the moment the errors are being thrown,
 *            disabling parts of the sniff via error codes would not work when using
 *            the fixer. Having the same errorcode prevents confusion about this.}}
 *
 * {@internal This sniff is a candidate for pulling upstream.}}
 */
class NamespaceUseStatementOrderSniff extends Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since 0.14.0
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_USE,
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {
		/*
		 * Find all namespace use statements and examine them.
		 */
		$usePtr = $stackPtr;
		$errors = 0;
		$fixes  = array();

		/*
		 * Complex array to store the found use statements.
		 *
		 * Primary key:   The stackPtr to the namespace the use statement belongs with.
		 * Secondary key: Statement type: class|function|const.
		 * Ternary key:   The stackPtr to the start of the use statement including
		 *                indentation and/or preceding comments.
		 *    Value:      The stackPtr to the end of the use statement including trailing comment(s).
		 */
		$use_statements     = array();
		$expected_start_pos = array();

		do {
			$use_type = $this->get_use_type( $usePtr );

			if ( 'class' !== $use_type ) {
				$usePtr = $this->phpcsFile->findNext( T_USE, ( $usePtr + 1 ) );
				continue;
			}

			/*
			 * Find which namespace this statement belongs with.
			 */
			$ns_token = $this->determine_namespace( $usePtr, true );
			if ( false === $ns_token ) {
				$ns_token = 'none'; // Prevent array key confusion.
			}

			/*
			 * Determine the class use type.
			 */
			$next = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $usePtr + 1 ), null, true );
			$type = 'class';
			if ( false !== $next && T_STRING === $this->tokens[ $next ]['code'] ) {
				if ( 'function' === $this->tokens[ $next ]['content'] ) {
					$type = 'function';
				} elseif ( 'const' === $this->tokens[ $next ]['content'] ) {
					$type = 'const';
				}
			}
			unset( $next );

			/*
			 * Find the start and end tokens for the use statement.
			 */
			$start = $this->find_start_of_use_statement( $usePtr );
			$end   = $this->find_end_of_use_statement( $usePtr );

			if ( false === $end ) {
				$usePtr = $this->phpcsFile->findNext( T_USE, ( $usePtr + 1 ) );
				continue;
			}

			if ( ! isset( $use_statements[ $ns_token ] ) ) {
				// Initialize the array.
				$use_statements[ $ns_token ] = array(
					'class'    => array(),
					'function' => array(),
					'const'    => array(),
				);

				$expected_start_pos[ $ns_token ] = $start;

				if ( 'none' !== $ns_token ) {
					/*
					 * Ok, this is the first use statement found for this namespace.
					 * Is it the first content after the namespace declaration ?
					 */
					$end_ns = $this->phpcsFile->findNext(
						array( T_SEMICOLON, T_OPEN_CURLY_BRACKET ),
						( $ns_token + 1 ),
						$usePtr
					);

					if ( false !== $end_ns ) {
						$next_non_empty = $this->phpcsFile->findNext(
							Tokens::$emptyTokens,
							( $end_ns + 1 ),
							null,
							true
						);

						if ( false !== $next_non_empty ) {
							if ( $next_non_empty !== $usePtr ) {
								$errors++;
								$fixes[] = $this->phpcsFile->addFixableError(
									'The first use statement should be the first content after the namespace statement.',
									$usePtr,
									'UseStatementOrder'
								);
							}

							$expected_start_pos[ $ns_token ] = $next_non_empty;
						}
					}
				}
			} elseif ( ! empty( $use_statements[ $ns_token ][ $type ] ) ) {
				/*
				 * Check whether this use statement directly follows the previous
				 * use statement of the same type.
				 */
				$end_prev = end( $use_statements[ $ns_token ][ $type ] );

				if ( ( $this->tokens[ $end_prev ]['line'] + 1 ) !== $this->tokens[ $start ]['line'] ) {

					$errors++;
					$fixes[] = $this->phpcsFile->addFixableError(
						'Each "use %s" statement should directly follow the previous "use %s" statement. Previous statement ended on line %s.',
						$usePtr,
						'UseStatementOrder',
						array( $type, $type, $this->tokens[ $end_prev ]['line'] )
					);
				}
			}

			$function_use_count = count( $use_statements[ $ns_token ]['function'] );
			$const_use_count    = count( $use_statements[ $ns_token ]['const'] );

			if ( 'class' === $type
				&& ( $function_use_count + $const_use_count ) > 0
			) {
				$errors++;
				$fixes[] = $this->phpcsFile->addFixableError(
					'Found a "use class" statement after a "use function" or "use const" statement.',
					$usePtr,
					'UseStatementOrder'
				);
			} elseif ( 'function' === $type && $const_use_count > 0 ) {
				$errors++;
				$fixes[] = $this->phpcsFile->addFixableError(
					'Found a "use function" statement after a "use const" statement.',
					$usePtr,
					'UseStatementOrder'
				);
			}

			$use_statements[ $ns_token ][ $type ][ $start ] = $end;

			$usePtr = $this->phpcsFile->findNext( T_USE, ( $usePtr + 1 ) );

		} while ( false !== $usePtr && $usePtr < $this->phpcsFile->numTokens );

		/*
		 * Batch fix all the use statements.
		 *
		 * As both the order and the start position may change, batching all
		 * fixes together looks to be the only way to avoid fixer conflicts
		 * and/or running out of fixer loops.
		 */
		$fixes = array_filter( $fixes ); // Remove 'false's.
		if ( $errors > 0 && count( $fixes ) === $errors ) {
			$this->batch_fix_statement_order( $use_statements, $expected_start_pos );

			// Fix the blank lines in the next fixer round as they would be incorrect/conflicting now anyhow.
			return $this->phpcsFile->numTokens;
		}

		/*
		 * Check for a blank line before the first and after the last use statement in each block.
		 *
		 * If the uase statement order was changed too, the fixers here will run in a subsequent loop.
		 */
		foreach ( $use_statements as $ns_token => $types ) {

			foreach ( $types as $type => $statements ) {
				if ( empty( $statements ) ) {
					continue;
				}

				$this->check_blank_line_before_group( $statements, $type );
				$this->check_blank_line_after_group( $statements, $type );
			}
		}

		return $this->phpcsFile->numTokens;

	} // End process_token().


	/**
	 * Batch fix all the use statements.
	 *
	 * As both the order and the start position may change, batching
	 * all fixes together looks to be the only way to avoid fixer conflicts
	 * and/or running out of fixer loops.
	 *
	 * @param array $use_statements     Array with info on all namespace use statements
	 *                                  found in the file.
	 * @param array $expected_start_pos Array with expected start position of use
	 *                                  statements per namespace.
	 */
	protected function batch_fix_statement_order( $use_statements, $expected_start_pos ) {

		$this->phpcsFile->fixer->beginChangeset();

		foreach ( $use_statements as $ns_token => $types ) {

			$replace     = false;
			$replacement = '';

			foreach ( $types as $type => $statements ) {
				if ( empty( $statements ) ) {
					continue;
				}

				foreach ( $statements as $start => $end ) {
					/*
					 * {@internal Once upstream PR #1674 has been merged and the WPCS minimum
					 * PHPCS requirement has gone up to the version which contains that change,
					 * the third parameter `true` should be added to this function call and
					 * the unit test `fixed` files should be updated to reflect the improvement.
					 * Setting the tab-width in the unit test file can then also be removed.}}
					 * @link https://github.com/squizlabs/PHP_CodeSniffer/pull/1674
					 */
					$replacement .= $this->phpcsFile->getTokensAsString( $start, ( ( $end - $start ) + 1 ) );

					if ( false === strpos( $this->tokens[ $end ]['content'], $this->phpcsFile->eolChar ) ) {
						$replacement .= $this->phpcsFile->eolChar;
					}

					for ( $i = $start; $i <= $end; $i++ ) {
						if ( $i === $expected_start_pos[ $ns_token ] ) {
							$replace = true;
							continue;
						}

						$this->phpcsFile->fixer->replaceToken( $i, '' );
					}
					unset( $i );
				}
			}

			// Add the complete block of use statements in the correct order below the namespace declaration.
			if ( true === $replace ) {
				$this->phpcsFile->fixer->replaceToken( $expected_start_pos[ $ns_token ], $replacement );
			} else {
				$this->phpcsFile->fixer->addContentBefore( $expected_start_pos[ $ns_token ], $replacement );
			}
		}

		$this->phpcsFile->fixer->endChangeset();
	}

	/**
	 * Check for a blank line before the first statement in a block.
	 *
	 * @param array  $statements Array with start/end pointers of each use statement in a group.
	 * @param string $type       Use statement group type.
	 */
	protected function check_blank_line_before_group( $statements, $type ) {
		reset( $statements );

		$start        = key( $statements );
		$prev_content = $this->phpcsFile->findPrevious( T_WHITESPACE, ( $start - 1 ), null, true );
		if ( false === $prev_content ) {
			return;
		}

		$diff = ( $this->tokens[ $start ]['line'] - $this->tokens[ $prev_content ]['line'] );
		if ( 2 === $diff ) {
			return;
		}
		if ( $diff < 0 ) {
			$diff = 0;
		}

		$error = 'There must be exactly one blank line before a "use %s" statement group.';
		$data  = array( $type );
		$fix   = $this->phpcsFile->addFixableError( $error, $start, 'BlankLineBeforeGroup', $data );

		if ( true === $fix ) {
			switch ( $diff ) {
				case 0:
					$this->phpcsFile->fixer->addContentBefore(
						$start,
						$this->phpcsFile->eolChar . $this->phpcsFile->eolChar
					);
					break;

				case 1:
					$this->phpcsFile->fixer->addContentBefore(
						$start,
						$this->phpcsFile->eolChar
					);
					break;

				default:
					$this->phpcsFile->fixer->beginChangeset();
					for ( $i = ( $prev_content + 1 ); $i < $start; $i++ ) {
						if ( $this->tokens[ $i ]['line'] === $this->tokens[ $start ]['line'] ) {
							break;
						}

						$this->phpcsFile->fixer->replaceToken( $i, '' );
					}
					unset( $i );

					$this->phpcsFile->fixer->addContentBefore(
						$start,
						$this->phpcsFile->eolChar . $this->phpcsFile->eolChar
					);
					$this->phpcsFile->fixer->endChangeset();
					break;
			}
		}
	}

	/**
	 * Check for a blank line after the last statement in a block.
	 *
	 * We check for a minimum of one blank line and allow for two.
	 * If this is between two groups of use statement blocks, superfluous blank
	 * lines will be corrected by the "before" fixer of the next block.
	 * If this is at the end of all the use statements, allowing for more
	 * than one blank line will prevent conflicts with standards demanding
	 * two blank lines before classes/functions.
	 *
	 * @param array  $statements Array with start/end pointers of each use statement in a group.
	 * @param string $type       Use statement group type.
	 */
	protected function check_blank_line_after_group( $statements, $type ) {
		$end          = end( $statements );
		$next_content = $this->phpcsFile->findNext(
			T_WHITESPACE,
			( $end + 1 ),
			$this->phpcsFile->numTokens,
			true
		);
		if ( false === $next_content ) {
			return;
		}

		$diff = ( $this->tokens[ $next_content ]['line'] - $this->tokens[ ( $end + 1 ) ]['line'] );
		if ( 1 === $diff || 2 === $diff ) {
			return;
		}
		if ( $diff < 0 ) {
			$diff = 0;
		}

		$error = 'There must be a blank line after the last statement in a "use %s" statement group.';
		$data  = array( $type );
		$fix   = $this->phpcsFile->addFixableError( $error, $end, 'BlankLineAfterGroup', $data );

		if ( true === $fix ) {
			if ( 0 === $diff ) {
				$this->phpcsFile->fixer->addNewlineBefore( $end + 1 );
			} else {
				$this->phpcsFile->fixer->beginChangeset();

				for ( $i = ( $end + 1 ); $i < $next_content; $i++ ) {
					if ( $this->tokens[ $i ]['line'] === $this->tokens[ $next_content ]['line'] ) {
						break;
					}

					$this->phpcsFile->fixer->replaceToken( $i, '' );
				}

				$this->phpcsFile->fixer->addNewline( $end );
				$this->phpcsFile->fixer->endChangeset();
			}
		}
	}

	/**
	 * Find what should be regarded as the start of the statement.
	 *
	 * Comments directly before or above the statement should be included.
	 * Same goes for indentation before the use statement.
	 *
	 * @param int $usePtr Stack pointer to the use keyword.
	 *
	 * @return int
	 */
	protected function find_start_of_use_statement( $usePtr ) {
		$last_newline = null;

		for ( $i = ( $usePtr - 1 ); $i >= 0; $i-- ) {

			if ( isset( Tokens::$emptyTokens[ $this->tokens[ $i ]['code'] ] ) ) {
				if ( false !== strpos( $this->tokens[ $i ]['content'], $this->phpcsFile->eolChar ) ) {
					if ( 1 === $this->tokens[ $i ]['column']
						&& T_WHITESPACE === $this->tokens[ $i ]['code']
						&& isset( $this->tokens[ ( $i - 1 ) ] )
						&& $this->tokens[ $i ]['line'] !== $this->tokens[ ( $i - 1 ) ]['line']
					) {
						// Blank line found.
						break;
					}
					$last_newline = $i;
				}
				continue;
			}

			/*
			 * Non empty token found.
			 */
			if ( ! isset( $last_newline )
				&& $this->tokens[ $i ]['line'] === $this->tokens[ ( $i + 1 ) ]['line']
				&& T_WHITESPACE === $this->tokens[ ( $i + 1 ) ]['code']
			) {
				// Deal with multiple statements on one line.
				++$i;
				break;
			}

			if ( isset( $last_newline ) ) {
				$i = $last_newline;
			}
			break;
		}

		return ++$i;
	}

	/**
	 * Find the last token for the complete statement, including trailing comments
	 * and the new line token.
	 *
	 * @param int $usePtr Stack pointer to the use keyword.
	 *
	 * @return int|bool Integer stack pointer or false when it couldn't be determined.
	 */
	protected function find_end_of_use_statement( $usePtr ) {
		$semicolon = $this->phpcsFile->findNext( T_SEMICOLON, ( $usePtr + 1 ), null, false, null, true );
		if ( false === $semicolon ) {
			// Live coding.
			return false;
		}

		for ( $end = ( $semicolon + 1 ); $end < $this->phpcsFile->numTokens; $end++ ) {
			if ( isset( Tokens::$emptyTokens[ $this->tokens[ $end ]['code'] ] )
				&& strpos( $this->tokens[ $end ]['content'], $this->phpcsFile->eolChar ) === false
				&& $this->tokens[ $end ]['line'] === $this->tokens[ ( $end - 1 ) ]['line']
			) {
				continue;
			}

			// Deal with multiple statements on one line.
			if ( ! isset( Tokens::$emptyTokens[ $this->tokens[ $end ]['code'] ] )
				&& $this->tokens[ $end ]['line'] === $this->tokens[ ( $end - 1 ) ]['line']
			) {
				$end = $semicolon;
			}

			break;
		}

		return $end;
	}

} // End class.
