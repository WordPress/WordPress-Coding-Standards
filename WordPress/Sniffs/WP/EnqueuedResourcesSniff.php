<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WP;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\Sniff;

/**
 * Makes sure scripts and styles are enqueued and not explicitly echo'd.
 *
 * @link https://vip.wordpress.com/documentation/vip-go/code-review-blockers-warnings-notices/#inline-resources
 *
 * @since 0.3.0
 * @since 0.12.0 This class now extends the WordPressCS native `Sniff` class.
 * @since 0.13.0 Class name changed: this class is now namespaced.
 */
final class EnqueuedResourcesSniff extends Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		$targets   = Collections::textStringStartTokens();
		$targets[] = \T_INLINE_HTML;

		return $targets;
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

		$end_ptr = $stackPtr;
		$content = $this->tokens[ $stackPtr ]['content'];
		if ( \T_INLINE_HTML !== $this->tokens[ $stackPtr ]['code'] ) {
			try {
				$end_ptr = TextStrings::getEndOfCompleteTextString( $this->phpcsFile, $stackPtr );
				$content = TextStrings::getCompleteTextString( $this->phpcsFile, $stackPtr );
			} catch ( RuntimeException $e ) {
				// Parse error/live coding.
				return;
			}
		}

		if ( preg_match_all( '# rel=\\\\?[\'"]?stylesheet\\\\?[\'"]?#', $content, $matches, \PREG_OFFSET_CAPTURE ) > 0 ) {
			foreach ( $matches[0] as $match ) {
				$this->phpcsFile->addError(
					'Stylesheets must be registered/enqueued via wp_enqueue_style()',
					$this->find_token_in_multiline_string( $stackPtr, $content, $match[1] ),
					'NonEnqueuedStylesheet'
				);
			}
		}

		if ( preg_match_all( '#<script[^>]*(?<=src=)#', $content, $matches, \PREG_OFFSET_CAPTURE ) > 0 ) {
			foreach ( $matches[0] as $match ) {
				$this->phpcsFile->addError(
					'Scripts must be registered/enqueued via wp_enqueue_script()',
					$this->find_token_in_multiline_string( $stackPtr, $content, $match[1] ),
					'NonEnqueuedScript'
				);
			}
		}

		return ( $end_ptr + 1 );
	}

	/**
	 * Find the exact token on which the error should be reported for multi-line strings.
	 *
	 * @param int    $stackPtr     The position of the current token in the stack.
	 * @param string $content      The complete, potentially multi-line, text string.
	 * @param int    $match_offset The offset within the content at which the match was found.
	 *
	 * @return int The stack pointer to the token containing the start of the match.
	 */
	private function find_token_in_multiline_string( $stackPtr, $content, $match_offset ) {
		$newline_count = 0;
		if ( $match_offset > 0 ) {
			$newline_count = substr_count( $content, "\n", 0, $match_offset );
		}

		// Account for heredoc/nowdoc text starting at the token *after* the opener.
		if ( isset( Tokens::$heredocTokens[ $this->tokens[ $stackPtr ]['code'] ] ) === true ) {
			++$newline_count;
		}

		return ( $stackPtr + $newline_count );
	}
}
