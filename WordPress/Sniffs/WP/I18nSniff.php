<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WP;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\Utils\MessageHelper;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\AbstractFunctionParameterSniff;
use WordPressCS\WordPress\Helpers\RulesetPropertyHelper;
use XMLReader;

/**
 * Makes sure WP internationalization functions are used properly.
 *
 * @link https://make.wordpress.org/core/handbook/best-practices/internationalization/
 * @link https://developer.wordpress.org/plugins/internationalization/
 *
 * @since 0.10.0
 * @since 0.11.0 - Now also checks for translators comments.
 *               - Now has the ability to handle text domain set via the command-line
 *                 as a comma-delimited list.
 *                 `phpcs --runtime-set text_domain my-slug,default`
 * @since 0.13.0 Class name changed: this class is now namespaced.
 * @since 1.0.0  This class now extends the WordPressCS native
 *               `AbstractFunctionRestrictionSniff` class.
 *               The parent `exclude` property is, however, disabled as it
 *               would disable the whole sniff.
 * @since 3.0.0  This class now extends the WordPressCS native
 *               `AbstractFunctionParameterSniff` class.
 *               The parent `exclude` property is still disabled.
 */
final class I18nSniff extends AbstractFunctionParameterSniff {

	/**
	 * These Regexes were originally copied from https://www.php.net/function.sprintf#93552
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
			[bcdeEfFgGhHosuxX]           # Type specifier.
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
		[bcdeEfFgGhHosuxX]           # Type specifier.
	)/x';

	/**
	 * Text domain.
	 *
	 * @var string[]
	 */
	public $text_domain;

	/**
	 * The I18N functions in use in WP.
	 *
	 * @since 0.10.0
	 * @since 0.11.0 Changed visibility from public to protected.
	 *
	 * @var array<string, string> Key is function name, value is the function type.
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
	 * Whether or not the `default` text domain is one of the allowed text domains.
	 *
	 * @since 0.14.0
	 *
	 * @var bool
	 */
	private $text_domain_contains_default = false;

	/**
	 * Whether or not the `default` text domain is the only allowed text domain.
	 *
	 * @since 0.14.0
	 *
	 * @var bool
	 */
	private $text_domain_is_default = false;

	/**
	 * Parameter specifications for the functions in each group.
	 *
	 * {@internal Even when not all parameters will be examined, the parameter list should still
	 * be complete in the below array to allow for a correct "total parameters" calculation.}
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, array> Array of the parameter positions and names.
	 */
	private $parameter_specs = array(
		'simple' => array(
			1 => 'text',
			2 => 'domain',
		),
		'context' => array(
			1 => 'text',
			2 => 'context',
			3 => 'domain',
		),
		'number' => array(
			1 => 'single',
			2 => 'plural',
			3 => 'number',
			4 => 'domain',
		),
		'number_context' => array(
			1 => 'single',
			2 => 'plural',
			3 => 'number',
			4 => 'context',
			5 => 'domain',
		),
		'noopnumber' => array(
			1 => 'singular',
			2 => 'plural',
			3 => 'domain',
		),
		'noopnumber_context' => array(
			1 => 'singular',
			2 => 'plural',
			3 => 'context',
			4 => 'domain',
		),
	);

