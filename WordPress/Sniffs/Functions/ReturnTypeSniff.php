<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\Functions;

use PHP_CodeSniffer_Tokens as Tokens;
use WordPress\Sniff;

/**
 * Enforces formatting of return types.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 *
 * @link    https://github.com/zendframework/zend-coding-standard/blob/52b435c714879609262aa36b004c4075cd967acc/src/ZendCodingStandard/Sniffs/Formatting/ReturnTypeSniff.php
 */
class ReturnTypeSniff extends Sniff {
	/**
	 * Simple return types.
	 *
	 * @since 0.14.0
	 *
	 * @var string[]
	 */
	private $simple_return_types = array(
		'void',
		'int',
		'float',
		'double',
		'string',
		'array',
		'iterable',
		'callable',
		'parent',
		'self',
		'bool',
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_RETURN_TYPE,
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 * @return null Return before end if Return type declaration contains an invalid token.
	 */
	public function process_token( $stackPtr ) {
		$colon = (int) $this->phpcsFile->findPrevious( T_COLON, $stackPtr - 1 );

		// Space before colon disallowed.
		if ( T_CLOSE_PARENTHESIS !== $this->tokens[ $colon - 1 ]['code'] ) {
			$error = 'There must be no space before colon before a return type.';
			$fix   = $this->phpcsFile->addFixableError( $error, $colon - 1, 'SpaceBeforeColon' );

			if ( true === $fix ) {
				$this->phpcsFile->fixer->beginChangeset();
				$token = $colon - 1;
				do {
					$this->phpcsFile->fixer->replaceToken( $token, '' );
					-- $token;
				} while ( T_CLOSE_PARENTHESIS !== $this->tokens[ $token ]['code'] );
				$this->phpcsFile->fixer->endChangeset();
			}
		}

		// Only one space after colon.
		if ( T_WHITESPACE !== $this->tokens[ $colon + 1 ]['code'] ) {
			$error = 'There must be a space after colon and before return type declaration.';
			$fix   = $this->phpcsFile->addFixableError( $error, $colon, 'NoSpaceAfterColon' );
			if ( true === $fix ) {
				$this->phpcsFile->fixer->addContent( $colon, ' ' );
			}
		} elseif ( ' ' !== $this->tokens[ $colon + 1 ]['content'] ) {
			$error = 'There must be exactly one space after colon and before return type declaration.';
			$fix   = $this->phpcsFile->addFixableError( $error, $colon + 1, 'TooManySpacesAfterColon' );
			if ( true === $fix ) {
				$this->phpcsFile->fixer->replaceToken( $colon + 1, ' ' );
			}
		}

		$nullable = (int) $this->phpcsFile->findNext( T_NULLABLE, $colon + 1, $stackPtr );
		if ( $nullable ) {
			// Check if there is space after nullable operator.
			if ( T_WHITESPACE === $this->tokens[ $nullable + 1 ]['code'] ) {
				$error = 'Space is not not allowed after nullable operator.';
				$fix   = $this->phpcsFile->addFixableError( $error, $nullable + 1, 'SpaceAfterNullable' );
				if ( $fix ) {
					$this->phpcsFile->fixer->replaceToken( $nullable + 1, '' );
				}
			}
		}

		$first   = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $nullable ?: $colon ) + 1, null, true );
		$end     = $this->phpcsFile->findNext( array( T_SEMICOLON, T_OPEN_CURLY_BRACKET ), $stackPtr + 1 );
		$last    = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, $end - 1, null, true );
		$invalid = (int) $this->phpcsFile->findNext( array( T_STRING, T_NS_SEPARATOR, T_RETURN_TYPE ), $first, $last + 1, true );
		if ( $invalid ) {
			$error = 'Return type declaration contains invalid token %s';
			$data  = array( $this->tokens[ $invalid ]['type'] );
			$fix   = $this->phpcsFile->addFixableError( $error, $invalid, 'InvalidToken', $data );
			if ( true === $fix ) {
				$this->phpcsFile->fixer->replaceToken( $invalid, '' );
			}

			return;
		}

		$returnType = trim( $this->phpcsFile->getTokensAsString( $first, $last - $first + 1 ) );

		if ( $first === $last
			&& ! in_array( $returnType, $this->simple_return_types, true )
			&& in_array( strtolower( $returnType ), $this->simple_return_types, true )
		) {
			$error = 'Simple return type must be lowercase. Found "%s", expected "%s"';
			$data  = array(
				$returnType,
				strtolower( $returnType ),
			);
			$fix   = $this->phpcsFile->addFixableError( $error, $first, 'LowerCaseSimpleType', $data );
			if ( true === $fix ) {
				$this->phpcsFile->fixer->replaceToken( $stackPtr, strtolower( $returnType ) );
			}
		}
	}
}
