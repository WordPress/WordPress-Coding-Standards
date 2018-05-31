<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\WP;

use WordPress\AbstractFunctionParameterSniff;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Check that capabilities are used correctly.
 *
 * User capabilities should be used not roles. Deprecated capabilities.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   1.0.0
 */
class CapabilitiesSniff extends AbstractFunctionParameterSniff {

	/**
	 * Only check for known capabilites.
	 *
	 * @since 1.0.0
	 *
	 * @var boolean
	 */
	public $check_only_known_caps = true;

	/**
	 * List of custom capabilites.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $custom_capabilities = array();

	/**
	 * The group name for this group of functions.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $group_name = 'caps_not_roles';

	/**
	 * List of functions that accept roles and capabilities as an argument.
	 *
	 * The number represents the parameter position in the function call,
	 * where the capability is to be listed.
	 * The functions are defined in `wp-admin/includes/plugin.php` and
	 * `/wp-includes/capabilities.php`.
	 * The list is sorted alphabetically.
	 *
	 * @since 1.0.0
	 *
	 * @var array Function name with parameter position.
	 */
	protected $target_functions = array(
		'add_comments_page'         => 3,
		'add_dashboard_page'        => 3,
		'add_links_page'            => 3,
		'add_management_page'       => 3,
		'add_media_page'            => 3,
		'add_menu_page'             => 3,
		'add_object_page'           => 3,
		'add_options_page'          => 3,
		'add_pages_page'            => 3,
		'add_plugins_page'          => 3,
		'add_posts_page'            => 3,
		'add_submenu_page'          => 4,
		'add_theme_page'            => 3,
		'add_users_page'            => 3,
		'add_utility_page'          => 3,
		'author_can'                => 2,
		'current_user_can'          => 1,
		'current_user_can_for_blog' => 2,
		'user_can'                  => 2,
	);

	/**
	 * Blacklist of core roles which should not to be used directly.
	 *
	 * @since 1.0.0
	 *
	 * @var array Role available in core.
	 */
	protected $core_roles = array(
		'super_admin'   => true,
		'administrator' => true,
		'editor'        => true,
		'author'        => true,
		'contributor'   => true,
		'subscriber'    => true,
	);

	/**
	 * Whitelist of primitive and meta core capabilities.
	 *
	 * To be updated after every major release. Sorted as in capabilities tests.
	 * Last updated for WordPress 4.9.6.
	 *
	 * @link https://github.com/WordPress/wordpress-develop/blob/master/tests/phpunit/tests/user/capabilities.php
	 *
	 * @since 1.0.0
	 *
	 * @var array All capabilities available in core.
	 */
	protected $core_capabilities = array(
		'unfiltered_upload'           => true,
		'unfiltered_html'             => true,
		'activate_plugins'            => true,
		'create_users'                => true,
		'delete_plugins'              => true,
		'delete_themes'               => true,
		'delete_users'                => true,
		'edit_files'                  => true,
		'edit_plugins'                => true,
		'edit_themes'                 => true,
		'edit_users'                  => true,
		'install_plugins'             => true,
		'install_themes'              => true,
		'update_core'                 => true,
		'update_plugins'              => true,
		'update_themes'               => true,
		'edit_theme_options'          => true,
		'export'                      => true,
		'import'                      => true,
		'list_users'                  => true,
		'manage_options'              => true,
		'promote_users'               => true,
		'remove_users'                => true,
		'switch_themes'               => true,
		'edit_dashboard'              => true,
		'moderate_comments'           => true,
		'manage_categories'           => true,
		'edit_others_posts'           => true,
		'edit_pages'                  => true,
		'edit_others_pages'           => true,
		'edit_published_pages'        => true,
		'publish_pages'               => true,
		'delete_pages'                => true,
		'delete_others_pages'         => true,
		'delete_published_pages'      => true,
		'delete_others_posts'         => true,
		'delete_private_posts'        => true,
		'edit_private_posts'          => true,
		'read_private_posts'          => true,
		'delete_private_pages'        => true,
		'edit_private_pages'          => true,
		'read_private_pages'          => true,
		'edit_published_posts'        => true,
		'upload_files'                => true,
		'publish_posts'               => true,
		'delete_published_posts'      => true,
		'edit_posts'                  => true,
		'delete_posts'                => true,
		'read'                        => true,
		'create_sites'                => true,
		'delete_sites'                => true,
		'manage_network'              => true,
		'manage_sites'                => true,
		'manage_network_users'        => true,
		'manage_network_plugins'      => true,
		'manage_network_themes'       => true,
		'manage_network_options'      => true,
		'delete_site'                 => true,
		'upgrade_network'             => true,
		'setup_network'               => true,
		'upload_plugins'              => true,
		'upload_themes'               => true,
		'customize'                   => true,
		'add_users'                   => true,
		'install_languages'           => true,
		'update_languages'            => true,
		'deactivate_plugins'          => true,
		'upgrade_php'                 => true,
		'export_others_personal_data' => true,
		'erase_others_personal_data'  => true,
		'manage_privacy_options'      => true,
		'edit_categories'             => true,
		'delete_categories'           => true,
		'manage_post_tags'            => true,
		'edit_post_tags'              => true,
		'delete_post_tags'            => true,
		'edit_css'                    => true,
		'assign_categories'           => true,
		'assign_post_tags'            => true,
	);

