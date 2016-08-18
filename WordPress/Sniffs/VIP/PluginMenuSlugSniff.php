<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Warn about __FILE__ for page registration.
 *
 * @link    https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#using-__file__-for-page-registration
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 */
class WordPress_Sniffs_VIP_PluginMenuSlugSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Functions which can be used to add pages to the WP Admin menu.
	 *
	 * @var array
	 */
	public $add_menu_functions = array(
		'add_menu_page',
		'add_object_page',
		'add_utility_page',
		'add_submenu_page',
		'add_dashboard_page',
		'add_posts_page',
		'add_media_page',
		'add_links_page',
		'add_pages_page',
		'add_comments_page',
		'add_theme_page',
		'add_plugins_page',
		'add_users_page',
		'add_management_page',
		'add_options_page',
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
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$token  = $tokens[ $stackPtr ];

		if ( ! in_array( $token['content'], $this->add_menu_functions, true ) ) {
			return;
		}

		$opening = $phpcsFile->findNext( T_OPEN_PARENTHESIS, $stackPtr );
		$closing = $tokens[ $opening ]['parenthesis_closer'];

		$string = $phpcsFile->findNext( T_FILE, $opening, $closing, null, '__FILE__', true );

		if ( $string ) {
			$phpcsFile->addError( 'Using __FILE__ for menu slugs risks exposing filesystem structure.', $stackPtr, 'Using__FILE__' );
		}

	}

} // End class.
