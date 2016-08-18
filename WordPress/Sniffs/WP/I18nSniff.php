<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Makes sure WP internationalization functions are used properly.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/internationalization/
 * @link    https://developer.wordpress.org/plugins/internationalization/
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.10.0
 */
class WordPress_Sniffs_WP_I18nSniff extends WordPress_Sniff {

	/**
	 * Text domain.
	 *
	 * @todo Eventually this should be able to be auto-supplied via looking at $phpcs_file->getFilename()
	 * @link https://youtrack.jetbrains.com/issue/WI-17740
	 *
	 * @var string
	 */
	public $text_domain;

	/**
	 * Allow unit tests to override the supplied text_domain.
	 *
	 * @todo While it doesn't work, ideally this should be able to be done in \WordPress_Tests_WP_I18nUnitTest::setUp()
	 *
	 * @var string
	 */
	static $text_domain_override;

	/**
	 * The I18N functions in use in WP.
	 *
	 * @var array <string function name> => <string function type>
	 */
	public $i18n_functions = array(
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
	 * These Regexes copied from http://php.net/manual/en/function.sprintf.php#93552
	 *
	 * @var string
	 */
	public static $sprintf_placeholder_regex = '/(?:%%|(%(?:[0-9]+\$)?[+-]?(?:[ 0]|\'.)?-?[0-9]*(?:\.[0-9]+)?[bcdeufFos]))/';

	/**
	 * "Unordered" means there's no position specifier: '%s', not '%2$s'.
	 *
	 * @var string
	 */
	public static $unordered_sprintf_placeholder_regex = '/(?:%%|(?:%[+-]?(?:[ 0]|\'.)?-?[0-9]*(?:\.[0-9]+)?[bcdeufFosxX]))/';

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
	 * @param PHP_CodeSniffer_File $phpcs_file The file being scanned.
	 * @param int                  $stack_ptr  The position of the current token
	 *                                         in the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcs_file, $stack_ptr ) {
		$tokens = $phpcs_file->getTokens();
		$token  = $tokens[ $stack_ptr ];

		if ( ! empty( self::$text_domain_override ) ) {
			$this->text_domain = self::$text_domain_override;
		}

		if ( '_' === $token['content'] ) {
			$phpcs_file->addError( 'Found single-underscore "_()" function when double-underscore expected.', $stack_ptr, 'SingleUnderscoreGetTextFunction' );
		}

		if ( ! isset( $this->i18n_functions[ $token['content'] ] ) ) {
			return;
		}
		$translation_function = $token['content'];

		if ( in_array( $translation_function, array( 'translate', 'translate_with_gettext_context' ), true ) ) {
			$phpcs_file->addWarning( 'Use of the "%s()" function is reserved for low-level API usage.', $stack_ptr, 'LowLevelTranslationFunction', array( $translation_function ) );
		}

		$func_open_paren_token = $phpcs_file->findNext( T_WHITESPACE, ( $stack_ptr + 1 ), null, true );
		if ( ! $func_open_paren_token || T_OPEN_PARENTHESIS !== $tokens[ $func_open_paren_token ]['code'] ) {
			 return;
		}

		$arguments_tokens = array();
		$argument_tokens  = array();

		// Look at arguments.
		for ( $i = ( $func_open_paren_token + 1 ); $i < $tokens[ $func_open_paren_token ]['parenthesis_closer']; $i += 1 ) {
			$this_token                = $tokens[ $i ];
			$this_token['token_index'] = $i;
			if ( in_array( $this_token['code'], PHP_CodeSniffer_Tokens::$emptyTokens, true ) ) {
				continue;
			}
			if ( T_COMMA === $this_token['code'] ) {
				$arguments_tokens[] = $argument_tokens;
				$argument_tokens    = array();
				continue;
			}

			// Merge consecutive single or double quoted strings (when they span multiple lines).
			if ( T_CONSTANT_ENCAPSED_STRING === $this_token['code'] || T_DOUBLE_QUOTED_STRING === $this_token['code'] ) {
				for ( $j = ( $i + 1 ); $j < $tokens[ $func_open_paren_token ]['parenthesis_closer']; $j += 1 ) {
					if ( $this_token['code'] === $tokens[ $j ]['code'] ) {
						$this_token['content'] .= $tokens[ $j ]['content'];
						$i                      = $j;
					} else {
						break;
					}
				}
			}
			$argument_tokens[] = $this_token;

			// Include everything up to and including the parenthesis_closer if this token has one.
			if ( ! empty( $this_token['parenthesis_closer'] ) ) {
				for ( $j = ( $i + 1 ); $j <= $this_token['parenthesis_closer']; $j += 1 ) {
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
			$argument_assertions[] = array( 'arg_name' => 'text',    'tokens' => array_shift( $arguments_tokens ) );
			$argument_assertions[] = array( 'arg_name' => 'domain',  'tokens' => array_shift( $arguments_tokens ) );
		} elseif ( 'context' === $this->i18n_functions[ $translation_function ] ) {
			$argument_assertions[] = array( 'arg_name' => 'text',    'tokens' => array_shift( $arguments_tokens ) );
			$argument_assertions[] = array( 'arg_name' => 'context', 'tokens' => array_shift( $arguments_tokens ) );
			$argument_assertions[] = array( 'arg_name' => 'domain',  'tokens' => array_shift( $arguments_tokens ) );
		} elseif ( 'number' === $this->i18n_functions[ $translation_function ] ) {
			$argument_assertions[] = array( 'arg_name' => 'single',  'tokens' => array_shift( $arguments_tokens ) );
			$argument_assertions[] = array( 'arg_name' => 'plural',  'tokens' => array_shift( $arguments_tokens ) );
			array_shift( $arguments_tokens );
			$argument_assertions[] = array( 'arg_name' => 'domain',  'tokens' => array_shift( $arguments_tokens ) );
		} elseif ( 'number_context' === $this->i18n_functions[ $translation_function ] ) {
			$argument_assertions[] = array( 'arg_name' => 'single',  'tokens' => array_shift( $arguments_tokens ) );
			$argument_assertions[] = array( 'arg_name' => 'plural',  'tokens' => array_shift( $arguments_tokens ) );
			array_shift( $arguments_tokens );
			$argument_assertions[] = array( 'arg_name' => 'context', 'tokens' => array_shift( $arguments_tokens ) );
			$argument_assertions[] = array( 'arg_name' => 'domain',  'tokens' => array_shift( $arguments_tokens ) );
		} elseif ( 'noopnumber' === $this->i18n_functions[ $translation_function ] ) {
			$argument_assertions[] = array( 'arg_name' => 'single',  'tokens' => array_shift( $arguments_tokens ) );
			$argument_assertions[] = array( 'arg_name' => 'plural',  'tokens' => array_shift( $arguments_tokens ) );
			$argument_assertions[] = array( 'arg_name' => 'domain',  'tokens' => array_shift( $arguments_tokens ) );
		} elseif ( 'noopnumber_context' === $this->i18n_functions[ $translation_function ] ) {
			$argument_assertions[] = array( 'arg_name' => 'single',  'tokens' => array_shift( $arguments_tokens ) );
			$argument_assertions[] = array( 'arg_name' => 'plural',  'tokens' => array_shift( $arguments_tokens ) );
			$argument_assertions[] = array( 'arg_name' => 'context', 'tokens' => array_shift( $arguments_tokens ) );
			$argument_assertions[] = array( 'arg_name' => 'domain',  'tokens' => array_shift( $arguments_tokens ) );
		}

		if ( ! empty( $arguments_tokens ) ) {
			$phpcs_file->addError( 'Too many arguments for function "%s".', $func_open_paren_token, 'TooManyFunctionArgs', array( $translation_function ) );
		}

		foreach ( $argument_assertions as $argument_assertion_context ) {
			if ( empty( $argument_assertion_context['tokens'][0] ) ) {
				$argument_assertion_context['stack_ptr'] = $func_open_paren_token;
			} else {
				$argument_assertion_context['stack_ptr'] = $argument_assertion_context['tokens'][0]['token_index'];
			}
			call_user_func( array( $this, 'check_argument_tokens' ), $phpcs_file, $argument_assertion_context );
		}

		// For _n*() calls, compare the singular and plural strings.
		if ( false !== strpos( $this->i18n_functions[ $translation_function ], 'number' ) ) {
			$single_context = $argument_assertions[0];
			$plural_context = $argument_assertions[1];

			$this->compare_single_and_plural_arguments( $phpcs_file, $stack_ptr, $single_context, $plural_context );
		}
	}

	/**
	 * Check if supplied tokens represent a translation text string literal.
	 *
	 * @param PHP_CodeSniffer_File $phpcs_file The file being scanned.
	 * @param array                $context    Context (@todo needs better description).
	 * @return bool
	 */
	protected function check_argument_tokens( PHP_CodeSniffer_File $phpcs_file, $context ) {
		$stack_ptr = $context['stack_ptr'];
		$tokens    = $context['tokens'];
		$arg_name  = $context['arg_name'];
		$method    = empty( $context['warning'] ) ? 'addError' : 'addWarning';
		$content   = $tokens[0]['content'];

		if ( 0 === count( $tokens ) ) {
			$code = 'MissingArg' . ucfirst( $arg_name );
			if ( 'domain' !== $arg_name || ! empty( $this->text_domain ) ) {
				$phpcs_file->$method( 'Missing $%s arg.', $stack_ptr, $code, array( $arg_name ) );
			}
			return false;
		}
		if ( count( $tokens ) > 1 ) {
			$contents = '';
			foreach ( $tokens as $token ) {
				$contents .= $token['content'];
			}
			$code = 'NonSingularStringLiteral' . ucfirst( $arg_name );
			$phpcs_file->$method( 'The $%s arg must be a single string literal, not "%s".', $stack_ptr, $code, array( $arg_name, $contents ) );
			return false;
		}

		if ( in_array( $arg_name, array( 'text', 'single', 'plural' ), true ) ) {
			$this->check_text( $phpcs_file, $context );
		}

		if ( T_CONSTANT_ENCAPSED_STRING === $tokens[0]['code'] ) {
			if ( 'domain' === $arg_name && ! empty( $this->text_domain ) && trim( $content, '\'""' ) !== $this->text_domain ) {
				$phpcs_file->$method( 'Mismatch text domain. Expected \'%s\' but got %s.', $stack_ptr, 'TextDomainMismatch', array( $this->text_domain, $content ) );
				return false;
			}
			return true;
		}
		if ( T_DOUBLE_QUOTED_STRING === $tokens[0]['code'] ) {
			$interpolated_variables = $this->get_interpolated_variables( $content );
			foreach ( $interpolated_variables as $interpolated_variable ) {
				$code = 'InterpolatedVariable' . ucfirst( $arg_name );
				$phpcs_file->$method( 'The $%s arg must not contain interpolated variables. Found "$%s".', $stack_ptr, $code, array( $arg_name, $interpolated_variable ) );
			}
			if ( ! empty( $interpolated_variables ) ) {
				return false;
			}
			if ( 'domain' === $arg_name && ! empty( $this->text_domain ) && trim( $content, '\'""' ) !== $this->text_domain ) {
				$phpcs_file->$method( 'Mismatch text domain. Expected \'%s\' but got %s.', $stack_ptr, 'TextDomainMismatch', array( $this->text_domain, $content ) );
				return false;
			}
			return true;
		}

		$code = 'NonSingularStringLiteral' . ucfirst( $arg_name );
		$phpcs_file->$method( 'The $%s arg should be single a string literal, not "%s".', $stack_ptr, $code, array( $arg_name, $content ) );
		return false;
	}

	/**
	 * Check for inconsistencies between single and plural arguments.
	 *
	 * @param PHP_CodeSniffer_File $phpcs_file     The file being scanned.
	 * @param int                  $stack_ptr      The position of the current token
	 *                                             in the stack passed in $tokens.
	 * @param array                $single_context Single context (@todo needs better description).
	 * @param array                $plural_context Plural context (@todo needs better description).
	 * @return void
	 */
	protected function compare_single_and_plural_arguments( PHP_CodeSniffer_File $phpcs_file, $stack_ptr, $single_context, $plural_context ) {
		$single_content = $single_context['tokens'][0]['content'];
		$plural_content = $plural_context['tokens'][0]['content'];

		preg_match_all( self::$sprintf_placeholder_regex, $single_content, $single_placeholders );
		$single_placeholders = $single_placeholders[0];

		preg_match_all( self::$sprintf_placeholder_regex, $plural_content, $plural_placeholders );
		$plural_placeholders = $plural_placeholders[0];

		// English conflates "singular" with "only one", described in the codex:
		// https://codex.wordpress.org/I18n_for_WordPress_Developers#Plurals .
		if ( count( $single_placeholders ) < count( $plural_placeholders ) ) {
			$error_string = 'Missing singular placeholder, needed for some languages. See https://codex.wordpress.org/I18n_for_WordPress_Developers#Plurals';
			$single_index = $single_context['tokens'][0]['token_index'];

			$phpcs_file->addError( $error_string, $single_index, 'MissingSingularPlaceholder' );
		}

		// Reordering is fine, but mismatched placeholders is probably wrong.
		sort( $single_placeholders );
		sort( $plural_placeholders );

		if ( $single_placeholders !== $plural_placeholders ) {
			$phpcs_file->addWarning( 'Mismatched placeholders is probably an error', $stack_ptr, 'MismatchedPlaceholders' );
		}
	}

	/**
	 * Check the string itself for problems.
	 *
	 * @param PHP_CodeSniffer_File $phpcs_file The file being scanned.
	 * @param array                $context    Context (@todo needs better description).
	 * @return void
	 */
	protected function check_text( PHP_CodeSniffer_File $phpcs_file, $context ) {
		$stack_ptr      = $context['stack_ptr'];
		$arg_name       = $context['arg_name'];
		$content        = $context['tokens'][0]['content'];
		$fixable_method = empty( $context['warning'] ) ? 'addFixableError' : 'addFixableWarning';

		// UnorderedPlaceholders: Check for multiple unordered placeholders.
		preg_match_all( self::$unordered_sprintf_placeholder_regex, $content, $unordered_matches );
		$unordered_matches       = $unordered_matches[0];
		$unordered_matches_count = count( $unordered_matches );

		if ( $unordered_matches_count >= 2 ) {
			$code = 'UnorderedPlaceholders' . ucfirst( $arg_name );

			$suggestions = array();
			for ( $i = 0; $i < $unordered_matches_count; $i++ ) {
				$suggestions[ $i ] = substr_replace( $unordered_matches[ $i ], ( $i + 1 ) . '$', 1, 0 );
			}

			$fix = $phpcs_file->$fixable_method(
				'Multiple placeholders should be ordered. Expected \'%s\', but got %s.',
				$stack_ptr,
				'UnorderedPlaceholders',
				array( join( ', ', $suggestions ), join( ',', $unordered_matches ) )
			);

			if ( true === $fix ) {
				$fixed_str = str_replace( $unordered_matches, $suggestions, $content );

				$phpcs_file->fixer->beginChangeset();
				$phpcs_file->fixer->replaceToken( $stack_ptr, $fixed_str );
				$phpcs_file->fixer->endChangeset();
			}
		}

		/*
		 * NoEmptyStrings.
		 *
		 * Strip placeholders and surrounding quotes.
		 */
		$non_placeholder_content = trim( $content, "'" );
		$non_placeholder_content = preg_replace( self::$sprintf_placeholder_regex, '', $non_placeholder_content );

		if ( empty( $non_placeholder_content ) ) {
			$phpcs_file->addError( 'Strings should have translatable content', $stack_ptr, 'NoEmptyStrings' );
		}
	} // end check_text()

}
