<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\Arrays;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Fixers\SpacesFixer;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Arrays;
use PHPCSUtils\Utils\PassedParameters;
use WordPressCS\WordPress\Sniff;

/**
 * Enforces WordPress array spacing format.
 *
 * - Checks that associative arrays are multi-line.
 * - Checks that each array item in a multi-line array starts on a new line.
 *
 * @link https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/#indentation
 *
 * @since 0.11.0 - The WordPress specific additional checks have now been split off
 *                 from the `WordPress.Arrays.ArrayDeclaration` sniff into this sniff.
 *               - Added sniffing & fixing for associative arrays.
 * @since 0.12.0 Decoupled this sniff from the upstream sniff completely.
 *               This sniff now extends the WordPressCS native `Sniff` class instead.
 * @since 0.13.0 Added the last remaining checks from the `WordPress.Arrays.ArrayDeclaration`
 *               sniff which were not covered elsewhere.
 *               The `WordPress.Arrays.ArrayDeclaration` sniff has now been deprecated.
 * @since 0.13.0 Class name changed: this class is now namespaced.
 * @since 0.14.0 Single item associative arrays are now by default exempt from the
 *               "must be multi-line" rule. This behaviour can be changed using the
 *               `allow_single_item_single_line_associative_arrays` property.
 * @since 3.0.0  Removed various whitespace related checks and fixers in favour of the PHPCSExtra
 *               `NormalizedArrays.Arrays.ArrayBraceSpacing` sniff.
 */
final class ArrayDeclarationSpacingSniff extends Sniff {