	/**
	 * Groups of functions to restrict.
	 *
	 * Example: groups => array(
	 *  'lambda' => array(
	 *      'type'      => 'error' | 'warning',
	 *      'message'   => 'Use anonymous functions instead please!',
	 *      'functions' => array( 'file_get_contents', 'create_function' ),
	 *  )
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(
			'i18n' => array(
				'functions' => array_keys( $this->i18n_functions ),
			),
			'typos' => array(
				'functions' => array(
					'_',
				),
			),
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @since 1.0.0 Defers to the abstractFunctionRestriction sniff for determining
	 *              whether something is a function call. The logic after that has
	 *              been split off to the `process_matched_token()` method.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {

		// Reset defaults.
		$this->text_domain_contains_default = false;
		$this->text_domain_is_default       = false;

		// Allow overruling the text_domain set in a ruleset via the command line.
		$cl_text_domain = Helper::getConfigData( 'text_domain' );
		if ( ! empty( $cl_text_domain ) ) {
			$cl_text_domain = trim( $cl_text_domain );
			if ( '' !== $cl_text_domain ) {
				$this->text_domain = array_filter( array_map( 'trim', explode( ',', $cl_text_domain ) ) );
			}
		}

		$this->text_domain = RulesetPropertyHelper::merge_custom_array( $this->text_domain, array(), false );

		if ( ! empty( $this->text_domain ) ) {
			if ( \in_array( 'default', $this->text_domain, true ) ) {
				$this->text_domain_contains_default = true;
				if ( \count( $this->text_domain ) === 1 ) {
					$this->text_domain_is_default = true;
				}
			}
		}

		// Prevent exclusion of the i18n group.
		$this->exclude = array();

		parent::process_token( $stackPtr );
	}

	/**
	 * Process a matched token.
	 *
	 * @since 1.0.0 Logic split off from the `process_token()` method.
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched
	 *                                in lowercase.
	 *
	 * @return void
	 */
	public function process_matched_token( $stackPtr, $group_name, $matched_content ) {

		$func_open_paren_token = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );
		if ( ! isset( $this->tokens[ $func_open_paren_token ]['parenthesis_closer'] ) ) {
			// Live coding, parse error or not a function call.
			return;
		}

		if ( 'typos' === $group_name && '_' === $matched_content ) {
			$this->phpcsFile->addError(
				'Found single-underscore "_()" function when double-underscore expected.',
				$stackPtr,
				'SingleUnderscoreGetTextFunction'
			);
			return;
		}

		if ( 'translate' === $matched_content || 'translate_with_gettext_context' === $matched_content ) {
			$this->phpcsFile->addWarning(
				'Use of the "%s()" function is reserved for low-level API usage.',
				$stackPtr,
				'LowLevelTranslationFunction',
				array( $matched_content )
			);
		}

