<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\DB;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Arrays;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\Helpers\MinimumWPVersionTrait;
use WordPressCS\WordPress\Helpers\WPDBTrait;
use WordPressCS\WordPress\Sniff;

/**
 * Checks for incorrect use of the $wpdb->prepare method.
 *
 * Checks the following issues:
 * - The only placeholders supported are: %d, %f (%F), %s, %i, and their variations.
 * - Literal % signs need to be properly escaped as `%%`.
 * - Simple placeholders (%d, %f, %F, %s, %i) should be left unquoted in the query string.
 * - Complex placeholders - numbered and formatted variants - will not be quoted
 *   automagically by $wpdb->prepare(), so if used for values, should be quoted in
 *   the query string.
 *   The only exception to this is complex placeholders for %i. In that case, the
 *   replacement *will* still be backtick-quoted.
 * - Either an array of replacements should be passed matching the number of
 *   placeholders found or individual parameters for each placeholder should
 *   be passed.
 * - Wildcards for LIKE compare values should be passed in via a replacement parameter.
 *
 * The sniff allows for a specific pattern with a variable number of placeholders
 * created using code along the lines of:
 * `sprintf( 'query .... IN (%s) ...', implode( ',', array_fill( 0, count( $something ), '%s' ) ) )`.
 *
 * @link https://developer.wordpress.org/reference/classes/wpdb/prepare/
 * @link https://core.trac.wordpress.org/changeset/41496
 * @link https://core.trac.wordpress.org/changeset/41471
 * @link https://core.trac.wordpress.org/changeset/55151
 *
 * @since 0.14.0
 * @since 3.0.0 Support for the %i placeholder has been added
 *
 * @uses \WordPressCS\WordPress\Helpers\MinimumWPVersionTrait::$minimum_wp_version
 */
final class PreparedSQLPlaceholdersSniff extends Sniff {

	use MinimumWPVersionTrait;
	use WPDBTrait;

