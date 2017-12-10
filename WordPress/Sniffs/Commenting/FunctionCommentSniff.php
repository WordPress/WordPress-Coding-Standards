<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\Commenting;

use WordPress\Sniff;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Validates function documentation.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.16.0
 */
class FunctionCommentSniff extends Sniff {

	/**
	 * Registers the tokens that this sniff wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array( T_FUNCTION );
	}

	/**
	 * Processes this test when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {
		$commentEnd = $this->phpcsFile->findPrevious(
			array_merge( Tokens::$methodPrefixes, array( T_WHITESPACE ) ),
			( $stackPtr - 1 ),
			null,
			true
		);

		if ( T_DOC_COMMENT_CLOSE_TAG !== $this->tokens[ $commentEnd ]['code'] ) {
			// Invalid function comment. Handled elsewhere.
			return;
		}

		$commentStart = $this->tokens[ $commentEnd ]['comment_opener'];

		$this->process_return( $stackPtr, $commentStart );
	}

	/**
	 * Process this function's return comment.
	 *
	 * @param int $stackPtr     The position of the current token in the stack.
	 * @param int $commentStart The position in the stack where the comment started.
	 * @return void
	 */
	protected function process_return( $stackPtr, $commentStart ) {
		$returnTag = null;

		foreach ( $this->tokens[ $commentStart ]['comment_tags'] as $tag ) {
			if ( '@return' === $this->tokens[ $tag ]['content'] ) {
				$returnTag = $tag;
				// Multiple return tags are invalid, but flagged elsewhere.
				break;
			}
		}

		if ( ! $returnTag ) {
			return;
		}

		$returnCommentPtr = ( $returnTag + 2 );
		$returnComment    = $this->tokens[ $returnCommentPtr ];

		if ( empty( $returnComment['content'] ) || T_DOC_COMMENT_STRING !== $returnComment['code'] ) {
			// Invalid return comment. Handled elsewhere.
			return;
		}

		// Extracted from PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting\FunctionCommentSniff::processReturn().
		preg_match( '`^((?:\|?(?:array\([^\)]*\)|[\\\\a-z0-9\[\]]+))*)( .*)?`i', $returnComment['content'], $commentParts );

		if ( empty( $commentParts[1] ) ) {
			return;
		}

		$returnTypes = array_unique( explode( '|', $commentParts[1] ) );

		/*
		 * Disallow `@return void`.
		 * See https://make.wordpress.org/core/handbook/best-practices/inline-documentation-standards/php/#phpdoc-tags.
		 */
		if ( array( 'void' ) === $returnTypes ) {
			$this->phpcsFile->addError(
				'`@return void` should not be used. Omit the `@return` tag instead',
				$returnTag,
				'NoReturnVoid'
			);
		}
	}

}