		parent::process_matched_token( $stackPtr, $group_name, $matched_content );
	}

	/**
	 * Process the function if no parameters were found.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched
	 *                                in lowercase.
	 *
	 * @return void
	 */
	public function process_no_parameters( $stackPtr, $group_name, $matched_content ) {
		$function_param_specs = $this->parameter_specs[ $this->i18n_functions[ $matched_content ] ];

		foreach ( $function_param_specs as $param_name ) {
			$error_code = MessageHelper::stringToErrorcode( 'MissingArg' . ucfirst( $param_name ) );
			$this->phpcsFile->addError(
				'Missing $%s parameter in function call to %s().',
				$stackPtr,
				$error_code,
				array( $param_name, $matched_content )
			);
		}
	}

	/**
	 * Process the parameters of a matched function.
	 *
	 * @since 3.0.0
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
		 * Retrieve the individual parameters from the array in a way that we know which is which.
		 */
		$parameter_details = array();

		$function_param_specs = $this->parameter_specs[ $this->i18n_functions[ $matched_content ] ];
		$expected_args        = count( $function_param_specs );

		foreach ( $function_param_specs as $position => $name ) {
			if ( 'number' === $name ) {
				// This sniff does not examine the $number parameter.
				continue;
			}

			$parameter_details[ $name ] = PassedParameters::getParameterFromStack( $parameters, $position, $name );
		}

		/*
		 * Examine the individual parameters.
		 */
		$this->check_argument_count( $stackPtr, $matched_content, $parameters, $expected_args );

		foreach ( $parameter_details as $param_name => $param_info ) {
			$is_string_literal = $this->check_argument_is_string_literal( $stackPtr, $matched_content, $param_name, $param_info );

			/*
			 * If the parameter exists, remember whether the argument was a valid string literal.
			 * This is used in a few places to determine whether the checks which examine a text string should run.
			 */
			if ( false !== $param_info ) {
				$parameter_details[ $param_name ]['is_string_literal'] = $is_string_literal;
			}

			if ( false === $is_string_literal ) {
				continue;
			}

			if ( 'domain' === $param_name ) {
				$this->check_textdomain_matches( $matched_content, $param_name, $param_info );
			}

			if ( \in_array( $param_name, array( 'text', 'single', 'singular', 'plural' ), true ) ) {
				$this->check_placeholders_in_string( $matched_content, $param_name, $param_info );
				$has_content = $this->check_string_has_translatable_content( $matched_content, $param_name, $param_info );
				if ( true === $has_content ) {
					$this->check_string_has_no_html_wrapper( $matched_content, $param_name, $param_info );
				}
			}
		}

		/*
		 * For _n*() calls, compare the singular and plural strings.
		 *
		 * If either of the arguments is missing, empty or has more than 1 token, skip out.
		 * An error for that will already have been reported via the `check_argument_is_string_literal()` method.
		 */
		$single_details = null;
		if ( isset( $parameter_details['single'] ) ) {
			$single_details = $parameter_details['single'];
		} elseif ( isset( $parameter_details['singular'] ) ) {
			$single_details = $parameter_details['singular'];
		}

		if ( isset( $single_details, $parameter_details['plural'] )
			&& false !== $single_details
			&& false !== $parameter_details['plural']
			&& true === $single_details['is_string_literal']
			&& true === $parameter_details['plural']['is_string_literal']
		) {
			$this->compare_single_and_plural_arguments( $stackPtr, $single_details, $parameter_details['plural'] );
		}

		/*
		 * Check if a translators comments is necessary and if so, if it exists.
		 */
		$this->check_for_translator_comment( $stackPtr, $matched_content, $parameter_details );
	}

	/**
	 * Verify that there are no superfluous function arguments.
	 *
	 * @since 3.0.0 Check moved from the `process_matched_token()` method to this method.
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $matched_content The token content (function name) which was matched
	 *                                in lowercase.
	 * @param array  $parameters      The parameters array.
	 * @param int    $expected_count  The expected number of passed arguments.
	 *
	 * @return void
	 */
	private function check_argument_count( $stackPtr, $matched_content, $parameters, $expected_count ) {
		$actual_count = count( $parameters );
		if ( $actual_count > $expected_count ) {
			$this->phpcsFile->addError(
				'Too many parameters passed to function "%s()". Expected: %s parameters, received: %s',
				$stackPtr,
				'TooManyFunctionArgs',
				array( $matched_content, $expected_count, $actual_count )
			);
		}
	}

	/**
	 * Check if an arbitrary function call parameter is a text string literal suitable for use in the translation functions.
	 *
	 * Will also check and warn about missing parameters.
	 *
	 * @since 3.0.0 Most of the logic in this method used to be contained in the, now removed, `check_argument_tokens()` method.
	 *
	 * @param int         $stackPtr        The position of the current token in the stack.
	 * @param string      $matched_content The token content (function name) which was matched
	 *                                     in lowercase.
	 * @param string      $param_name      The name of the parameter being examined.
	 * @param array|false $param_info      Parameter info array for an individual parameter,
	 *                                     as received from the PassedParemeters class.
	 *
	 * @return bool Whether or not the argument is a string literal.
	 */
	private function check_argument_is_string_literal( $stackPtr, $matched_content, $param_name, $param_info ) {
		/*
		 * Check if the parameter was supplied.
		 */
		if ( false === $param_info || '' === $param_info['clean'] ) {
			$error_code = MessageHelper::stringToErrorcode( 'MissingArg' . ucfirst( $param_name ) );

			/*
			 * Special case the text domain parameter, which is allowed to be "missing"
			 * when set to `default` (= WP Core translation).
			 */
			if ( 'domain' === $param_name ) {
				if ( empty( $this->text_domain ) ) {
					// If no text domain is passed, presume WP Core.
					return false;
				}

				if ( true === $this->text_domain_is_default ) {
					return false;
				}

				if ( true === $this->text_domain_contains_default ) {
					$this->phpcsFile->addWarning(
						'Missing $%s parameter in function call to %s(). If this text string is supposed to use a WP Core translation, use the "default" text domain.',
						$stackPtr,
						$error_code . 'Default',
						array( $param_name, $matched_content )
					);
					return false;
				}
			}

			$this->phpcsFile->addError(
				'Missing $%s parameter in function call to %s().',
				$stackPtr,
				$error_code,
				array( $param_name, $matched_content )
			);

			return false;
		}

		/*
		 * Check if the parameter consists of one singular text string literal.
		 * Heredoc/nowdocs not allowed. Multi-line single/double quoted strings are allowed.
		 */
		$first_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, $param_info['start'], ( $param_info['end'] + 1 ), true );
		for ( $i = $first_non_empty; $i <= $param_info['end']; $i++ ) {
			if ( isset( Tokens::$emptyTokens[ $this->tokens[ $i ]['code'] ] ) ) {
				continue;
			}

			if ( isset( Tokens::$stringTokens[ $this->tokens[ $i ]['code'] ] ) === false ) {
				$error_code = MessageHelper::stringToErrorcode( 'NonSingularStringLiteral' . ucfirst( $param_name ) );
				$this->phpcsFile->addError(
					'The $%s parameter must be a single text string literal. Found: %s',
					$first_non_empty,
					$error_code,
					array( $param_name, $param_info['clean'] )
				);
				return false;
			}
		}

		/*
		 * Make sure the text string does not contain any interpolated variable.
		 */
		if ( \T_DOUBLE_QUOTED_STRING === $this->tokens[ $first_non_empty ]['code'] ) {
			$error_code = MessageHelper::stringToErrorcode( 'InterpolatedVariable' . ucfirst( $param_name ) );

			$interpolated_variables = TextStrings::getEmbeds( $param_info['clean'] );
			foreach ( $interpolated_variables as $interpolated_variable ) {
				$this->phpcsFile->addError(
					'The $%s parameter must not contain interpolated variables or expressions. Found: %s',
					$first_non_empty,
					$error_code,
					array( $param_name, $interpolated_variable )
				);
			}

			if ( ! empty( $interpolated_variables ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check the correct text domain is being used.
	 *
	 * @since 3.0.0 The logic in this method used to be contained in the, now removed, `check_argument_tokens()` method.
	 *
	 * @param string      $matched_content The token content (function name) which was matched
	 *                                     in lowercase.
	 * @param string      $param_name      The name of the parameter being examined.
	 * @param array|false $param_info      Parameter info array for an individual parameter,
	 *                                     as received from the PassedParemeters class.
	 *
	 * @return void
	 */
	private function check_textdomain_matches( $matched_content, $param_name, $param_info ) {
		$stripped_content = TextStrings::stripQuotes( $param_info['clean'] );

		if ( empty( $this->text_domain ) && '' === $stripped_content ) {
			$first_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, $param_info['start'], ( $param_info['end'] + 1 ), true );

			$this->phpcsFile->addError(
				'The passed $domain should never be an empty string. Either pass a text domain or remove the parameter.',
				$first_non_empty,
				'EmptyTextDomain'
			);
		}

		if ( empty( $this->text_domain ) ) {
			// Nothing more to do, the other checks all depend on a text domain being known.
			return;
		}

		if ( ! \in_array( $stripped_content, $this->text_domain, true ) ) {
			$first_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, $param_info['start'], ( $param_info['end'] + 1 ), true );

			$this->phpcsFile->addError(
				'Mismatched text domain. Expected \'%s\' but got %s.',
				$first_non_empty,
				'TextDomainMismatch',
				array( implode( "' or '", $this->text_domain ), $param_info['clean'] )
			);
			return;
		}

		if ( true === $this->text_domain_is_default && 'default' === $stripped_content ) {
			$fixable    = false;
			$error      = 'No need to supply the text domain in function call to %s() when the only accepted text domain is "default".';
			$error_code = 'SuperfluousDefaultTextDomain';
			$data       = array( $matched_content );

			$first_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, $param_info['start'], ( $param_info['end'] + 1 ), true );

			// Prevent removing comments when auto-fixing.
			$remove_from = ( $param_info['start'] - 1 );
			$remove_to   = $first_non_empty;

			if ( isset( $param_info['name_token'] ) ) {
				$remove_from = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $param_info['name_token'] - 1 ), null, true );
				if ( \T_OPEN_PARENTHESIS === $this->tokens[ $remove_from ]['code'] ) {
					++$remove_from; // Don't remove the open parenthesis.

					/*
					 * Named param as first param in the function call, if we fix this, we need to
					 * remove the comma _after_ the parameter as well to prevent creating a parse error.
					 */
					$remove_to = $param_info['end'];
					if ( \T_COMMA === $this->tokens[ ( $param_info['end'] + 1 ) ]['code'] ) {
						++$remove_to; // Include the comma.
					}
				}
			}

			// Now, make sure there are no comments in the tokens we want to remove.
			if ( $this->phpcsFile->findNext( Tokens::$commentTokens, $remove_from, ( $remove_to + 1 ) ) === false ) {
				$fixable = true;
			}

			if ( false === $fixable ) {
				$this->phpcsFile->addWarning( $error, $first_non_empty, $error_code, $data );
				return;
			}

			$fix = $this->phpcsFile->addFixableWarning( $error, $first_non_empty, $error_code, $data );
			if ( true === $fix ) {
				$this->phpcsFile->fixer->beginChangeset();
				for ( $i = $remove_from; $i <= $remove_to; $i++ ) {
					$this->phpcsFile->fixer->replaceToken( $i, '' );
				}
				$this->phpcsFile->fixer->endChangeset();
			}
		}
	}

	/**
	 * Check the placeholders used in translatable text for common problems.
	 *
	 * @since 3.0.0 The logic in this method used to be contained in the, now removed, `check_text()` method.
	 *
	 * @param string      $matched_content The token content (function name) which was matched
	 *                                     in lowercase.
	 * @param string      $param_name      The name of the parameter being examined.
	 * @param array|false $param_info      Parameter info array for an individual parameter,
	 *                                     as received from the PassedParemeters class.
	 *
	 * @return void
	 */
	private function check_placeholders_in_string( $matched_content, $param_name, $param_info ) {
		$content = $param_info['clean'];

		// UnorderedPlaceholders: Check for multiple unordered placeholders.
		$unordered_matches_count = preg_match_all( self::UNORDERED_SPRINTF_PLACEHOLDER_REGEX, $content, $unordered_matches );
		$unordered_matches       = $unordered_matches[0];
		$all_matches_count       = preg_match_all( self::SPRINTF_PLACEHOLDER_REGEX, $content, $all_matches );

		if ( $unordered_matches_count > 0
			&& $unordered_matches_count !== $all_matches_count
			&& $all_matches_count > 1
		) {
			$first_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, $param_info['start'], ( $param_info['end'] + 1 ), true );
			$error_code      = MessageHelper::stringToErrorcode( 'MixedOrderedPlaceholders' . ucfirst( $param_name ) );

			$this->phpcsFile->addError(
				'Multiple placeholders in translatable strings should be ordered. Mix of ordered and non-ordered placeholders found. Found: "%s" in %s.',
				$first_non_empty,
				$error_code,
				array( implode( ', ', $all_matches[0] ), $param_info['clean'] )
			);
			return;
		}

		if ( $unordered_matches_count >= 2 ) {
			$first_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, $param_info['start'], ( $param_info['end'] + 1 ), true );
			$error_code      = MessageHelper::stringToErrorcode( 'UnorderedPlaceholders' . ucfirst( $param_name ) );

			$suggestions     = array();
			$replace_regexes = array();
			$replacements    = array();
			for ( $i = 0; $i < $unordered_matches_count; $i++ ) {
				$to_insert         = ( $i + 1 );
				$to_insert        .= ( '"' !== $content[0] ) ? '$' : '\$';
				$suggestions[ $i ] = substr_replace( $unordered_matches[ $i ], $to_insert, 1, 0 );

				// Prepare the strings for use in a regex.
				$replace_regexes[ $i ] = '`\Q' . $unordered_matches[ $i ] . '\E`';
				// Note: the initial \\ is a literal \, the four \ in the replacement translate also to a literal \.
				$replacements[ $i ] = str_replace( '\\', '\\\\', $suggestions[ $i ] );
				// Note: the $ needs escaping to prevent numeric sequences after the $ being interpreted as match replacements.
				$replacements[ $i ] = str_replace( '$', '\\$', $replacements[ $i ] );
			}

			$fix = $this->phpcsFile->addFixableError(
				'Multiple placeholders in translatable strings should be ordered. Expected "%s", but got "%s" in %s.',
				$first_non_empty,
				$error_code,
				array( implode( ', ', $suggestions ), implode( ', ', $unordered_matches ), $param_info['clean'] )
			);

			if ( true === $fix ) {
				$this->phpcsFile->fixer->beginChangeset();

				$fixed_str = preg_replace( $replace_regexes, $replacements, $content, 1 );

				$this->phpcsFile->fixer->replaceToken( $first_non_empty, $fixed_str );

				$i = ( $first_non_empty + 1 );
				while ( $i <= $param_info['end'] && isset( Tokens::$stringTokens[ $this->tokens[ $i ]['code'] ] ) ) {
					$this->phpcsFile->fixer->replaceToken( $i, '' );
					++$i;
				}

				$this->phpcsFile->fixer->endChangeset();
			}
		}
	}

	/**
	 * Check if a parameter which is supposed to hold translatable text actually has translatable text.
	 *
	 * @since 3.0.0 The logic in this method used to be contained in the, now removed, `check_text()` method.
	 *
	 * @param string      $matched_content The token content (function name) which was matched
	 *                                     in lowercase.
	 * @param string      $param_name      The name of the parameter being examined.
	 * @param array|false $param_info      Parameter info array for an individual parameter,
	 *                                     as received from the PassedParemeters class.
	 *
	 * @return bool Whether or not the text string has translatable content.
	 */
	private function check_string_has_translatable_content( $matched_content, $param_name, $param_info ) {
		// Strip placeholders and surrounding quotes.
		$content_without_quotes  = trim( TextStrings::stripQuotes( $param_info['clean'] ) );
		$non_placeholder_content = preg_replace( self::SPRINTF_PLACEHOLDER_REGEX, '', $content_without_quotes );

		if ( '' === $non_placeholder_content ) {
			$first_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, $param_info['start'], ( $param_info['end'] + 1 ), true );

			$this->phpcsFile->addError(
				'The $%s text string should have translatable content. Found: %s',
				$first_non_empty,
				'NoEmptyStrings',
				array( $param_name, $param_info['clean'] )
			);
			return false;
		}

		return true;
	}

	/**
	 * Ensure that a translatable text string is not wrapped in HTML code.
	 *
	 * If the text is wrapped in HTML, the HTML should be moved out of the translatable text string.
	 *
	 * @since 3.0.0 The logic in this method used to be contained in the, now removed, `check_text()` method.
	 *
	 * @param string      $matched_content The token content (function name) which was matched
	 *                                     in lowercase.
	 * @param string      $param_name      The name of the parameter being examined.
	 * @param array|false $param_info      Parameter info array for an individual parameter,
	 *                                     as received from the PassedParemeters class.
	 *
	 * @return void
	 */
	private function check_string_has_no_html_wrapper( $matched_content, $param_name, $param_info ) {
		// Strip surrounding quotes.
		$content_without_quotes = trim( TextStrings::stripQuotes( $param_info['clean'] ) );

		$reader = new XMLReader();
		$reader->XML( $content_without_quotes, 'UTF-8', \LIBXML_NOERROR | \LIBXML_ERR_NONE | \LIBXML_NOWARNING );

		// Is the first node an HTML element?
		if ( ! $reader->read() || XMLReader::ELEMENT !== $reader->nodeType ) {
			return;
		}

		// If the opening HTML element includes placeholders in its attributes, we don't warn.
		// E.g. '<option id="%1$s" value="%2$s">Translatable option name</option>'.
		$i = 0;
		while ( $attr = $reader->getAttributeNo( $i ) ) {
			if ( preg_match( self::SPRINTF_PLACEHOLDER_REGEX, $attr ) === 1 ) {
				return;
			}

			++$i;
		}

		// We don't flag strings wrapped in `<a href="...">...</a>`, as the link target might actually need localization.
		if ( 'a' === $reader->name && $reader->getAttribute( 'href' ) ) {
			return;
		}

		// Does the entire string only consist of this HTML node?
		if ( $reader->readOuterXml() === $content_without_quotes ) {
			$first_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, $param_info['start'], ( $param_info['end'] + 1 ), true );

			$this->phpcsFile->addWarning(
				'Translatable string should not be wrapped in HTML. Found: %s',
				$first_non_empty,
				'NoHtmlWrappedStrings',
				array( $param_info['clean'] )
			);
		}
	}

	/**
	 * Check for inconsistencies in the placeholders between single and plural form of the translatable text string.
	 *
	 * @since 3.0.0 - The parameter names and expected format for the $param_info_single
	 *                and the $param_info_plural parameters has changed.
	 *              - The method visibility has been changed from `protected` to `private`.
	 *
	 * @param int   $stackPtr          The position of the function call token in the stack.
	 * @param array $param_info_single Parameter info array for the `$single` parameter,
	 *                                 as received from the PassedParemeters class.
	 * @param array $param_info_plural Parameter info array for the `$plural` parameter,
	 *                                 as received from the PassedParemeters class.
	 *
	 * @return void
	 */
	private function compare_single_and_plural_arguments( $stackPtr, $param_info_single, $param_info_plural ) {
		$single_content = $param_info_single['clean'];
		$plural_content = $param_info_plural['clean'];

		preg_match_all( self::SPRINTF_PLACEHOLDER_REGEX, $single_content, $single_placeholders );
		$single_placeholders = $single_placeholders[0];

		preg_match_all( self::SPRINTF_PLACEHOLDER_REGEX, $plural_content, $plural_placeholders );
		$plural_placeholders = $plural_placeholders[0];

		// English conflates "singular" with "only one", described in the codex:
		// https://codex.wordpress.org/I18n_for_WordPress_Developers#Plurals .
		if ( \count( $single_placeholders ) < \count( $plural_placeholders ) ) {
			$error_string           = 'Missing singular placeholder, needed for some languages. See https://codex.wordpress.org/I18n_for_WordPress_Developers#Plurals';
			$first_non_empty_single = $this->phpcsFile->findNext( Tokens::$emptyTokens, $param_info_single['start'], ( $param_info_single['end'] + 1 ), true );

			$this->phpcsFile->addError( $error_string, $first_non_empty_single, 'MissingSingularPlaceholder' );
			return;
		}

		// Reordering is fine, but mismatched placeholders is probably wrong.
		sort( $single_placeholders, \SORT_NATURAL );
		sort( $plural_placeholders, \SORT_NATURAL );

		if ( $single_placeholders !== $plural_placeholders ) {
			$this->phpcsFile->addWarning( 'Mismatched placeholders is probably an error', $stackPtr, 'MismatchedPlaceholders' );
		}
	}

	/**
	 * Check for the presence of a translators comment if one of the text strings contains a placeholder.
	 *
	 * @since 3.0.0 - The parameter names and expected format for the $parameters parameter has changed.
	 *              - The method visibility has been changed from `protected` to `private`.
	 *
	 * @param int    $stackPtr        The position of the gettext call token in the stack.
	 * @param string $matched_content The token content (function name) which was matched
	 *                                in lowercase.
	 * @param array  $parameters      Array with information about the parameters.
	 *
	 * @return void
	 */
	private function check_for_translator_comment( $stackPtr, $matched_content, $parameters ) {
		$needs_translators_comment = false;

		foreach ( $parameters as $param_name => $param_info ) {
			if ( false === \in_array( $param_name, array( 'text', 'single', 'singular', 'plural' ), true ) ) {
				continue;
			}

			if ( false === $param_info || false === $param_info['is_string_literal'] ) {
				continue;
			}

			if ( preg_match( self::SPRINTF_PLACEHOLDER_REGEX, $param_info['clean'], $placeholders ) === 1 ) {
				$needs_translators_comment = true;
				break;
			}
		}

		if ( false === $needs_translators_comment ) {
			// No text string with placeholders found, no translation comment needed.
			return;
		}

		$previous_comment = $this->phpcsFile->findPrevious( Tokens::$commentTokens, ( $stackPtr - 1 ) );

		if ( false !== $previous_comment ) {
			/*
			 * Check that the comment is either on the line before the gettext call or
			 * if it's not, that there is only whitespace between.
			 */
			$correctly_placed = false;

			if ( ( $this->tokens[ $previous_comment ]['line'] + 1 ) === $this->tokens[ $stackPtr ]['line'] ) {
				$correctly_placed = true;
			} else {
				$next_non_whitespace = $this->phpcsFile->findNext( \T_WHITESPACE, ( $previous_comment + 1 ), $stackPtr, true );
				if ( false === $next_non_whitespace || $this->tokens[ $next_non_whitespace ]['line'] === $this->tokens[ $stackPtr ]['line'] ) {
					// No non-whitespace found or next non-whitespace is on same line as gettext call.
					$correctly_placed = true;
				}
				unset( $next_non_whitespace );
			}

			/*
			 * Check that the comment starts with 'translators:'.
			 */
			if ( true === $correctly_placed ) {
				if ( \T_COMMENT === $this->tokens[ $previous_comment ]['code'] ) {
					$comment_text = trim( $this->tokens[ $previous_comment ]['content'] );

					// If it's multi-line /* */ comment, collect all the parts.
					if ( '*/' === substr( $comment_text, -2 ) && '/*' !== substr( $comment_text, 0, 2 ) ) {
						for ( $i = ( $previous_comment - 1 ); 0 <= $i; $i-- ) {
							if ( \T_COMMENT !== $this->tokens[ $i ]['code'] ) {
								break;
							}

							$comment_text = trim( $this->tokens[ $i ]['content'] ) . $comment_text;
						}
					}

					if ( true === $this->is_translators_comment( $comment_text ) ) {
						// Comment is ok.
						return;
					}
				}

				if ( \T_DOC_COMMENT_CLOSE_TAG === $this->tokens[ $previous_comment ]['code'] ) {
					// If it's docblock comment (wrong style) make sure that it's a translators comment.
					if ( isset( $this->tokens[ $previous_comment ]['comment_opener'] ) ) {
						$db_start = $this->tokens[ $previous_comment ]['comment_opener'];
					} else {
						$db_start = $this->phpcsFile->findPrevious( \T_DOC_COMMENT_OPEN_TAG, ( $previous_comment - 1 ) );
					}

					$db_first_text = $this->phpcsFile->findNext( \T_DOC_COMMENT_STRING, ( $db_start + 1 ), $previous_comment );

					if ( true === $this->is_translators_comment( $this->tokens[ $db_first_text ]['content'] ) ) {
						$this->phpcsFile->addWarning(
							'A "translators:" comment must be a "/* */" style comment. Docblock comments will not be picked up by the tools to generate a ".pot" file.',
							$stackPtr,
							'TranslatorsCommentWrongStyle'
						);
						return;
					}
				}
			}
		}

		// Found placeholders but no translators comment.
		$this->phpcsFile->addWarning(
			'A function call to %s() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders.',
			$stackPtr,
			'MissingTranslatorsComment',
			array( $matched_content )
		);
	}

	/**
	 * Check if a (collated) comment string starts with 'translators:'.
	 *
	 * @since 0.11.0
	 *
	 * @param string $content Comment string content.
	 *
	 * @return bool
	 */
	private function is_translators_comment( $content ) {
		if ( preg_match( '`^(?:(?://|/\*{1,2}) )?translators:`i', $content, $matches ) === 1 ) {
			return true;
		}
		return false;
	}
}
