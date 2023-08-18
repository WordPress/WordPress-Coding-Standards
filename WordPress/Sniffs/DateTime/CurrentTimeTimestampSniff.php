<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\DateTime;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\AbstractFunctionParameterSniff;

/**
 * Don't use current_time() to get a (timezone corrected) "timestamp".
 *
 * Disallow using the current_time() function to get "timestamps" as it
 * doesn't produce a *real* timestamp, but a "WordPress timestamp", i.e.
 * a Unix timestamp with current timezone offset, not a Unix timestamp ansich.
 *
 * @link https://developer.wordpress.org/reference/functions/current_time/
 * @link https://make.wordpress.org/core/2019/09/23/date-time-improvements-wp-5-3/
 * @link https://core.trac.wordpress.org/ticket/40657
 * @link https://github.com/WordPress/WordPress-Coding-Standards/issues/1791
 *
 * @since 2.2.0
 */
final class CurrentTimeTimestampSniff extends AbstractFunctionParameterSniff {

	/**
	 * The group name for this group of functions.
	 *
	 * @since 2.2.0
	 *
	 * @var string
	 */
	protected $group_name = 'current_time';

	/**
	 * List of functions to examine.
	 *
	 * @since 2.2.0
	 *
	 * @var array<string, true> Key is function name, value irrelevant.
	 */
	protected $target_functions = array(
		'current_time' => true,
	);

	/**
	 * Process the parameters of a matched function.
	 *
	 * @since 2.2.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched
	 *                                in lowercase.
	 * @param array  $parameters      Array with information about the parameters.
	 *
	 * @return void
	 */
	public function process_parameters( $stackPtr, $group_name, $matched_content, $parameters ) {
		/*
		 * We already know there will be valid open & close parentheses as otherwise the parameter
		 * retrieval function call would have returned an empty array, so no additional checks needed.
		 */
		$open_parens  = $this->phpcsFile->findNext( \T_OPEN_PARENTHESIS, $stackPtr );
		$close_parens = $this->tokens[ $open_parens ]['parenthesis_closer'];

		/*
		 * Check whether the first parameter is a timestamp format.
		 */
		$type_param = PassedParameters::getParameterFromStack( $parameters, 1, 'type' );
		if ( false === $type_param ) {
			// Type parameter not found. Bow out.
			return;
		}

		$content_type = '';
		for ( $i = $type_param['start']; $i <= $type_param['end']; $i++ ) {
			if ( isset( Tokens::$emptyTokens[ $this->tokens[ $i ]['code'] ] ) ) {
				continue;
			}

			if ( isset( Tokens::$textStringTokens[ $this->tokens[ $i ]['code'] ] ) ) {
				$content_type = trim( TextStrings::stripQuotes( $this->tokens[ $i ]['content'] ) );
				if ( 'U' !== $content_type && 'timestamp' !== $content_type ) {
					// Most likely valid use of current_time().
					return;
				}

				continue;
			}

			if ( isset( Tokens::$heredocTokens[ $this->tokens[ $i ]['code'] ] ) ) {
				continue;
			}

			/*
			 * If we're still here, we've encountered an unexpected token, like a variable or
			 * function call. Bow out as we can't determine the runtime value.
			 */
			return;
		}

		$gmt_true = false;

		/*
		 * Check whether the second parameter, $gmt, is a set to `true` or `1`.
		 */
		$gmt_param = PassedParameters::getParameterFromStack( $parameters, 2, 'gmt' );
		if ( is_array( $gmt_param ) ) {
			$content_gmt = '';
			if ( 'true' === $gmt_param['clean'] || '1' === $gmt_param['clean'] ) {
				$content_gmt = $gmt_param['clean'];
				$gmt_true    = true;
			}
		}

		/*
		 * Non-UTC timestamp requested.
		 */
		if ( false === $gmt_true ) {
			$this->phpcsFile->addWarning(
				'Calling current_time() with a $type of "timestamp" or "U" is strongly discouraged as it will not return a Unix (UTC) timestamp. Please consider using a non-timestamp format or otherwise refactoring this code.',
				$stackPtr,
				'Requested'
			);

			return;
		}

		/*
		 * UTC timestamp requested. Should use time() instead.
		 */
		$has_comment = $this->phpcsFile->findNext( Tokens::$commentTokens, ( $stackPtr + 1 ), ( $close_parens + 1 ) );
		$error       = 'Don\'t use current_time() for retrieving a Unix (UTC) timestamp. Use time() instead. Found: %s';
		$error_code  = 'RequestedUTC';

		$code_snippet = "current_time( '" . $content_type . "'";
		if ( isset( $content_gmt ) ) {
			$code_snippet .= ', ' . $content_gmt;
		}
		$code_snippet .= ' )';

		if ( false !== $has_comment ) {
			// If there are comments, we don't auto-fix as it would remove those comments.
			$this->phpcsFile->addError( $error, $stackPtr, $error_code, array( $code_snippet ) );
			return;
		}

		$fix = $this->phpcsFile->addFixableError( $error, $stackPtr, $error_code, array( $code_snippet ) );
		if ( true === $fix ) {
			$this->phpcsFile->fixer->beginChangeset();

			for ( $i = ( $stackPtr + 1 ); $i < $close_parens; $i++ ) {
				$this->phpcsFile->fixer->replaceToken( $i, '' );
			}

			$this->phpcsFile->fixer->replaceToken( $stackPtr, 'time(' );
			$this->phpcsFile->fixer->endChangeset();
		}
	}
}
