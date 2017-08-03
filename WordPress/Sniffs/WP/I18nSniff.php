<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\WP;

use WordPress\Sniff;
use WordPress\PHPCSHelper;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Makes sure WP internationalization functions are used properly.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/internationalization/
 * @link    https://developer.wordpress.org/plugins/internationalization/
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.10.0
 * @since   0.11.0 - Now also checks for translators comments.
 *                 - Now has the ability to handle text-domain set via the command-line
 *                   as a comma-delimited list.
 *                   `phpcs --runtime-set text_domain my-slug,default`
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class I18nSniff extends Sniff {

	/**
	 * These Regexes copied from http://php.net/manual/en/function.sprintf.php#93552
	 * and adjusted for better precision and updated specs.
	 */
	const SPRINTF_PLACEHOLDER_REGEX = '/(?:
		(?<!%)                     # Don\'t match a literal % (%%).
		(
			%                          # Start of placeholder.
			(?:[0-9]+\$)?              # Optional ordering of the placeholders.
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
			[bcdeEfFgGosuxX]           # Type specifier.
		)
	)/x';

	/**
	 * "Unordered" means there's no position specifier: '%s', not '%2$s'.
	 */
	const UNORDERED_SPRINTF_PLACEHOLDER_REGEX = '/(?:
		(?<!%)                     # Don\'t match a literal % (%%).
		%                          # Start of placeholder.
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
		[bcdeEfFgGosuxX]           # Type specifier.
	)/x';

	/**
	 * Text domain.
	 *
	 * @todo Eventually this should be able to be auto-supplied via looking at $this->phpcsFile->getFilename()
	 * @link https://youtrack.jetbrains.com/issue/WI-17740
	 *
	 * @var string[]|string
	 */
	public $text_domain;

	/**
	 * The I18N functions in use in WP.
	 *
	 * @since 0.10.0
	 * @since 0.11.0 Changed visibility from public to protected.
	 *
	 * @var array <string function name> => <string function type>
	 */
	protected $i18n_functions = array(
		'translate'                      => 'simple',
		'__'                             => 'simple',
		'esc_attr__'                     => 'simple',
		'esc_html__'                     => 'simple',
		'_e'                             => 'simple',
		'esc_attr_e'                     => 'simple',
		'esc_html_e'                     => 'simple',
		'translate_with_gettext_context' => 'context',
		'_x'                             => 'context',
		'_ex'                            => 'context',
		'esc_attr_x'                     => 'context',
		'esc_html_x'                     => 'context',
		'_n'                             => 'number',
		'_nx'                            => 'number_context',
		'_n_noop'                        => 'noopnumber',
		'_nx_noop'                       => 'noopnumber_context',
	);

	/**
	 * Toggle whether or not to check for translators comments for text string containing placeholders.
	 *
	 * Intended to make this part of the sniff unit testable, but can be used by end-users too,
	 * though they can just as easily disable this via the sniff code.
	 *
	 * @since 0.11.0
	 *
	 * @var bool
	 */
	public $check_translator_comments = true;

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_STRING,
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stack_ptr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stack_ptr ) {
		$token = $this->tokens[ $stack_ptr ];

		// Allow overruling the text_domain set in a ruleset via the command line.
		$cl_text_domain = trim( PHPCSHelper::get_config_data( 'text_domain' ) );
		if ( ! empty( $cl_text_domain ) ) {
			$this->text_domain = $cl_text_domain;
		}

		$this->text_domain = $this->merge_custom_array( $this->text_domain, array(), false );

		if ( '_' === $token['content'] ) {
			$this->phpcsFile->addError( 'Found single-underscore "_()" function when double-underscore expected.', $stack_ptr, 'SingleUnderscoreGetTextFunction' );
		}

		if ( ! isset( $this->i18n_functions[ $token['content'] ] ) ) {
			return;
		}
		$translation_function = $token['content'];

		if ( in_array( $translation_function, array( 'translate', 'translate_with_gettext_context' ), true ) ) {
			$this->phpcsFile->addWarning( 'Use of the "%s()" function is reserved for low-level API usage.', $stack_ptr, 'LowLevelTranslationFunction', array( $translation_function ) );
		}

		$func_open_paren_token = $this->phpcsFile->findNext( T_WHITESPACE, ( $stack_ptr + 1 ), null, true );
		if ( false === $func_open_paren_token || T_OPEN_PARENTHESIS !== $this->tokens[ $func_open_paren_token ]['code'] ) {
			 return;
		}

		$arguments_tokens = array();
		$argument_tokens  = array();
		$tokens           = $this->tokens;

		// Look at arguments.
		for ( $i = ( $func_open_paren_token + 1 ); $i < $this->tokens[ $func_open_paren_token ]['parenthesis_closer']; $i++ ) {
			$this_token                = $this->tokens[ $i ];
			$this_token['token_index'] = $i;
			if ( isset( Tokens::$emptyTokens[ $this_token['code'] ] ) ) {
				continue;
			}
			if ( T_COMMA === $this_token['code'] ) {
				$arguments_tokens[] = $argument_tokens;
				$argument_tokens    = array();
				continue;
			}

			// Merge consecutive single or double quoted strings (when they span multiple lines).
			if ( isset( Tokens::$textStringTokens[ $this_token['code'] ] ) ) {
				for ( $j = ( $i + 1 ); $j < $this->tokens[ $func_open_paren_token ]['parenthesis_closer']; $j++ ) {
					if ( $this_token['code'] === $this->tokens[ $j ]['code'] ) {
						$this_token['content'] .= $this->tokens[ $j ]['content'];
						$i                      = $j;
					} else {
						break;
					}
				}
			}
			$argument_tokens[] = $this_token;

			// Include everything up to and including the parenthesis_closer if this token has one.
			if ( ! empty( $this_token['parenthesis_closer'] ) ) {
				for ( $j = ( $i + 1 ); $j <= $this_token['parenthesis_closer']; $j++ ) {
					$tokens[ $j ]['token_index'] = $j;
					$argument_tokens[]           = $tokens[ $j ];
				}
				$i = $this_token['parenthesis_closer'];
			}
		}

		if ( ! empty( $argument_tokens ) ) {
			$arguments_tokens[] = $argument_tokens;
		}
		unset( $argument_tokens );

		$argument_assertions = array();
		if ( 'simple' === $this->i18n_functions[ $translation_function ] ) {
			$argument_assertions[] = array(
				'arg_name' => 'text',
				'tokens'   => array_shift( $arguments_tokens ),
			);
			$argument_assertions[] = array(
				'arg_name' => 'domain',
				'tokens'   => array_shift( $arguments_tokens ),
			);
		} elseif ( 'context' === $this->i18n_functions[ $translation_function ] ) {
			$argument_assertions[] = array(
				'arg_name' => 'text',
				'tokens'   => array_shift( $arguments_tokens ),
			);
			$argument_assertions[] = array(
				'arg_name' => 'context',
				'tokens'   => array_shift( $arguments_tokens ),
			);
			$argument_assertions[] = array(
				'arg_name' => 'domain',
				'tokens'   => array_shift( $arguments_tokens ),
			);
		} elseif ( 'number' === $this->i18n_functions[ $translation_function ] ) {
			$argument_assertions[] = array(
				'arg_name' => 'single',
				'tokens'   => array_shift( $arguments_tokens ),
			);
			$argument_assertions[] = array(
				'arg_name' => 'plural',
				'tokens'   => array_shift( $arguments_tokens ),
			);
			array_shift( $arguments_tokens );
			$argument_assertions[] = array(
				'arg_name' => 'domain',
				'tokens'   => array_shift( $arguments_tokens ),
			);
		} elseif ( 'number_context' === $this->i18n_functions[ $translation_function ] ) {
			$argument_assertions[] = array(
				'arg_name' => 'single',
				'tokens'   => array_shift( $arguments_tokens ),
			);
			$argument_assertions[] = array(
				'arg_name' => 'plural',
				'tokens'   => array_shift( $arguments_tokens ),
			);
			array_shift( $arguments_tokens );
			$argument_assertions[] = array(
				'arg_name' => 'context',
				'tokens'   => array_shift( $arguments_tokens ),
			);
			$argument_assertions[] = array(
				'arg_name' => 'domain',
				'tokens'   => array_shift( $arguments_tokens ),
			);
		} elseif ( 'noopnumber' === $this->i18n_functions[ $translation_function ] ) {
			$argument_assertions[] = array(
				'arg_name' => 'single',
				'tokens'   => array_shift( $arguments_tokens ),
			);
			$argument_assertions[] = array(
				'arg_name' => 'plural',
				'tokens' => array_shift( $arguments_tokens ),
			);
			$argument_assertions[] = array(
				'arg_name' => 'domain',
				'tokens'   => array_shift( $arguments_tokens ),
			);
		} elseif ( 'noopnumber_context' === $this->i18n_functions[ $translation_function ] ) {
			$argument_assertions[] = array(
				'arg_name' => 'single',
				'tokens'   => array_shift( $arguments_tokens ),
			);
			$argument_assertions[] = array(
				'arg_name' => 'plural',
				'tokens'   => array_shift( $arguments_tokens ),
			);
			$argument_assertions[] = array(
				'arg_name' => 'context',
				'tokens'   => array_shift( $arguments_tokens ),
			);
			$argument_assertions[] = array(
				'arg_name' => 'domain',
				'tokens'   => array_shift( $arguments_tokens ),
			);
		}

		if ( ! empty( $arguments_tokens ) ) {
			$this->phpcsFile->addError( 'Too many arguments for function "%s".', $func_open_paren_token, 'TooManyFunctionArgs', array( $translation_function ) );
		}

		foreach ( $argument_assertions as $argument_assertion_context ) {
			if ( empty( $argument_assertion_context['tokens'][0] ) ) {
				$argument_assertion_context['stack_ptr'] = $func_open_paren_token;
			} else {
				$argument_assertion_context['stack_ptr'] = $argument_assertion_context['tokens'][0]['token_index'];
			}
			$this->check_argument_tokens( $argument_assertion_context );
		}

		// For _n*() calls, compare the singular and plural strings.
		if ( false !== strpos( $this->i18n_functions[ $translation_function ], 'number' ) ) {
			$single_context = $argument_assertions[0];
			$plural_context = $argument_assertions[1];

			$this->compare_single_and_plural_arguments( $stack_ptr, $single_context, $plural_context );
		}

		if ( true === $this->check_translator_comments ) {
			$this->check_for_translator_comment( $stack_ptr, $argument_assertions );
		}
	}

	/**
	 * Check if supplied tokens represent a translation text string literal.
	 *
	 * @param array $context Context (@todo needs better description).
	 * @return bool
	 */
	protected function check_argument_tokens( $context ) {
		$stack_ptr = $context['stack_ptr'];
		$tokens    = $context['tokens'];
		$arg_name  = $context['arg_name'];
		$is_error  = empty( $context['warning'] );
		$content   = $tokens[0]['content'];

		if ( empty( $tokens ) || 0 === count( $tokens ) ) {
			$code = $this->string_to_errorcode( 'MissingArg' . ucfirst( $arg_name ) );
			if ( 'domain' !== $arg_name || ! empty( $this->text_domain ) ) {
				$this->addMessage( 'Missing $%s arg.', $stack_ptr, $is_error, $code, array( $arg_name ) );
			}
			return false;
		}
		if ( count( $tokens ) > 1 ) {
			$contents = '';
			foreach ( $tokens as $token ) {
				$contents .= $token['content'];
			}
			$code = $this->string_to_errorcode( 'NonSingularStringLiteral' . ucfirst( $arg_name ) );
			$this->addMessage( 'The $%s arg must be a single string literal, not "%s".', $stack_ptr, $is_error, $code, array( $arg_name, $contents ) );
			return false;
		}

		if ( in_array( $arg_name, array( 'text', 'single', 'plural' ), true ) ) {
			$this->check_text( $context );
		}

		if ( T_CONSTANT_ENCAPSED_STRING === $tokens[0]['code'] ) {
			if ( 'domain' === $arg_name && ! empty( $this->text_domain ) && ! in_array( $this->strip_quotes( $content ), $this->text_domain, true ) ) {
				$this->addMessage( 'Mismatch text domain. Expected \'%s\' but got %s.', $stack_ptr, $is_error, 'TextDomainMismatch', array( implode( "' or '", $this->text_domain ), $content ) );
				return false;
			}
			return true;
		}
		if ( T_DOUBLE_QUOTED_STRING === $tokens[0]['code'] || T_HEREDOC === $tokens[0]['code'] ) {
			$interpolated_variables = $this->get_interpolated_variables( $content );
			foreach ( $interpolated_variables as $interpolated_variable ) {
				$code = $this->string_to_errorcode( 'InterpolatedVariable' . ucfirst( $arg_name ) );
				$this->addMessage( 'The $%s arg must not contain interpolated variables. Found "$%s".', $stack_ptr, $is_error, $code, array( $arg_name, $interpolated_variable ) );
			}
			if ( ! empty( $interpolated_variables ) ) {
				return false;
			}
			if ( 'domain' === $arg_name && ! empty( $this->text_domain ) && ! in_array( $this->strip_quotes( $content ), $this->text_domain, true ) ) {
				$this->addMessage( 'Mismatch text domain. Expected \'%s\' but got %s.', $stack_ptr, $is_error, 'TextDomainMismatch', array( implode( "' or '", $this->text_domain ), $content ) );
				return false;
			}
			return true;
		}

		$code = $this->string_to_errorcode( 'NonSingularStringLiteral' . ucfirst( $arg_name ) );
		$this->addMessage( 'The $%s arg must be a single string literal, not "%s".', $stack_ptr, $is_error, $code, array( $arg_name, $content ) );
		return false;
	}

	/**
	 * Check for inconsistencies between single and plural arguments.
	 *
	 * @param int   $stack_ptr      The position of the current token in the stack.
	 * @param array $single_context Single context (@todo needs better description).
	 * @param array $plural_context Plural context (@todo needs better description).
	 * @return void
	 */
	protected function compare_single_and_plural_arguments( $stack_ptr, $single_context, $plural_context ) {
		$single_content = $single_context['tokens'][0]['content'];
		$plural_content = $plural_context['tokens'][0]['content'];

		preg_match_all( self::SPRINTF_PLACEHOLDER_REGEX, $single_content, $single_placeholders );
		$single_placeholders = $single_placeholders[0];

		preg_match_all( self::SPRINTF_PLACEHOLDER_REGEX, $plural_content, $plural_placeholders );
		$plural_placeholders = $plural_placeholders[0];

		// English conflates "singular" with "only one", described in the codex:
		// https://codex.wordpress.org/I18n_for_WordPress_Developers#Plurals .
		if ( count( $single_placeholders ) < count( $plural_placeholders ) ) {
			$error_string = 'Missing singular placeholder, needed for some languages. See https://codex.wordpress.org/I18n_for_WordPress_Developers#Plurals';
			$single_index = $single_context['tokens'][0]['token_index'];

			$this->phpcsFile->addError( $error_string, $single_index, 'MissingSingularPlaceholder' );
		}

		// Reordering is fine, but mismatched placeholders is probably wrong.
		sort( $single_placeholders );
		sort( $plural_placeholders );

		if ( $single_placeholders !== $plural_placeholders ) {
			$this->phpcsFile->addWarning( 'Mismatched placeholders is probably an error', $stack_ptr, 'MismatchedPlaceholders' );
		}
	}

	/**
	 * Check the string itself for problems.
	 *
	 * @param array $context Context (@todo needs better description).
	 * @return void
	 */
	protected function check_text( $context ) {
		$stack_ptr = $context['stack_ptr'];
		$arg_name  = $context['arg_name'];
		$content   = $context['tokens'][0]['content'];
		$is_error  = empty( $context['warning'] );

		// UnorderedPlaceholders: Check for multiple unordered placeholders.
		$unordered_matches_count = preg_match_all( self::UNORDERED_SPRINTF_PLACEHOLDER_REGEX, $content, $unordered_matches );
		$unordered_matches       = $unordered_matches[0];
		$all_matches_count       = preg_match_all( self::SPRINTF_PLACEHOLDER_REGEX, $content, $all_matches );

		if ( $unordered_matches_count > 0 && $unordered_matches_count !== $all_matches_count && $all_matches_count > 1 ) {
			$code = $this->string_to_errorcode( 'MixedOrderedPlaceholders' . ucfirst( $arg_name ) );
			$this->phpcsFile->addError(
				'Multiple placeholders should be ordered. Mix of ordered and non-ordered placeholders found. Found: %s.',
				$stack_ptr,
				$code,
				array( implode( ', ', $all_matches[0] ) )
			);

		} elseif ( $unordered_matches_count >= 2 ) {
			$code = $this->string_to_errorcode( 'UnorderedPlaceholders' . ucfirst( $arg_name ) );

			$suggestions     = array();
			$replace_regexes = array();
			$replacements    = array();
			for ( $i = 0; $i < $unordered_matches_count; $i++ ) {
				$to_insert         = ( $i + 1 );
				$to_insert        .= ( '"' !== $content[0] ) ? '$' : '\$';
				$suggestions[ $i ] = substr_replace( $unordered_matches[ $i ], $to_insert, 1, 0 );

				// Prepare the strings for use a regex.
				$replace_regexes[ $i ] = '`\Q' . $unordered_matches[ $i ] . '\E`';
				// Note: the initial \\ is a literal \, the four \ in the replacement translate to also to a literal \.
				$replacements[ $i ]    = str_replace( '\\', '\\\\', $suggestions[ $i ] );
				// Note: the $ needs escaping to prevent numeric sequences after the $ being interpreted as match replacements.
				$replacements[ $i ]    = str_replace( '$', '\\$', $replacements[ $i ] );
			}

			$fix = $this->addFixableMessage(
				'Multiple placeholders should be ordered. Expected \'%s\', but got %s.',
				$stack_ptr,
				$is_error,
				$code,
				array( implode( ', ', $suggestions ), implode( ', ', $unordered_matches ) )
			);

			if ( true === $fix ) {
				$fixed_str = preg_replace( $replace_regexes, $replacements, $content, 1 );

				$this->phpcsFile->fixer->replaceToken( $stack_ptr, $fixed_str );
			}
		}

		/*
		 * NoEmptyStrings.
		 *
		 * Strip placeholders and surrounding quotes.
		 */
		$non_placeholder_content = $this->strip_quotes( $content );
		$non_placeholder_content = preg_replace( self::SPRINTF_PLACEHOLDER_REGEX, '', $non_placeholder_content );

		if ( empty( $non_placeholder_content ) ) {
			$this->phpcsFile->addError( 'Strings should have translatable content', $stack_ptr, 'NoEmptyStrings' );
		}
	} // End check_text().

	/**
	 * Check for the presence of a translators comment if one of the text strings contains a placeholder.
	 *
	 * @param int   $stack_ptr  The position of the gettext call token in the stack.
	 * @param array $args       The function arguments.
	 * @return void
	 */
	protected function check_for_translator_comment( $stack_ptr, $args ) {
		foreach ( $args as $arg ) {
			if ( false === in_array( $arg['arg_name'], array( 'text', 'single', 'plural' ), true ) ) {
				continue;
			}

			if ( empty( $arg['tokens'] ) ) {
				continue;
			}

			foreach ( $arg['tokens'] as $token ) {
				if ( empty( $token['content'] ) ) {
					continue;
				}

				if ( preg_match( self::SPRINTF_PLACEHOLDER_REGEX, $token['content'], $placeholders ) < 1 ) {
					// No placeholders found.
					continue;
				}

				$previous_comment = $this->phpcsFile->findPrevious( Tokens::$commentTokens, ( $stack_ptr - 1 ) );

				if ( false !== $previous_comment ) {
					/*
					 * Check that the comment is either on the line before the gettext call or
					 * if it's not, that there is only whitespace between.
					 */
					$correctly_placed = false;

					if ( ( $this->tokens[ $previous_comment ]['line'] + 1 ) === $this->tokens[ $stack_ptr ]['line'] ) {
						$correctly_placed = true;
					} else {
						$next_non_whitespace = $this->phpcsFile->findNext( T_WHITESPACE, ( $previous_comment + 1 ), $stack_ptr, true );
						if ( false === $next_non_whitespace || $this->tokens[ $next_non_whitespace ]['line'] === $this->tokens[ $stack_ptr ]['line'] ) {
							// No non-whitespace found or next non-whitespace is on same line as gettext call.
							$correctly_placed = true;
						}
						unset( $next_non_whitespace );
					}

					/*
					 * Check that the comment starts with 'translators:'.
					 */
					if ( true === $correctly_placed ) {

						if ( T_COMMENT === $this->tokens[ $previous_comment ]['code'] ) {
							$comment_text = trim( $this->tokens[ $previous_comment ]['content'] );

							// If it's multi-line /* */ comment, collect all the parts.
							if ( '*/' === substr( $comment_text, -2 ) && '/*' !== substr( $comment_text, 0, 2 ) ) {
								for ( $i = ( $previous_comment - 1 ); 0 <= $i; $i-- ) {
									if ( T_COMMENT !== $this->tokens[ $i ]['code'] ) {
										break;
									}

									$comment_text = trim( $this->tokens[ $i ]['content'] ) . $comment_text;
								}
							}

							if ( true === $this->is_translators_comment( $comment_text ) ) {
								// Comment is ok.
								return;
							}
						} elseif ( T_DOC_COMMENT_CLOSE_TAG === $this->tokens[ $previous_comment ]['code'] ) {
							// If it's docblock comment (wrong style) make sure that it's a translators comment.
							$db_start      = $this->phpcsFile->findPrevious( T_DOC_COMMENT_OPEN_TAG, ( $previous_comment - 1 ) );
							$db_first_text = $this->phpcsFile->findNext( T_DOC_COMMENT_STRING, ( $db_start + 1 ),  $previous_comment );

							if ( true === $this->is_translators_comment( $this->tokens[ $db_first_text ]['content'] ) ) {
								$this->phpcsFile->addWarning(
									'A "translators:" comment must be a "/* */" style comment. Docblock comments will not be picked up by the tools to generate a ".pot" file.',
									$stack_ptr,
									'TranslatorsCommentWrongStyle'
								);
								return;
							}
						}
					}
				}

				// Found placeholders but no translators comment.
				$this->phpcsFile->addWarning(
					'A gettext call containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders.',
					$stack_ptr,
					'MissingTranslatorsComment'
				);
				return;
			}
		}

	} // End check_for_translator_comment().

	/**
	 * Check if a (collated) comment string starts with 'translators:'.
	 *
	 * @since 0.11.0
	 *
	 * @param string $content Comment string content.
	 * @return bool
	 */
	private function is_translators_comment( $content ) {
		if ( preg_match( '`^(?:(?://|/\*{1,2}) )?translators:`i', $content, $matches ) === 1 ) {
			return true;
		}
		return false;
	}

}
