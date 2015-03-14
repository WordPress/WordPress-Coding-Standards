<?php

/**
 * Represents a PHP_CodeSniffer sniff for sniffing WordPress coding standards.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 */

/**
 * Represents a PHP_CodeSniffer sniff for sniffing WordPress coding standards.
 *
 * Provides a bootstrap for the sniffs, to reduce code duplication.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @version   0.4.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
abstract class WordPress_Sniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Get the last pointer in a line.
	 *
	 * @since 0.4.0
	 *
	 * @param array   $tokens   Tokens.
	 * @param integer $stackPtr The position of the current token in the stack passed
	 *                          in $tokens.
	 *
	 * @return integer Position of the last pointer on that line.
	 */
	protected function get_last_ptr_on_line( array $tokens, $stackPtr ) {

		$currentLine = $tokens[ $stackPtr ]['line'];
		$nextPtr = $stackPtr + 1;

		while ( isset( $tokens[ $nextPtr ] ) && $tokens[ $nextPtr ]['line'] === $currentLine ) {
			$nextPtr++;
			// Do nothing, we just want the last token of the line.
		}

		// We've made it to the next line, back up one to the last in the previous line.
		// We do this for micro-optimization of the above loop.
		$lastPtr = $nextPtr - 1;

		return $lastPtr;
	}

	/**
	 * Find whitelisting comment.
	 *
	 * Comment must be at the end of the line, and use // format.
	 * It can be prefixed or suffixed with anything e.g. "foobar" will match:
	 * ... // foobar okay
	 * ... // WPCS: foobar whitelist.
	 *
	 * @since 0.4.0
	 *
	 * @param string  $comment  Comment to find.
	 * @param array   $tokens   Tokens.
	 * @param integer $stackPtr The position of the current token in the stack passed
	 *                          in $tokens.
	 *
	 * @return boolean True if whitelisting comment was found, false otherwise.
	 */
	protected function has_whitelist_comment( $comment, array $tokens, $stackPtr ) {

		$lastPtr = $this->get_last_ptr_on_line( $tokens, $stackPtr );

		if ( T_COMMENT === $tokens[ $lastPtr ]['code'] ) {

			return preg_match(
				'#' . preg_quote( $comment ) . '#i'
				, $tokens[ $lastPtr ]['content']
			);

		} else {
			return false;
		}
	}
}

// EOF
