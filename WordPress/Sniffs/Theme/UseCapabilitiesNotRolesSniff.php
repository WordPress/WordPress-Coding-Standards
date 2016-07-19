<?php
/**
 * WordPress Coding Standard.
 * UseCapabilitiesNotRolesSniff
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * WordPress_Sniffs_Theme_UseCapabilitiesNotRolesSniff
 *
 * WordPress Theme Requirements Coding Standards
 * ERROR | Check that capabilities are used not roles.
 * Functions to check: current_user_can(),
 * current_user_can_for_blog(), user_can(), add_..._page().
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   khacoder
 */
class WordPress_Sniffs_Theme_UseCapabilitiesNotRolesSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_STRING,
			T_CONSTANT_ENCAPSED_STRING,
			T_VARIABLE,
			T_LNUMBER,
		);
	}//end register()

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

		// the number represents the position in the function call passed variables,
		// where the capability is to be listed.
		$checks = array(
			'add_dashboard_page' => 3,
			'add_posts_page' => 3,
			'add_media_page' => 3,
			'add_pages_page' => 3,
			'add_comments_page' => 3,
			'add_theme_page' => 3,
			'add_plugins_page' => 3,
			'add_users_page' => 3,
			'add_management_page' => 3,
			'add_options_page' => 3,
			'add_menu_page' => 3,
			'add_utility_page' => 3,
			'add_submenu_page' => 4,
			'current_user_can' => 1,
			'author_can' => 2,
			'current_user_can_for_blog' => 2,
		);

		$roles = array(
			'super_admin',
			'administrator',
			'editor',
			'author',
			'contributor',
			'subscriber',
		);

		$types = array( T_CONSTANT_ENCAPSED_STRING , T_VARIABLE , T_LNUMBER );

		foreach ( $checks as $key => $check ) {
			if ( strpos( $token['content'] , $key ) !== false ) {

				$nextStackPtr = $stackPtr;

				for ( $i = 1; $i <= $check; $i++ ) {
					$nextStackPtr = $phpcsFile->findNext( $types , $nextStackPtr + 1 );
				}

				if ( in_array( trim( $tokens[ $nextStackPtr ]['content'] , '\'\"' ) , $roles, true ) ) {
					$phpcsFile->addError( 'Please use Capabilities and not Roles in [' . $key . '] ref:https://codex.wordpress.org/Roles_and_Capabilities', $nextStackPtr, 'UseCapabilitiesNotRoles' );
				} elseif ( trim( $tokens[ $nextStackPtr ]['code'] , '\'\"' ) == T_VARIABLE ) {
					$phpcsFile->addWarning( 'The capability in [' . $key . '] is a variable. Please check to ensure it is a capability and not a role. ref:https://codex.wordpress.org/Roles_and_Capabilities', $nextStackPtr, 'UseCapabilitiesNotRoles' );
				}
			}
		}
	}//end process()
}
