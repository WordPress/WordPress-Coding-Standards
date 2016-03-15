<?php
/**
 * WordPress_Sniffs_WP_I18nSniff
 *
 * Makes sure internationalization functions are used properly
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Shady Sharaf <shady@x-team.com>
 */
class WordPress_Sniffs_WP_I18nSniff extends WordPress_Sniff {

	public $i18n_functions = array(
		'translate',
		'translate_with_gettext_context',
		'__',
		'esc_attr__',
		'esc_html__',
		'_e',
		'esc_attr_e',
		'esc_html_e',
		'_x',
		'_ex',
		'esc_attr_x',
		'esc_html_x',
		'_n',
		'_nx',
		'_n_noop',
		'_nx_noop',
	);

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
	 *                                        in the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcs_file, $stack_ptr ) {
		$tokens = $phpcs_file->getTokens();
		$token  = $tokens[ $stack_ptr ];

		if ( '_' === $token['content'] ) {
			$phpcs_file->addError( 'Found single-underscore "_()" function when double-underscore expected.', $stack_ptr, 'SingleUnderscoreGetTextFunction' );
		}

		if ( ! in_array( $token['content'], $this->i18n_functions, true ) ) {
			return;
		}

		if ( in_array( $token['content'], array( 'translate', 'translate_with_gettext_context' ), true ) ) {
			$phpcs_file->addWarning( 'Use of the "%s()" function is reserved for low-level API usage.', $stack_ptr, 'LowLevelTranslationFunction', array( $token['content'] ) );
		}

		$translation_function = $token['content'];

		$func_open_paren_token = $phpcs_file->findNext( T_WHITESPACE, $stack_ptr + 1, null, true );
		if ( ! $func_open_paren_token || T_OPEN_PARENTHESIS !== $tokens[ $func_open_paren_token ]['code'] ) {
			 return;
		}

		$arguments_tokens = array();
		$argument_tokens = array();

		// Look at arguments.
		for ( $i = $func_open_paren_token + 1; $i < $tokens[ $func_open_paren_token ]['parenthesis_closer'] - 1; $i += 1 ) {
			$this_token = $tokens[ $i ];
			$this_token['token_index'] = $i;
			if ( in_array( $this_token['code'], array( T_WHITESPACE, T_COMMENT ), true ) ) {
				continue;
			}
			if ( T_COMMA === $this_token['code'] ) {
				$arguments_tokens[] = $argument_tokens;
				$argument_tokens = array();
				continue;
			}
			$argument_tokens[] = $this_token;

			// Include everything up to and including the parenthesis_closer if this token has one.
			if ( ! empty( $this_token['parenthesis_closer'] ) ) {
				for ( $j = $i + 1; $j <= $this_token['parenthesis_closer']; $j += 1 ) {
					$tokens[ $j ]['token_index'] = $j;
					$argument_tokens[] = $tokens[ $j ];
				}
				$i = $this_token['parenthesis_closer'];
			}
		}
		if ( ! empty( $argument_tokens ) ) {
			$arguments_tokens[] = $argument_tokens;
		}
		unset( $argument_tokens );

		$argument_assertions = array();
		if ( in_array( $translation_function, array( '__', 'esc_attr__', 'esc_html__', '_e', 'esc_attr_e', 'esc_html_e', 'translate' ) ) ) {
			$argument_assertions[] = array( '$text', 'check_literal_string_text_tokens' );
			$argument_assertions[] = array( '$domain', 'check_string_domain_tokens' );
		} else if ( in_array( $translation_function, array( '_x', '_ex', 'esc_attr_x', 'esc_html_x', 'translate_with_gettext_context' ) ) ) {
			$argument_assertions[] = array( '$text', 'check_literal_string_text_tokens' );
			$argument_assertions[] = array( '$context', 'check_literal_string_context_tokens' );
			$argument_assertions[] = array( '$domain', 'check_string_domain_tokens' );
		} else if ( in_array( $translation_function, array( '_n', '_n_noop' ) ) ) {
			$argument_assertions[] = array( '$single', 'check_literal_string_text_tokens' );
			$argument_assertions[] = array( '$plural', 'check_literal_string_text_tokens' );
			$argument_assertions[] = array( '$number', 'check_number_tokens' );
			$argument_assertions[] = array( '$domain', 'check_string_domain_tokens' );
		} else if ( in_array( $translation_function, array( '_nx', '_nx_noop' ) ) ) {
			$argument_assertions[] = array( '$single', 'check_literal_string_text_tokens' );
			$argument_assertions[] = array( '$plural', 'check_literal_string_text_tokens' );
			$argument_assertions[] = array( '$number', 'check_number_tokens' );
			$argument_assertions[] = array( '$context', 'check_literal_string_context_tokens' );
			$argument_assertions[] = array( '$domain', 'check_string_domain_tokens' );
		}

		$argument_stack_ptr = $func_open_paren_token;
		foreach ( $argument_assertions as $argument_assertion ) {
			$argument_tokens = array_shift( $arguments_tokens );
			if ( ! $argument_tokens ) {
				$argument_tokens = array();
			} else {
				$argument_stack_ptr = $argument_tokens[0]['token_index'];
			}
			$method_name = $argument_assertion[1];
			$arg_name = $argument_assertion[0];
			call_user_func( array( $this, $method_name ), $phpcs_file, $arg_name, $argument_stack_ptr, $argument_tokens );
		}
	}

	/**
	 * Check if supplied tokens represent a translation text string literal.
	 *
	 * @param PHP_CodeSniffer_File $phpcs_file The file being scanned.
	 * @param string $arg_name
	 * @param int $stack_ptr
	 * @param array $tokens
	 * @return bool
	 */
	protected function check_literal_string_text_tokens( $phpcs_file, $arg_name = '$text', $stack_ptr, $tokens = array() ) {
		if ( 0 === count( $tokens ) ) {
			$phpcs_file->addError( 'Missing translatable text (%s arg).', $stack_ptr, 'MissingText', array( $arg_name ) );
			return false;
		}
		if ( count( $tokens ) > 1 ) {
			$contents = '';
			foreach ( $tokens as $token ) {
				$contents .= $token['content'];
			}
			$phpcs_file->addError( 'Translatable text (%s arg) must be a single string literal, not "%s".', $stack_ptr, 'NonSingularStringLiteralText', array( $arg_name, $contents ) );
			return false;
		}
		if ( T_CONSTANT_ENCAPSED_STRING === $tokens[0]['code'] ) {
			return true;
		}
		if ( T_DOUBLE_QUOTED_STRING === $tokens[0]['code'] ) {
			$interpolated_variables = $this->get_interpolated_variables( $tokens[0]['content'] );
			foreach ( $interpolated_variables as $interpolated_variable ) {
				$phpcs_file->addError( 'Translatable text (%s arg) must not contain interpolated variables. Found "$%s".', $stack_ptr, 'InterpolatedVariableText', array( $arg_name, $interpolated_variable ) );
			}
			return false;
		}
		$phpcs_file->addError( 'Translatable text (%s arg) should be single a string literal, not "%s".', $stack_ptr, 'NonSingularStringLiteralText', array( $arg_name, $tokens[0]['content'] ) );
		return false;
	}

	/**
	 * The $number argument can be anything, so this is a no-op.
	 *
	 * @return bool
	 */
	protected function check_number_tokens() {
		return true;
	}

	/**
	 * Check if supplied tokens are a literal string context.
	 *
	 * @param PHP_CodeSniffer_File $phpcs_file The file being scanned.
	 * @param int $stack_ptr
	 * @param array $tokens
	 * @return bool
	 */
	protected function check_literal_string_context_tokens( $phpcs_file, $arg_name = '$context', $stack_ptr, $tokens = array() ) {
		if ( 0 === count( $tokens ) ) {
			$phpcs_file->addError( 'Missing context (%s arg).', $stack_ptr, 'MissingContext', array( $arg_name ) );
			return false;
		}
		if ( count( $tokens ) > 1 ) {
			$contents = '';
			foreach ( $tokens as $token ) {
				$contents .= $token['content'];
			}
			$phpcs_file->addError( 'Context (%s arg) should be a single string literal, not "%s".', $stack_ptr, 'NonSingularStringLiteralContext', array( $arg_name, $contents ) );
			return false;
		}
		if ( T_CONSTANT_ENCAPSED_STRING === $tokens[0]['code'] ) {
			return true;
		}
		if ( T_DOUBLE_QUOTED_STRING === $tokens[0]['code'] ) {
			$interpolated_variables = $this->get_interpolated_variables( $tokens[0]['content'] );
			foreach ( $interpolated_variables as $interpolated_variable ) {
				$phpcs_file->addError( 'Context strings (%s arg) should not contain interpolated variables. Found "$%s".', $stack_ptr, 'InterpolatedVariableContext', array( $arg_name, $interpolated_variable ) );
			}
			return false;
		}
		$phpcs_file->addError( 'Context (%s arg) should be single a string literal, not "%s".', $stack_ptr, 'NonSingularStringLiteralContext', array( $arg_name, $tokens[0]['content'] ) );
		return false;
	}

	/**
	 * Check if supplied tokens are a valid text domain.
	 *
	 * @param PHP_CodeSniffer_File $phpcs_file The file being scanned.
	 * @param string $arg_name
	 * @param int $stack_ptr
	 * @param array $tokens
	 * @return bool
	 */
	protected function check_string_domain_tokens( $phpcs_file, $arg_name = '$domain', $stack_ptr, $tokens = array() ) {
		if ( 0 === count( $tokens ) ) {
			$phpcs_file->addWarning( 'Missing text domain (%s arg).', $stack_ptr, 'MissingTextDomain', array( $arg_name ) );
			return false;
		}
		if ( count( $tokens ) > 1 ) {
			$contents = '';
			foreach ( $tokens as $token ) {
				$contents .= $token['content'];
			}
			$phpcs_file->addWarning( 'Text domain (%s arg) should be a single string literal, not "%s".', $stack_ptr, 'NonSingularStringLiteralTextDomain', array( $arg_name, $contents ) );
			return false;
		}
		if ( T_CONSTANT_ENCAPSED_STRING === $tokens[0]['code'] ) {
			return true;
		}
		if ( T_DOUBLE_QUOTED_STRING === $tokens[0]['code'] ) {
			$interpolated_variables = $this->get_interpolated_variables( $tokens[0]['content'] );
			foreach ( $interpolated_variables as $interpolated_variable ) {
				$phpcs_file->addWarning( 'Text domain strings (%s arg) should not contain interpolated variables. Found "$%s".', $stack_ptr, 'InterpolatedVariableTextDomain', array( $arg_name, $interpolated_variable ) );
			}
			return false;
		}
		$phpcs_file->addWarning( 'Text domain (%s arg) should be single a string literal, not "%s".', $stack_ptr, 'NonSingularStringLiteralTextDomain', array( $arg_name, $tokens[0]['content'] ) );
		return false;
	}
}