	/**
	 * List of deprecated core capabilities.
	 *
	 * User Levels were  deprecated in version 3.0.
	 * To be updated after every major release.
	 * Last updated for WordPress 4.9.6.
	 *
	 * @link https://github.com/WordPress/wordpress-develop/blob/master/tests/phpunit/tests/user/capabilities.php
	 *
	 * @since 1.0.0
	 *
	 * @var array All deprecated capabilities in core.
	 */
	protected $deprecated_capabilities = array(
		'level_10' => true,
		'level_9'  => true,
		'level_8'  => true,
		'level_7'  => true,
		'level_6'  => true,
		'level_5'  => true,
		'level_4'  => true,
		'level_3'  => true,
		'level_2'  => true,
		'level_1'  => true,
		'level_0'  => true,
	);

	/**
	 * Process the parameters of a matched function.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param array  $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched.
	 * @param array  $parameters      Array with information about the parameters.
	 *
	 * @return void
	 */
	public function process_parameters( $stackPtr, $group_name, $matched_content, $parameters ) {

		$position = $this->target_functions[ $matched_content ];
		if ( ! isset( $parameters[ $position ] ) ) {
			return;
		}

		$next_not_empty = $this->phpcsFile->findNext(
			Tokens::$emptyTokens,
			$parameters[ $position ]['start'],
			$parameters[ $position ]['end'] + 1,
			true
		);

		$matched_parameter = $this->strip_quotes( $this->tokens[ $next_not_empty ]['content'] );
		if ( isset( $this->core_capabilities[ $matched_parameter ] ) ) {
			return;
		}

		$custom_capabilities = $this->merge_custom_array( $this->custom_capabilities, array(), true );
		if ( isset( $custom_capabilities[ $matched_parameter ] ) ) {
			return;
		}

		if ( isset( $this->deprecated_capabilities[ $matched_parameter ] ) ) {
			$this->phpcsFile->addError(
				'The capatibility "%s" found in function call "%s()" has been deprecated since WordPress version 3.0.',
				$next_not_empty,
				'DeprecatedCapability',
				array(
					$matched_parameter,
					$matched_content,
				)
			);
			return;
		}

		if ( isset( $this->core_roles[ $matched_parameter ] ) ) {
			$this->phpcsFile->addError(
				'Capabilities should be used instead of roles. Found "%s" in function call "%s()"',
				$next_not_empty,
				'RoleFound',
				array(
					$matched_parameter,
					$matched_content,
				)
			);
			return;
		}

		if ( false === $this->check_only_known_caps ) {
			$this->phpcsFile->addWarning(
				'"%s" is an unknown role or capability. Check the "%s()" function call to ensure it is a capability and not a role.',
				$next_not_empty,
				'UnknownCapabilityFound',
				array(
					$matched_parameter,
					$matched_content,
				)
			);
			return;
		}
	}

}
