<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Verify operator spacing, based upon Squiz code.
 *
 * "Always put spaces after commas, and on both sides of logical, comparison, string and assignment operators."
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#space-usage
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.1.0
 * @since   0.3.0 This sniff now has the ability to fix the issues it flags.
 * @since   0.12.0 This class now extends WordPress_Sniff.
 *
 * Last synced with base class December 2008 at commit f01746fd1c89e98174b16c76efd325825eb58bf1.
 * @link    https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/Squiz/Sniffs/WhiteSpace/OperatorSpacingSniff.php
 */
class WordPress_Sniffs_WhiteSpace_OperatorSpacingSniff extends WordPress_Sniff {

	/**
	 * A list of tokenizers this sniff supports.
	 *
	 * @var array
	 */
	public $supportedTokenizers = array(
		'PHP',
		'JS',
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		$comparison = PHP_CodeSniffer_Tokens::$comparisonTokens;
		$operators  = PHP_CodeSniffer_Tokens::$operators;
		$assignment = PHP_CodeSniffer_Tokens::$assignmentTokens;

		// Union the arrays - keeps the array keys and - in this case - automatically de-dups.
		$tokens   = ( $comparison + $operators + $assignment );
		$tokens[] = T_BOOLEAN_NOT;

		return $tokens;

	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {

		if ( T_EQUAL === $this->tokens[ $stackPtr ]['code'] ) {
			// Skip for '=&' case.
			if ( isset( $this->tokens[ ( $stackPtr + 1 ) ] )
				&& T_BITWISE_AND === $this->tokens[ ( $stackPtr + 1 ) ]['code']
			) {
				return;
			}

			// Skip default values in function declarations.
			// Skip declare statements.
			if ( isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
				$bracket = end( $this->tokens[ $stackPtr ]['nested_parenthesis'] );
				if ( isset( $this->tokens[ $bracket ]['parenthesis_owner'] ) ) {
					$function = $this->tokens[ $bracket ]['parenthesis_owner'];
					if ( T_FUNCTION === $this->tokens[ $function ]['code']
						|| T_CLOSURE === $this->tokens[ $function ]['code']
						|| T_DECLARE === $this->tokens[ $function ]['code']
					) {
						return;
					}
				}
			}
		}

		if ( T_BITWISE_AND === $this->tokens[ $stackPtr ]['code'] ) {
			/*
			// If it's not a reference, then we expect one space either side of the
			// bitwise operator.
			if ( false === $this->phpcsFile->isReference( $stackPtr ) ) {
				// @todo Implement or remove ?
			}
			*/
			return;

		} else {
			if ( T_MINUS === $this->tokens[ $stackPtr ]['code'] ) {
				// Check minus spacing, but make sure we aren't just assigning
				// a minus value or returning one.
				$prev = $this->phpcsFile->findPrevious( T_WHITESPACE, ( $stackPtr - 1 ), null, true );
				if ( T_RETURN === $this->tokens[ $prev ]['code'] ) {
					// Just returning a negative value; eg. return -1.
					return;
				}

				if ( isset( PHP_CodeSniffer_Tokens::$operators[ $this->tokens[ $prev ]['code'] ] ) ) {
					// Just trying to operate on a negative value; eg. ($var * -1).
					return;
				}

				if ( isset( PHP_CodeSniffer_Tokens::$comparisonTokens[ $this->tokens[ $prev ]['code'] ] ) ) {
					// Just trying to compare a negative value; eg. ($var === -1).
					return;
				}

				// A list of tokens that indicate that the token is not
				// part of an arithmetic operation.
				$invalidTokens = array(
					T_COMMA,
					T_OPEN_PARENTHESIS,
					T_OPEN_SQUARE_BRACKET,
				);

				if ( in_array( $this->tokens[ $prev ]['code'], $invalidTokens, true ) ) {
					// Just trying to use a negative value; eg. myFunction($var, -2).
					return;
				}

				$number = $this->phpcsFile->findNext( T_WHITESPACE, ( $stackPtr + 1 ), null, true );
				if ( T_LNUMBER === $this->tokens[ $number ]['code'] ) {
					$semi = $this->phpcsFile->findNext( T_WHITESPACE, ( $number + 1 ), null, true );
					if ( T_SEMICOLON === $this->tokens[ $semi ]['code'] ) {
						if ( false !== $prev &&
							isset( PHP_CodeSniffer_Tokens::$assignmentTokens[ $this->tokens[ $prev ]['code'] ] )
						) {
							// This is a negative assignment.
							return;
						}
					}
				}
			}

			$operator = $this->tokens[ $stackPtr ]['content'];

			if ( T_WHITESPACE !== $this->tokens[ ( $stackPtr - 1 ) ]['code'] ) {
				$error = 'Expected 1 space before "%s"; 0 found';
				$data  = array( $operator );
				$fix   = $this->phpcsFile->addFixableError( $error, $stackPtr, 'NoSpaceBefore', $data );
				if ( true === $fix ) {
					$this->phpcsFile->fixer->beginChangeset();
					$this->phpcsFile->fixer->addContentBefore( $stackPtr, ' ' );
					$this->phpcsFile->fixer->endChangeset();
				}
			} elseif ( 1 !== strlen( $this->tokens[ ( $stackPtr - 1 ) ]['content'] )
				&& 1 !== $this->tokens[ ( $stackPtr - 1 ) ]['column']
			) {
				// Don't throw an error for assignments, because other standards allow
				// multiple spaces there to align multiple assignments.
				if ( false === isset( PHP_CodeSniffer_Tokens::$assignmentTokens[ $this->tokens[ $stackPtr ]['code'] ] ) ) {
					$found = strlen( $this->tokens[ ( $stackPtr - 1 ) ]['content'] );
					$error = 'Expected 1 space before "%s"; %s found';
					$data  = array(
						$operator,
						$found,
					);

					$fix = $this->phpcsFile->addFixableError( $error, $stackPtr, 'SpacingBefore', $data );
					if ( true === $fix ) {
						$this->phpcsFile->fixer->beginChangeset();
						$this->phpcsFile->fixer->replaceToken( ( $stackPtr - 1 ), ' ' );
						$this->phpcsFile->fixer->endChangeset();
					}
				}
			}

			if ( '-' !== $operator ) {
				if ( T_WHITESPACE !== $this->tokens[ ( $stackPtr + 1 ) ]['code'] ) {
					$error = 'Expected 1 space after "%s"; 0 found';
					$data  = array( $operator );

					$fix = $this->phpcsFile->addFixableError( $error, $stackPtr, 'NoSpaceAfter', $data );
					if ( true === $fix ) {
						$this->phpcsFile->fixer->beginChangeset();
						$this->phpcsFile->fixer->addContent( $stackPtr, ' ' );
						$this->phpcsFile->fixer->endChangeset();
					}
				} elseif ( 1 !== strlen( $this->tokens[ ( $stackPtr + 1 ) ]['content'] ) ) {
					$found = strlen( $this->tokens[ ( $stackPtr + 1 ) ]['content'] );
					$error = 'Expected 1 space after "%s"; %s found';
					$data  = array(
						$operator,
						$found,
					);

					$fix = $this->phpcsFile->addFixableError( $error, $stackPtr, 'SpacingAfter', $data );
					if ( true === $fix ) {
						$this->phpcsFile->fixer->beginChangeset();
						$this->phpcsFile->fixer->replaceToken( ( $stackPtr + 1 ), ' ' );
						$this->phpcsFile->fixer->endChangeset();
					}
				}
			}
		}

	} // End process().

} // End class.
