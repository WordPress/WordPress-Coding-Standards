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
class WordPress_Sniffs_WP_I18nSniff implements PHP_CodeSniffer_Sniff {

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

		if ( $next_token = $phpcs_file->findNext( T_WHITESPACE, $stack_ptr + 1, null, true ) ) {
			if ( T_OPEN_PARENTHESIS !== $tokens[ $next_token ]['code'] ) {
				return;
			}
		}

		// Get arguments
		for ( $i = $next_token + 1; $i < $tokens[ $next_token ]['parenthesis_closer'] - 1; $i += 1 ) {
			if ( in_array( $tokens[ $i ]['code'], array( T_WHITESPACE, T_COMMA, T_CONSTANT_ENCAPSED_STRING ) ) ) {
				continue;
			}

			if ( T_DOUBLE_QUOTED_STRING === $tokens[ $i ]['code'] ) {
				$string = $tokens[ $i ]['content'];
				if ( preg_match( '#\$#', $string ) > 0 ) {
					$phpcs_file->addError( 'Translatable strings should not contain variables, found ' . $tokens[ $i ]['content'], $i );
					return;
				}
				continue;
			}

			$phpcs_file->addError( sprintf( 'Translatable string expected, but found "%s"', $tokens[ $i ]['content'] ), $i );
			return;
		}
	}
}
