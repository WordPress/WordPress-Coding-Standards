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
		'array'    => true,
		'bool'     => true,
		'callable' => true,
		'double'   => true,
		'float'    => true,
		'int'      => true,
		'iterable' => true,
		'parent'   => true,
		'self'     => true,
		'string'   => true,
		'void'     => true,
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
	 * @return void Return before end if return type declaration contains an invalid token.
	 */
	public function process_token( $stackPtr ) {
		$colon = $this->phpcsFile->findPrevious( T_COLON, $stackPtr - 1, null, false, null, true );

		if ( false === $colon ) {
			// Shouldn't happen, but just in case.
			return;
		}

		// Space before colon disallowed.
		if ( isset( $this->tokens[ $colon - 1 ] ) && T_CLOSE_PARENTHESIS !== $this->tokens[ $colon - 1 ]['code'] ) {
			$error = 'There must be no space between the closing parenthesis and the colon when declaring a return type for a function.';
			$fix   = $this->phpcsFile->addFixableError( $error, $colon - 1, 'SpaceBeforeColon' );

			if ( true === $fix ) {
				$this->phpcsFile->fixer->beginChangeset();
				$token = $colon - 1;
				do {
					$this->phpcsFile->fixer->replaceToken( $token, '' );
					-- $token;
				} while ( isset( $this->tokens[ $token ] ) && T_CLOSE_PARENTHESIS !== $this->tokens[ $token ]['code'] );
				$this->phpcsFile->fixer->endChangeset();
			}
		}

		// Only one space after colon.
		if ( T_WHITESPACE !== $this->tokens[ $colon + 1 ]['code'] ) {
			$error = 'There must be one space between the colon and the return type. None found.';
			$fix   = $this->phpcsFile->addFixableError( $error, $colon, 'NoSpaceAfterColon' );
			if ( true === $fix ) {
				$this->phpcsFile->fixer->addContent( $colon, ' ' );
			}
		} elseif ( ' ' !== $this->tokens[ $colon + 1 ]['content'] ) {
			$error = 'There must be exactly one space between the colon and the return type. Found: %s';
			$data = array( 'more than one' ); /* @var @todo Count actual whitespaces */
			$fix   = $this->phpcsFile->addFixableError( $error, $colon + 1, 'TooManySpacesAfterColon', $data );
			if ( true === $fix ) {
				$this->phpcsFile->fixer->replaceToken( $colon + 1, ' ' );
			}
		}

		$nullable = $this->phpcsFile->findNext( T_NULLABLE, $colon + 1, $stackPtr );
		// Check if there is space after nullable operator.
		if ( false !== $nullable && T_WHITESPACE === $this->tokens[ $nullable + 1 ]['code'] ) {
			$error = 'Space is not allowed after nullable operator.';
			$fix   = $this->phpcsFile->addFixableError( $error, $nullable + 1, 'SpaceAfterNullable' );
			if ( true === $fix ) {
				$this->phpcsFile->fixer->replaceToken( $nullable + 1, '' );
			}
		}

		$contentLC = strtolower( $this->tokens[ $stackPtr ]['content'] );
		// Check if simple return type is in lowercase.
		if ( isset( $this->simple_return_types[ $contentLC ] ) && $contentLC !== $this->tokens[ $stackPtr ]['content'] ) {
			$error = 'Simple return type must be lowercase. Found "%s", expected "%s"';
			$data  = array(
				$this->tokens[ $stackPtr ]['content'],
				$contentLC,
			);
			$fix   = $this->phpcsFile->addFixableError( $error, $stackPtr, 'LowerCaseSimpleType', $data );
			if ( true === $fix ) {
				$this->phpcsFile->fixer->replaceToken( $stackPtr, $contentLC );
			}
		}
	}
}
