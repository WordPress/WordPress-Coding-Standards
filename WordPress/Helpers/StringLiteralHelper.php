<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Helpers;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Utils\TextStrings;

/**
 * Helper utilities for checking if a parameter is a string literal.
 *
 * @since x.y.z
 */
final class StringLiteralHelper {
	/**
	 * Check if a parameter is a simple string literal without interpolation.
	 *
	 * This method checks if a parameter:
	 *
	 *   - Is a quoted string (single or double quotes)
	 *   - Does not contain interpolated variables (in double-quoted strings)
	 *   - Is not a concatenation of multiple values
	 *   - Is not a heredoc/nowdoc
	 *
	 * @param array $param_info Parameter info array as received from PassedParameters::getParameter().
	 * @param array $tokens     The token stack from phpcsFile->getTokens().
	 * @return bool True if the parameter is a simple string literal, false otherwise.
	 */
	public static function is_string_literal( $param_info, $tokens ) {
		if ( false === $param_info || '' === $param_info['clean'] ) {
			return false;
		}

		// Quick check using TextStrings utility.
		$stripped = TextStrings::stripQuotes( $param_info['clean'] );

		// If stripQuotes doesn't change the string, it's not quoted.
		if ( $stripped === $param_info['clean'] ) {
			return false;
		}

		// Check for interpolated variables in the stripped content.
		if ( strpos( $stripped, '$' ) !== false ) {
			return false;
		}

		// Check for concatenation by looking for multiple non-whitespace tokens.
		// Count non-empty tokens to detect concatenation.
		$non_empty_count = 0;
		for ( $i = $param_info['start']; $i <= $param_info['end']; $i++ ) {
			if ( ! isset( Tokens::$emptyTokens[ $tokens[ $i ]['code'] ] ) ) {
				++$non_empty_count;
				if ( $non_empty_count > 1 ) {
					// More than one non-empty token means concatenation or complex expression.
					return false;
				}
			}
		}

		return true;
	}
}
