<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

if ( ! class_exists( 'Generic_Sniffs_PHP_ForbiddenFunctionsSniff', true ) ) {
	throw new PHP_CodeSniffer_Exception( 'Class Generic_Sniffs_PHP_ForbiddenFunctionsSniff not found' );
}

/**
 * Discourages the use of various functions and suggests (WordPress) alternatives.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.1.0
 * @since   0.10.0 The checks for the POSIX functions have been replaced by the stand-alone
 *                 sniff WordPress_Sniffs_PHP_POSIXFunctionsSniff.
 */
class WordPress_Sniffs_PHP_DiscouragedFunctionsSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff {

	/**
	 * A list of forbidden functions with their alternatives.
	 *
	 * The value is NULL if no alternative exists. I.e. the
	 * function should just not be used.
	 *
	 * @var array(string => string|null)
	 */
	public $forbiddenFunctions = array(
		// Development.
		'print_r'                  => null,
		'debug_print_backtrace'    => null,
		'var_dump'                 => null,
		'var_export'               => null,

		// Discouraged.
		'json_encode'              => 'wp_json_encode',

		// WordPress deprecated.
		'find_base_dir'            => 'WP_Filesystem::abspath',
		'get_base_dir'             => 'WP_Filesystem::abspath',
		'dropdown_categories'      => 'wp_link_category_checklist',
		'dropdown_link_categories' => 'wp_link_category_checklist',
		'get_link'                 => 'get_bookmark',
		'get_catname'              => 'get_cat_name',
		'register_globals'         => null,
		'wp_setcookie'             => 'wp_set_auth_cookie',
		'wp_get_cookie_login'      => null,
		'wp_login'                 => 'wp_signon',
		'get_the_attachment_link'  => 'wp_get_attachment_link',
		'get_attachment_icon_src'  => 'wp_get_attachment_image_src',
		'get_attachment_icon'      => 'wp_get_attachment_image',
		'get_attachment_innerHTML' => 'wp_get_attachment_image',

		// WordPress discouraged.
		'query_posts'              => 'WP_Query',
		'wp_reset_query'           => 'wp_reset_postdata',
	);

	/**
	 * If true, an error will be thrown; otherwise a warning.
	 *
	 * @var bool
	 */
	public $error = false;

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * {@internal Temporarily overrule the parent register() method until bugfix has
	 * been merged into PHPCS upstream and WPCS minimum PHPCS version has caught up.
	 * {@link https://github.com/squizlabs/PHP_CodeSniffer/pull/1076} }}
	 *
	 * @return array
	 */
	public function register() {
		$register = parent::register();

		if ( true !== $this->patternMatch ) {
			$this->forbiddenFunctionNames = array_map( 'strtolower', $this->forbiddenFunctionNames );
			$this->forbiddenFunctions     = array_combine( $this->forbiddenFunctionNames, $this->forbiddenFunctions );
		}

		return $register;
	}

} // End class.