	/**
	 * These regexes were originally copied from https://www.php.net/function.sprintf#93552
	 * and adjusted for limitations in `$wpdb->prepare()`.
	 *
	 * Near duplicate of the one used in the WP.I18n sniff, but with fewer types allowed.
	 *
	 * Note: The regex delimiters and modifiers are not included to allow this regex to be
	 * concatenated together with other regex partials.
	 *
	 * @since 0.14.0
	 *
	 * @var string
	 */
	const PREPARE_PLACEHOLDER_REGEX = '(?:
		(?<![^%]%)                     # Don\'t match a literal % (%%), including when it could overlap with a placeholder.
		(?:
			%                          # Start of placeholder.
			(?:[0-9]+\\\\?\$)?         # Optional ordering of the placeholders.
			[+-]?                      # Optional sign specifier.
			(?:
				(?:0|\'.)?                 # Optional padding specifier - excluding the space.
				-?                         # Optional alignment specifier.
				[0-9]*                     # Optional width specifier.
				(?:\.(?:[ 0]|\'.)?[0-9]+)? # Optional precision specifier with optional padding character.
				|                      # Only recognize the space as padding in combination with a width specifier.
				(?:[ ])?                   # Optional space padding specifier.
				-?                         # Optional alignment specifier.
				[0-9]+                     # Width specifier.
				(?:\.(?:[ 0]|\'.)?[0-9]+)? # Optional precision specifier with optional padding character.
			)
			[dfFsi]                    # Type specifier.
		)
	)';

	/**
	 * Similar to above, but for the placeholder types *not* supported.
	 *
	 * Note: all optional parts are forced to be greedy to allow for the negative look ahead
	 * at the end to work.
	 *
	 * @since 0.14.0
	 *
	 * @var string
	 */
	const UNSUPPORTED_PLACEHOLDER_REGEX = '`(?:
		(?<!%)                     # Don\'t match a literal % (%%).
		(
			%                          # Start of placeholder.
			(?!                        # Negative look ahead.
				%[^%]                       # Not a correct literal % (%%).
				|
				%%[dfFsi]                   # Nor a correct literal % (%%), followed by a simple placeholder.
			)
			(?:[0-9]+\\\\??\$)?+       # Optional ordering of the placeholders.
			[+-]?+                     # Optional sign specifier.
			(?:
				(?:0|\'.)?+                 # Optional padding specifier - excluding the space.
				-?+                         # Optional alignment specifier.
				[0-9]*+                     # Optional width specifier.
				(?:\.(?:[ 0]|\'.)?[0-9]+)?+ # Optional precision specifier with optional padding character.
				|                      # Only recognize the space as padding in combination with a width specifier.
				(?:[ ])?+                   # Optional space padding specifier.
				-?+                         # Optional alignment specifier.
				[0-9]++                     # Width specifier.
				(?:\.(?:[ 0]|\'.)?[0-9]+)?+ # Optional precision specifier with optional padding character.
			)
			(?![dfFsi])                # Negative look ahead: not one of the supported placeholders.
			(?:[^ \'"]*|$)             # but something else instead.
		)
	)`x';

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
	 * Simple regex snippet to recognize and remember quotes.
	 *
	 * @since 0.14.0
	 *
	 * @var string
	 */
	private $regex_quote = '["\']';

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since 0.14.0
	 *
	 * @return array
	 */
	public function register() {
		return array(
			\T_VARIABLE,
			\T_STRING,
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

		$this->set_minimum_wp_version();

		if ( ! $this->is_wpdb_method_call( $this->phpcsFile, $stackPtr, $this->target_methods ) ) {
			return;
		}

		$parameters = PassedParameters::getParameters( $this->phpcsFile, $this->methodPtr );
		if ( empty( $parameters ) ) {
			return;
		}

		$query = PassedParameters::getParameterFromStack( $parameters, 1, 'query' );
		if ( false === $query ) {
			return;
		}

		$text_string_tokens_found = false;
		$variable_found           = false;
		$sql_wildcard_found       = false;
		$total_placeholders       = 0;
		$total_parameters         = \count( $parameters );
		$valid_in_clauses         = array(
			'uses_in'          => 0,
			'implode_fill'     => 0,
			'adjustment_count' => 0,
		);
		$skip_from                = null;
		$skip_to                  = null;

		for ( $i = $query['start']; $i <= $query['end']; $i++ ) {
			// Skip over groups of tokens if they are part of an inline function call.
			if ( isset( $skip_from, $skip_to ) && $i >= $skip_from && $i <= $skip_to ) {
				$i = $skip_to;
				continue;
			}

			if ( ! isset( Tokens::$textStringTokens[ $this->tokens[ $i ]['code'] ] ) ) {
				if ( \T_VARIABLE === $this->tokens[ $i ]['code'] ) {
					if ( '$wpdb' !== $this->tokens[ $i ]['content'] ) {
						$variable_found = true;
					}
					continue;
				}

				// Detect a specific pattern for variable replacements in combination with `IN`.
				if ( \T_STRING === $this->tokens[ $i ]['code'] ) {

					if ( 'sprintf' === strtolower( $this->tokens[ $i ]['content'] ) ) {
						$sprintf_parameters = PassedParameters::getParameters( $this->phpcsFile, $i );

						if ( ! empty( $sprintf_parameters ) ) {
							/*
							 * Check for named params. sprintf() does not support this due to its variadic nature,
							 * and we cannot analyse the code correctly if it is used, so skip the whole sprintf()
							 * in that case.
							 */
							$valid_sprintf = true;
							foreach ( $sprintf_parameters as $param ) {
								if ( isset( $param['name'] ) ) {
									$valid_sprintf = false;
									break;
								}
							}

							if ( false === $valid_sprintf ) {
								$next = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $i + 1 ), null, true );
								if ( \T_OPEN_PARENTHESIS === $this->tokens[ $next ]['code']
									&& isset( $this->tokens[ $next ]['parenthesis_closer'] )
								) {
									$skip_from = ( $i + 1 );
									$skip_to   = $this->tokens[ $next ]['parenthesis_closer'];
								}

								continue;
							}

							// We know for sure this sprintf() uses positional parameters, so this will be fine.
							$skip_from  = ( $sprintf_parameters[1]['end'] + 1 );
							$last_param = end( $sprintf_parameters );
							$skip_to    = ( $last_param['end'] + 1 );

							$valid_in_clauses['implode_fill']     += $this->analyse_sprintf( $sprintf_parameters );
							$valid_in_clauses['adjustment_count'] += ( \count( $sprintf_parameters ) - 1 );
						}
						unset( $sprintf_parameters, $valid_sprintf, $last_param );

					} elseif ( 'implode' === strtolower( $this->tokens[ $i ]['content'] ) ) {
						$prev = $this->phpcsFile->findPrevious(
							Tokens::$emptyTokens + array( \T_STRING_CONCAT => \T_STRING_CONCAT ),
							( $i - 1 ),
							$query['start'],
							true
						);

						if ( isset( Tokens::$textStringTokens[ $this->tokens[ $prev ]['code'] ] ) ) {
							$prev_content = TextStrings::stripQuotes( $this->tokens[ $prev ]['content'] );
							$regex_quote  = $this->get_regex_quote_snippet( $prev_content, $this->tokens[ $prev ]['content'] );

							// Only examine the implode if preceded by an ` IN (`.
							if ( preg_match( '`\s+IN\s*\(\s*(' . $regex_quote . ')?$`i', $prev_content, $match ) > 0 ) {

								if ( isset( $match[1] ) && $regex_quote !== $this->regex_quote ) {
									$this->phpcsFile->addError(
										'Dynamic placeholder generation should not have surrounding quotes.',
										$prev,
										'QuotedDynamicPlaceholderGeneration'
									);
								}

								if ( $this->analyse_implode( $i ) === true ) {
									++$valid_in_clauses['uses_in'];
									++$valid_in_clauses['implode_fill'];

									$next = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $i + 1 ), null, true );
									if ( \T_OPEN_PARENTHESIS === $this->tokens[ $next ]['code']
										&& isset( $this->tokens[ $next ]['parenthesis_closer'] )
									) {
										$skip_from = ( $i + 1 );
										$skip_to   = $this->tokens[ $next ]['parenthesis_closer'];
									}
								}
							}
							unset( $next, $prev_content, $regex_quote, $match );
						}
						unset( $prev );
					}
				}

				continue;
			}

			$text_string_tokens_found = true;
			$content                  = $this->tokens[ $i ]['content'];

			$regex_quote = $this->regex_quote;
			if ( isset( Tokens::$stringTokens[ $this->tokens[ $i ]['code'] ] ) ) {
				$content     = TextStrings::stripQuotes( $content );
				$regex_quote = $this->get_regex_quote_snippet( $content, $this->tokens[ $i ]['content'] );
			}

			if ( \T_DOUBLE_QUOTED_STRING === $this->tokens[ $i ]['code']
				|| \T_HEREDOC === $this->tokens[ $i ]['code']
			) {
				// Only interested in actual query text, so strip out variables.
				$stripped_content = TextStrings::stripEmbeds( $content );
				if ( $stripped_content !== $content ) {
					$vars_without_wpdb = array_filter(
						TextStrings::getEmbeds( $content ),
						static function ( $symbol ) {
							return preg_match( '`^\{?\$\{?wpdb\??->`', $symbol ) !== 1;
						}
					);

					$content = $stripped_content;

					if ( ! empty( $vars_without_wpdb ) ) {
						$variable_found = true;
					}
				}
				unset( $stripped_content, $vars_without_wpdb );
			}

			$placeholders = preg_match_all( '`' . self::PREPARE_PLACEHOLDER_REGEX . '`x', $content, $matches );
			if ( $placeholders > 0 ) {
				$total_placeholders += $placeholders;
			}

			/*
			 * Analyse the query for incorrect LIKE queries.
			 *
			 * - `LIKE %s` is the only correct one.
			 * - `LIKE '%s'` or `LIKE "%s"` will not be reported here, but in the quote check.
			 * - Any other `LIKE` statement should be reported, either for using `LIKE` without
			 *   SQL wildcards or for not passing the SQL wildcards via the replacement.
			 */
			$regex = '`\s+LIKE\s*(?:(' . $regex_quote . ')(?!%s(?:\1|$))(?P<content>.*?)(?:\1|$)|(?:concat\((?![^\)]*%s[^\)]*\))(?P<concat>[^\)]*))\))`i';
			if ( preg_match_all( $regex, $content, $matches ) > 0 ) {
				$walk = array();
				if ( ! empty( $matches['content'] ) ) {
					$matches['content'] = array_filter( $matches['content'] );
					if ( ! empty( $matches['content'] ) ) {
						$walk[] = 'content';
					}
				}
				if ( ! empty( $matches['concat'] ) ) {
					$matches['concat'] = array_filter( $matches['concat'] );
					if ( ! empty( $matches['concat'] ) ) {
						$walk[] = 'concat';
					}
				}

				if ( ! empty( $walk ) ) {
					foreach ( $walk as $match_key ) {
						foreach ( $matches[ $match_key ] as $index => $match ) {
							$data = array( $matches[0][ $index ] );

							// Both a `%` as well as a `_` are wildcards in SQL.
							if ( strpos( $match, '%' ) === false && strpos( $match, '_' ) === false ) {
								$this->phpcsFile->addWarning(
									'Unless you are using SQL wildcards, using LIKE is inefficient. Use a straight compare instead. Found: %s.',
									$i,
									'LikeWithoutWildcards',
									$data
								);
							} else {
								$sql_wildcard_found = true;

								if ( strpos( $match, '%s' ) === false ) {
									$this->phpcsFile->addError(
										'SQL wildcards for a LIKE query should be passed in through a replacement parameter. Found: %s.',
										$i,
										'LikeWildcardsInQuery',
										$data
									);
								} else {
									$this->phpcsFile->addError(
										'SQL wildcards for a LIKE query should be passed in through a replacement parameter and the variable part of the replacement should be escaped using "esc_like()". Found: %s.',
										$i,
										'LikeWildcardsInQueryWithPlaceholder',
										$data
									);
								}
							}

							/*
							 * Don't throw `UnescapedLiteral`, `UnsupportedPlaceholder` or `QuotedPlaceholder`
							 * for this part of the SQL query.
							 */
							$content = preg_replace( '`' . preg_quote( $match, '`' ) . '`', '', $content, 1 );
						}
					}
				}
				unset( $matches, $index, $match, $data );
			}

			if ( strpos( $content, '%' ) === false ) {
				continue;
			}

			/*
			 * Analyse the query for unsupported placeholders.
			 */
			if ( preg_match_all( self::UNSUPPORTED_PLACEHOLDER_REGEX, $content, $matches ) > 0 ) {
				if ( ! empty( $matches[0] ) ) {
					foreach ( $matches[0] as $match ) {
						if ( '%' === $match ) {
							$this->phpcsFile->addError(
								'Found unescaped literal "%%" character.',
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

			if ( $this->wp_version_compare( $this->minimum_wp_version, '6.2', '<' ) ) {
				if ( preg_match_all( '`' . self::PREPARE_PLACEHOLDER_REGEX . '`x', $content, $matches ) > 0 ) {
					if ( ! empty( $matches[0] ) ) {
						foreach ( $matches[0] as $match ) {
							if ( 'i' === substr( $match, -1 ) ) {
								$this->phpcsFile->addError(
									'The %%i modifier is only supported in WP 6.2 or higher. Found: "%s".',
									$i,
									'UnsupportedIdentifierPlaceholder',
									array( $match )
								);
							}
						}
					}
				}
				unset( $match, $matches );
			}

			/*
			 * Analyse the query for single/double quoted simple value placeholders
			 * Identifiers are checked separately.
			 */
			$regex = '`(' . $regex_quote . ')%[dfFs]\1`';
			if ( preg_match_all( $regex, $content, $matches ) > 0 ) {
				if ( ! empty( $matches[0] ) ) {
					foreach ( $matches[0] as $match ) {
						$this->phpcsFile->addError(
							'Simple placeholders should not be quoted in the query string in $wpdb->prepare(). Found: %s.',
							$i,
							'QuotedSimplePlaceholder',
							array( $match )
						);
					}
				}
				unset( $match, $matches );
			}

			/*
			 * Analyse the query for quoted identifier placeholders.
			 */
			$regex = '/(' . $regex_quote . '|`)(?<placeholder>' . self::PREPARE_PLACEHOLDER_REGEX . ')\1/x';
			if ( preg_match_all( $regex, $content, $matches ) > 0 ) {
				if ( ! empty( $matches ) ) {
					foreach ( $matches['placeholder'] as $index => $match ) {
						if ( 'i' === substr( $match, -1 ) ) {
							$this->phpcsFile->addError(
								'Placeholders used for identifiers (%%i) in the query string in $wpdb->prepare() are always quoted automagically. Please remove the surrounding quotes. Found: %s',
								$i,
								'QuotedIdentifierPlaceholder',
								array( $matches[0][ $index ] )
							);
						}
					}
				}
				unset( $index, $match, $matches );
			}

			/*
			 * Analyse the query for unquoted complex placeholders.
			 */
			$regex = '`(?<!' . $regex_quote . ')' . self::PREPARE_PLACEHOLDER_REGEX . '(?!' . $regex_quote . ')`x';
			if ( preg_match_all( $regex, $content, $matches ) > 0 ) {
				if ( ! empty( $matches[0] ) ) {
					foreach ( $matches[0] as $match ) {
						if ( substr( $match, -1 ) !== 'i' && preg_match( '`^%[dfFsi]$`', $match ) !== 1 ) { // Identifiers must always be unquoted.
							$this->phpcsFile->addWarning(
								'Complex placeholders used for values in the query string in $wpdb->prepare() will NOT be quoted automagically. Found: %s.',
								$i,
								'UnquotedComplexPlaceholder',
								array( $match )
							);
						}
					}
				}
				unset( $match, $matches );
			}

			/*
			 * Check for an ` IN (%s)` clause.
			 */
			$found_in = preg_match_all( '`\s+IN\s*\(\s*%s\s*\)`i', $content, $matches );
			if ( $found_in > 0 ) {
				$valid_in_clauses['uses_in'] += $found_in;
			}
			unset( $found_in );
		}

		if ( false === $text_string_tokens_found ) {
			// Query string passed in as a variable or function call, nothing to examine.
			return;
		}

		if ( 0 === $total_placeholders ) {
			if ( 1 === $total_parameters ) {
				if ( false === $variable_found && false === $sql_wildcard_found ) {
					/*
					 * Only throw this warning if the PreparedSQL sniff won't throw one about
					 * variables being found.
					 * Also don't throw it if we just advised to use a replacement variable to pass a
					 * string containing an SQL wildcard.
					 */
					$this->phpcsFile->addWarning(
						'It is not necessary to prepare a query which doesn\'t use variable replacement.',
						$i,
						'UnnecessaryPrepare'
					);
				}
			} elseif ( 0 === $valid_in_clauses['uses_in'] ) {
				$this->phpcsFile->addWarning(
					'Replacement variables found, but no valid placeholders found in the query.',
					$i,
					'UnfinishedPrepare'
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
		unset( $replacements['query'], $replacements[1] ); // Remove the query param, whether passed positionally or named.

		// The parameters may have been passed as an array in the variadic $args parameter.
		$args_param = PassedParameters::getParameterFromStack( $parameters, 2, 'args' );
		if ( false !== $args_param && 2 === $total_parameters ) {
			$next = $this->phpcsFile->findNext(
				Tokens::$emptyTokens,
				$args_param['start'],
				( $args_param['end'] + 1 ),
				true
			);

			if ( false !== $next
				&& ( \T_ARRAY === $this->tokens[ $next ]['code']
					|| ( isset( Collections::shortArrayListOpenTokensBC()[ $this->tokens[ $next ]['code'] ] )
						&& Arrays::isShortArray( $this->phpcsFile, $next ) === true ) )
			) {
				$replacements = PassedParameters::getParameters( $this->phpcsFile, $next );
			}
		}

		$total_replacements  = \count( $replacements );
		$total_placeholders -= $valid_in_clauses['adjustment_count'];

		// Bow out when `IN` clauses have been used which appear to be correct.
		if ( $valid_in_clauses['uses_in'] > 0
			&& $valid_in_clauses['uses_in'] === $valid_in_clauses['implode_fill']
			&& 1 === $total_replacements
		) {
			return;
		}

		/*
		 * Verify that the correct amount of replacements have been passed.
		 */
		if ( $total_replacements !== $total_placeholders ) {
			$this->phpcsFile->addWarning(
				'Incorrect number of replacements passed to $wpdb->prepare(). Found %d replacement parameters, expected %d.',
				$stackPtr,
				'ReplacementsWrongNumber',
				array( $total_replacements, $total_placeholders )
			);
		}
	}

	/**
	 * Retrieve a regex snippet to recognize and remember quotes based on the quote style
	 * used in the original string (if any).
	 *
	 * This allows for recognizing `"` and `\'` in single quoted strings,
	 * recognizing `'` and `\"` in double quotes strings and `'` and `"`when the quote
	 * style is unknown or it is a non-quoted string (heredoc/nowdoc and such).
	 *
	 * @since 0.14.0
	 *
	 * @param string $stripped_content Text string content without surrounding quotes.
	 * @param string $original_content Original content for the same text string.
	 *
	 * @return string
	 */
	protected function get_regex_quote_snippet( $stripped_content, $original_content ) {
		$regex_quote = $this->regex_quote;

		if ( $original_content !== $stripped_content ) {
			$quote_style = $original_content[0];

			if ( '"' === $quote_style ) {
				$regex_quote = '\\\\"|\'';
			} elseif ( "'" === $quote_style ) {
				$regex_quote = '"|\\\\\'';
			}
		}

		return $regex_quote;
	}

	/**
	 * Analyse a sprintf() query wrapper to see if it contains a specific code pattern
	 * to deal correctly with `IN` queries.
	 *
	 * The pattern we are searching for is:
	 * `sprintf( 'query ....', implode( ',', array_fill( 0, count( $something ), '%s' ) ) )`
	 *
	 * @since 0.14.0
	 *
	 * @param array $sprintf_params Parameters details for the sprintf call.
	 *
	 * @return int The number of times the pattern was found in the replacements.
	 */
	protected function analyse_sprintf( $sprintf_params ) {
		$found = 0;

		unset( $sprintf_params[1] ); // Remove the positionally passed $format param.

		foreach ( $sprintf_params as $sprintf_param ) {
			$implode = $this->phpcsFile->findNext(
				Tokens::$emptyTokens + array( \T_NS_SEPARATOR => \T_NS_SEPARATOR ),
				$sprintf_param['start'],
				$sprintf_param['end'],
				true
			);
			if ( \T_STRING === $this->tokens[ $implode ]['code']
				&& 'implode' === strtolower( $this->tokens[ $implode ]['content'] )
			) {
				if ( $this->analyse_implode( $implode ) === true ) {
					++$found;
				}
			}
		}

		return $found;
	}

	/**
	 * Analyse an implode() function call to see if it contains a specific code pattern
	 * to dynamically create placeholders.
	 *
	 * The pattern we are searching for is:
	 * `implode( ',', array_fill( 0, count( $something ), '%s' ) )`
	 *
	 * This pattern presumes unquoted placeholders!
	 *
	 * Identifiers (%i) are not supported, as this function is designed to work
	 * with `IN()`, which contains a list of values. In the future, it should
	 * be possible to simplify code using the implode/array_fill pattern to
	 * use a variable number of identifiers, e.g. `CONCAT(%...i)`,
	 * https://core.trac.wordpress.org/ticket/54042
	 *
	 * @since 0.14.0
	 *
	 * @param int $implode_token The stackPtr to the implode function call.
	 *
	 * @return bool True if the pattern is found, false otherwise.
	 */
	protected function analyse_implode( $implode_token ) {
		$implode_params = PassedParameters::getParameters( $this->phpcsFile, $implode_token );
		if ( empty( $implode_params ) || \count( $implode_params ) !== 2 ) {
			return false;
		}

		$implode_separator_param = PassedParameters::getParameterFromStack( $implode_params, 1, 'separator' );
		if ( false === $implode_separator_param
			|| preg_match( '`^(["\']), ?\1$`', $implode_separator_param['clean'] ) !== 1
		) {
			return false;
		}

		$implode_array_param = PassedParameters::getParameterFromStack( $implode_params, 2, 'array' );
		if ( false === $implode_array_param ) {
			return false;
		}

		$array_fill = $this->phpcsFile->findNext(
			Tokens::$emptyTokens + array( \T_NS_SEPARATOR => \T_NS_SEPARATOR ),
			$implode_array_param['start'],
			$implode_array_param['end'],
			true
		);

		if ( \T_STRING !== $this->tokens[ $array_fill ]['code']
			|| 'array_fill' !== strtolower( $this->tokens[ $array_fill ]['content'] )
		) {
			return false;
		}

		$array_fill_value_param = PassedParameters::getParameter( $this->phpcsFile, $array_fill, 3, 'value' );
		if ( false === $array_fill_value_param ) {
			return false;
		}

		if ( "'%i'" === $array_fill_value_param['clean']
			|| '"%i"' === $array_fill_value_param['clean']
		) {
			$firstNonEmpty = $this->phpcsFile->findNext( Tokens::$emptyTokens, $array_fill_value_param['start'], $array_fill_value_param['end'], true );

			$this->phpcsFile->addError(
				'The %i placeholder cannot be used within SQL `IN()` clauses.',
				$firstNonEmpty,
				'IdentifierWithinIN'
			);
			return false;
		}

		return (bool) preg_match( '`^(["\'])%[dfFs]\1$`', $array_fill_value_param['clean'] );
	}
}