	/**
	 * Whether or not to allow single item associative arrays to be single line.
	 *
	 * @since 0.14.0
	 *
	 * @var bool Defaults to true.
	 */
	public $allow_single_item_single_line_associative_arrays = true;

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since 0.12.0
	 *
	 * @return array
	 */
	public function register() {
		return Collections::arrayOpenTokensBC();
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @since 0.12.0 The actual checks contained in this method used to
	 *               be in the `processSingleLineArray()` method.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {

		if ( isset( Collections::shortArrayListOpenTokensBC()[ $this->tokens[ $stackPtr ]['code'] ] )
			&& Arrays::isShortArray( $this->phpcsFile, $stackPtr ) === false
		) {
			// Short list, not short array.
			return;
		}

		/*
		 * Determine the array opener & closer.
		 */
		$array_open_close = Arrays::getOpenClose( $this->phpcsFile, $stackPtr );
		if ( false === $array_open_close ) {
			// Array open/close could not be determined.
			return;
		}

		$opener = $array_open_close['opener'];
		$closer = $array_open_close['closer'];
		unset( $array_open_close );

		// Pass off to either the single line or multi-line array analysis.
		if ( $this->tokens[ $opener ]['line'] === $this->tokens[ $closer ]['line'] ) {
			$this->process_single_line_array( $stackPtr, $opener, $closer );
		} else {
			$this->process_multi_line_array( $stackPtr, $opener );
		}
	}

	/**
	 * Check that associative arrays are always multi-line.
	 *
	 * @since 0.13.0 The actual checks contained in this method used to
	 *               be in the `process()` method.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 * @param int $opener   The position of the array opener.
	 * @param int $closer   The position of the array closer.
	 *
	 * @return void
	 */
	protected function process_single_line_array( $stackPtr, $opener, $closer ) {
		$array_items = PassedParameters::getParameters( $this->phpcsFile, $stackPtr );
		if ( ( false === $this->allow_single_item_single_line_associative_arrays
				&& empty( $array_items ) )
			|| ( true === $this->allow_single_item_single_line_associative_arrays
				&& \count( $array_items ) === 1 )
		) {
			return;
		}

		/*
		 * Make sure the double arrow is for *this* array, not for a nested one.
		 */
		$array_has_keys = false;
		foreach ( $array_items as $item ) {
			if ( Arrays::getDoubleArrowPtr( $this->phpcsFile, $item['start'], $item['end'] ) !== false ) {
				$array_has_keys = true;
				break;
			}
		}

		if ( false === $array_has_keys ) {
			return;
		}
		$error = 'When an array uses associative keys, each value should start on %s.';
		if ( true === $this->allow_single_item_single_line_associative_arrays ) {
			$error = 'When a multi-item array uses associative keys, each value should start on %s.';
		}

		/*
		 * Just add a new line before the array closer.
		 * The multi-line array fixer will then fix the individual array items in the next fixer loop.
		 */
		SpacesFixer::checkAndFix(
			$this->phpcsFile,
			$closer,
			$this->phpcsFile->findPrevious( \T_WHITESPACE, ( $closer - 1 ), null, true ),
			'newline',
			$error,
			'AssociativeArrayFound',
			'error'
		);
	}

	/**
	 * Process a multi-line array.
	 *
	 * @since 0.13.0 The actual checks contained in this method used to
	 *               be in the `ArrayDeclaration` sniff.
	 * @since 3.0.0  Removed the `$closer` parameter.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 * @param int $opener   The position of the array opener.
	 *
	 * @return void
	 */
	protected function process_multi_line_array( $stackPtr, $opener ) {
		/*
		 * Check that each array item starts on a new line.
		 */
		$array_items      = PassedParameters::getParameters( $this->phpcsFile, $stackPtr );
		$end_of_last_item = $opener;

		foreach ( $array_items as $item ) {
			$end_of_this_item = ( $item['end'] + 1 );

			// Find the line on which the item starts.
			$first_content = $this->phpcsFile->findNext(
				array( \T_WHITESPACE, \T_DOC_COMMENT_WHITESPACE ),
				$item['start'],
				$end_of_this_item,
				true
			);

			// Ignore comments after array items if the next real content starts on a new line.
			if ( $this->tokens[ $first_content ]['line'] === $this->tokens[ $end_of_last_item ]['line']
				&& ( \T_COMMENT === $this->tokens[ $first_content ]['code']
				|| isset( Tokens::$phpcsCommentTokens[ $this->tokens[ $first_content ]['code'] ] ) )
			) {
				$end_of_comment = $first_content;

				// Find the end of (multi-line) /* */- style trailing comments.
				if ( substr( ltrim( $this->tokens[ $end_of_comment ]['content'] ), 0, 2 ) === '/*' ) {
					while ( ( \T_COMMENT === $this->tokens[ $end_of_comment ]['code']
						|| isset( Tokens::$phpcsCommentTokens[ $this->tokens[ $end_of_comment ]['code'] ] ) )
						&& substr( rtrim( $this->tokens[ $end_of_comment ]['content'] ), -2 ) !== '*/'
						&& ( $end_of_comment + 1 ) < $end_of_this_item
					) {
						++$end_of_comment;
					}

					if ( $this->tokens[ $end_of_comment ]['line'] !== $this->tokens[ $end_of_last_item ]['line'] ) {
						// Multi-line trailing comment.
						$end_of_last_item = $end_of_comment;
					}
				}

				$next = $this->phpcsFile->findNext(
					array( \T_WHITESPACE, \T_DOC_COMMENT_WHITESPACE ),
					( $end_of_comment + 1 ),
					$end_of_this_item,
					true
				);

				if ( false === $next ) {
					// Shouldn't happen, but just in case.
					$end_of_last_item = $end_of_this_item; // @codeCoverageIgnore
					continue; // @codeCoverageIgnore
				}

				if ( $this->tokens[ $next ]['line'] !== $this->tokens[ $first_content ]['line'] ) {
					$first_content = $next;
				}
			}

			if ( false === $first_content ) {
				// Shouldn't happen, but just in case.
				$end_of_last_item = $end_of_this_item; // @codeCoverageIgnore
				continue; // @codeCoverageIgnore
			}

			if ( $this->tokens[ $end_of_last_item ]['line'] === $this->tokens[ $first_content ]['line'] ) {
				SpacesFixer::checkAndFix(
					$this->phpcsFile,
					$first_content,
					$end_of_last_item,
					'newline',
					'Each item in a multi-line array must be on %s. Found: %s',
					'ArrayItemNoNewLine',
					'error'
				);
			}

			$end_of_last_item = $end_of_this_item;
		}
	}
}
