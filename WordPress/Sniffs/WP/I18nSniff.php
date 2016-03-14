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

		if ( ! in_array( $token['content'], $this->i18n_functions ) ) {
			return;
		}

		$next_token = $phpcs_file->findNext( T_WHITESPACE, $stack_ptr + 1, null, true );
		if ( ! $next_token || T_OPEN_PARENTHESIS !== $tokens[ $next_token ]['code'] ) {
			 return;
		}

		// Look at arguments.
		for ( $i = $next_token + 1; $i < $tokens[ $next_token ]['parenthesis_closer'] - 1; $i += 1 ) {
			if ( T_CONSTANT_ENCAPSED_STRING === $tokens[ $i ]['code'] ) {
				continue;
			}
			if ( in_array( $tokens[ $i ]['code'], array( T_WHITESPACE, T_COMMA ), true ) ) {
				continue;
			}

			if ( T_DOUBLE_QUOTED_STRING === $tokens[ $i ]['code'] ) {
				$interpolated_variables = $this->get_interpolated_variables( $tokens[ $i ]['content'] );
				foreach ( $interpolated_variables as $interpolated_variable ) {
					$phpcs_file->addError( "Translatable strings cannot contain interpolated variables. Found $interpolated_variable.", $i, 'InterpolatedVariable' );
				}
				continue;
			}

			$phpcs_file->addError( sprintf( 'Translatable string expected, but found "%s"', $tokens[ $i ]['content'] ), $i );
			return;
		}
	}
}
