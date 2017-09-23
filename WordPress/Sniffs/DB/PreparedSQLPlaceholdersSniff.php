<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\DB;

use WordPress\Sniff;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Check for incorrect use of the $wpdb->prepare method.
 *
 * Check the following issues:
 * - As of WP 4.8.2, the only placeholders supported are: %d, %f (%F) and %s.
 * - Literal % signs need to be properly escaped as `%%`.
 * - Placeholders should be left unquoted in the query string.
 * - Either an array of replacements should be passed matching the number of
 *   placeholders found or individual parameters for each placeholder should
 *   be passed.
 *
 * @link https://developer.wordpress.org/reference/classes/wpdb/prepare/
 * @link https://core.trac.wordpress.org/changeset/41496
 * @link https://core.trac.wordpress.org/changeset/41471
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 */
class PreparedSQLPlaceholdersSniff extends Sniff {

	/**
	 * List of $wpdb methods we are interested in.
	 *
	 * @since 0.14.0
	 *
	 * @var array
	 */
	protected $target_methods = array(
		'prepare' => true,
	);

	/**
	 * Storage for the stack pointer to the method call token.
	 *
	 * @since 0.14.0
	 *
	 * @var int
	 */
	protected $methodPtr;

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since 0.14.0
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_VARIABLE,
			T_STRING,
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @since 0.14.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {

		if ( ! $this->is_wpdb_method_call( $stackPtr, $this->target_methods ) ) {
			return;
		}

		$parameters = $this->get_function_call_parameters( $this->methodPtr );
		if ( empty( $parameters ) ) {
			return;
		}

		$query              = $parameters[1];
		$total_placeholders = 0;
		$total_parameters   = count( $parameters );

		for ( $i = $query['start']; $i <= $query['end']; $i++ ) {
			if ( ! isset( Tokens::$textStringTokens[ $this->tokens[ $i ]['code'] ] ) ) {
				continue;
			}

			$content     = $this->tokens[ $i ]['content'];
			$quote_style = false;
			if ( isset( Tokens::$stringTokens[ $this->tokens[ $i ]['code'] ] ) ) {
				$content = $this->strip_quotes( $content );

				if ( $this->tokens[ $i ]['content'] !== $content ) {
					$quote_style = $this->tokens[ $i ]['content'][0];
				}
			}

			$placeholders = preg_match_all( '`(?<!%)%[dFfs]`', $content, $matches );
			if ( $placeholders > 0 ) {
				$total_placeholders += $placeholders;
			}

			/*
			 * Analyse the query for unsupported placeholders.
			 */
			if ( preg_match_all( '`(?<!%)%(?![dfFs]|%[^%])(?:[^ \'"]*|$)`', $content, $matches ) > 0 ) {
				if ( ! empty( $matches[0] ) ) {
					foreach ( $matches[0] as $match ) {
						if ( '%' === $match ) {
							$this->phpcsFile->addError(
								'Found unescaped literal "%" character.',
								$i,
								'UnescapedLiteral',
								array( $match )
							);
						} else {
							$this->phpcsFile->addError(
								'Unsupported placeholder used in $wpdb->prepare(). Found: "%s".',
								$i,
								'UnsupportedPlaceholder',
								array( $match )
							);
						}
					}
				}
				unset( $match, $matches );
			}

			/*
			 * Analyse the query for quoted placeholders.
			 */
			$regex = '`(["\'])%[dfFs]+\1`';
			if ( '"' === $quote_style ) {
				$regex = '`(\\\\"|\')%[dfFs]+\1`';
			} elseif ( "'" === $quote_style ) {
				$regex = '`("|\\\\\')%[dfFs]+\1`';
			}
			if ( preg_match_all( $regex, $content, $matches ) > 0 ) {
				if ( ! empty( $matches[0] ) ) {
					foreach ( $matches[0] as $match ) {
						$this->phpcsFile->addError(
							'Placeholders should be unquoted in the query string in $wpdb->prepare(). Found: %s.',
							$i,
							'QuotedPlaceholder',
							array( $match )
						);
					}
				}
				unset( $match, $matches );
			}
		}

		if ( 0 === $total_placeholders ) {
			if ( 1 === $total_parameters ) {
				$this->phpcsFile->addWarning(
					'It is not necessary to prepare a query which doesn\'t use variable replacement.',
					$i,
					'UnnecessaryPrepare'
				);
			}

			return;
		}

		if ( 1 === $total_parameters ) {
			$this->phpcsFile->addError(
				'Placeholders found in the query passed to $wpdb->prepare(), but no replacements found. Expected %d replacement(s) parameters.',
				$stackPtr,
				'MissingReplacements',
				array( $total_placeholders )
			);
			return;
		}

		$replacements = $parameters;
		array_shift( $replacements ); // Remove the query.

		// The parameters may have been passed as an array in parameter 2.
		if ( isset( $parameters[2] ) && 2 === $total_parameters ) {
			$next = $this->phpcsFile->findNext(
				Tokens::$emptyTokens,
				$parameters[2]['start'],
				( $parameters[2]['end'] + 1 ),
				true
			);

			if ( false !== $next
				&& ( T_ARRAY === $this->tokens[ $next ]['code']
					|| T_OPEN_SHORT_ARRAY === $this->tokens[ $next ]['code'] )
			) {
				$replacements = $this->get_function_call_parameters( $next );
			}
		}

		/*
		 * Verify that the correct amount of replacements have been passed.
		 */
		$total_replacements = count( $replacements );
		if ( $total_replacements !== $total_placeholders ) {
			$this->phpcsFile->addError(
				'Incorrect number of replacements passed to $wpdb->prepare(). Found %d replacement parameters, expected %d.',
				$stackPtr,
				'ReplacementsWrongNumber',
				array( $total_replacements, $total_placeholders )
			);
		}
	}

}
