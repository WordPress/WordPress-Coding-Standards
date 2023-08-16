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
use PHPCSUtils\Utils\MessageHelper;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\AbstractFunctionParameterSniff;
use WordPressCS\WordPress\Helpers\MinimumWPVersionTrait;
use WordPressCS\WordPress\Helpers\RulesetPropertyHelper;

/**
 * Check that capabilities are used correctly.
 *
 * User capabilities should be used, not roles or deprecated capabilities.
 *
 * @since 3.0.0
 *
 * @uses \WordPressCS\WordPress\Helpers\MinimumWPVersionTrait::$minimum_wp_version
 */
final class CapabilitiesSniff extends AbstractFunctionParameterSniff {

	use MinimumWPVersionTrait;

	/**
	 * List of custom capabilities.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	public $custom_capabilities = array();

	/**
	 * The group name for this group of functions.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	protected $group_name = 'caps_not_roles';

	/**
	 * List of functions that accept roles and capabilities as an argument.
	 *
	 * The functions are defined in `wp-admin/includes/plugin.php` and
	 * `/wp-includes/capabilities.php`.
	 * The list is sorted alphabetically.
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, array> The key is the name of a function we're targetting,
	 *                           the value is an array containing the 1-based parameter position
	 *                           of the "capability" parameter within the function, as well as
	 *                           the name of the parameter as declared in the function.
	 *                           If the parameter name has been renamed since the release of PHP 8.0,
	 *                           the parameter can be set as an array.
	 */
	protected $target_functions = array(
		'add_comments_page'         => array(
			'position' => 3,
			'name'     => 'capability',
		),
		'add_dashboard_page'        => array(
			'position' => 3,
			'name'     => 'capability',
		),
		'add_links_page'            => array(
			'position' => 3,
			'name'     => 'capability',
		),
		'add_management_page'       => array(
			'position' => 3,
			'name'     => 'capability',
		),
		'add_media_page'            => array(
			'position' => 3,
			'name'     => 'capability',
		),
		'add_menu_page'             => array(
			'position' => 3,
			'name'     => 'capability',
		),
		'add_object_page'           => array( // Deprecated since WP 4.5.0.
			'position' => 3,
			'name'     => 'capability',
		),
		'add_options_page'          => array(
			'position' => 3,
			'name'     => 'capability',
		),
		'add_pages_page'            => array(
			'position' => 3,
			'name'     => 'capability',
		),
		'add_plugins_page'          => array(
			'position' => 3,
			'name'     => 'capability',
		),
		'add_posts_page'            => array(
			'position' => 3,
			'name'     => 'capability',
		),
		'add_submenu_page'          => array(
			'position' => 4,
			'name'     => 'capability',
		),
		'add_theme_page'            => array(
			'position' => 3,
			'name'     => 'capability',
		),
		'add_users_page'            => array(
			'position' => 3,
			'name'     => 'capability',
		),
		'add_utility_page'          => array( // Deprecated since WP 4.5.0.
			'position' => 3,
			'name'     => 'capability',
		),
		'author_can'                => array(
			'position' => 2,
			'name'     => 'capability',
		),
		'current_user_can'          => array(
			'position' => 1,
			'name'     => 'capability',
		),
		'current_user_can_for_blog' => array(
			'position' => 2,
			'name'     => 'capability',
		),
		'map_meta_cap'              => array(
			'position' => 1,
			'name'     => 'cap',
		),
		'user_can'                  => array(
			'position' => 2,
			'name'     => 'capability',
		),
	);

	/**
	 * List of core roles which should not to be used directly.
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, true> Key is role available in WP Core, value irrelevant.
	 */
	private $core_roles = array(
		'super_admin'   => true,
		'administrator' => true,
		'editor'        => true,
		'author'        => true,
		'contributor'   => true,
		'subscriber'    => true,
	);

	/**
	 * List of known primitive and meta core capabilities.
	 *
	 * Sources:
	 * - {@link https://wordpress.org/support/article/roles-and-capabilities/ Roles and Capabilities handbook page}
	 * - The `map_meta_cap()` function in the `src/wp-includes/capabilities.php` file.
	 * - The tests in the `tests/phpunit/tests/user/capabilities.php` file.
	 *
	 * List is sorted alphabetically.
	 *
	 * {@internal To be updated after every major release. Last updated for WordPress 6.1.0.}
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, true> All capabilities available in core.
	 */
	private $core_capabilities = array(
		'activate_plugin'             => true,
		'activate_plugins'            => true,
		'add_comment_meta'            => true,
		'add_post_meta'               => true,
		'add_term_meta'               => true,
		'add_user_meta'               => true,
		'add_users'                   => true,
		'assign_categories'           => true,
		'assign_post_tags'            => true,
		'assign_term'                 => true,
		'create_app_password'         => true,
		'create_sites'                => true,
		'create_users'                => true,
		'customize'                   => true,
		'deactivate_plugin'           => true,
		'deactivate_plugins'          => true,
		'delete_app_password'         => true,
		'delete_app_passwords'        => true,
		'delete_block'                => true, // Only seen in tests.
		'delete_blocks'               => true, // Alias for 'delete_posts', but supported.
		'delete_categories'           => true,
		'delete_comment_meta'         => true,
		'delete_others_blocks'        => true, // Alias for 'delete_others_posts', but supported.
		'delete_others_pages'         => true,
		'delete_others_posts'         => true,
		'delete_page'                 => true, // Alias, but supported.
		'delete_pages'                => true,
		'delete_plugins'              => true,
		'delete_post_tags'            => true,
		'delete_post'                 => true, // Alias, but supported.
		'delete_post_meta'            => true,
		'delete_posts'                => true,
		'delete_private_blocks'       => true, // Alias for 'delete_private_posts', but supported.
		'delete_private_pages'        => true,
		'delete_private_posts'        => true,
		'delete_published_blocks'     => true, // Alias for 'delete_published_posts', but supported.
		'delete_published_pages'      => true,
		'delete_published_posts'      => true,
		'delete_site'                 => true,
		'delete_sites'                => true,
		'delete_term'                 => true,
		'delete_term_meta'            => true,
		'delete_themes'               => true,
		'delete_user'                 => true, // Alias for 'delete_users', but supported.
		'delete_user_meta'            => true,
		'delete_users'                => true,
		'edit_app_password'           => true,
		'edit_categories'             => true,
		'edit_block'                  => true, // Only seen in tests.
		'edit_blocks'                 => true, // Alias for 'edit_posts', but supported.
		'edit_comment'                => true, // Alias, but supported.
		'edit_comment_meta'           => true,
		'edit_css'                    => true,
		'edit_dashboard'              => true,
		'edit_files'                  => true,
		'edit_others_blocks'          => true, // Alias for 'edit_others_posts', but supported.
		'edit_others_pages'           => true,
		'edit_others_posts'           => true,
		'edit_page'                   => true, // Alias, but supported.
		'edit_pages'                  => true,
		'edit_plugins'                => true,
		'edit_post_tags'              => true,
		'edit_post'                   => true, // Alias, but supported.
		'edit_post_meta'              => true,
		'edit_posts'                  => true,
		'edit_private_blocks'         => true, // Alias for 'edit_private_posts', but supported.
		'edit_private_pages'          => true,
		'edit_private_posts'          => true,
		'edit_published_blocks'       => true, // Alias for 'edit_published_posts', but supported.
		'edit_published_pages'        => true,
		'edit_published_posts'        => true,
		'edit_term'                   => true,
		'edit_term_meta'              => true,
		'edit_theme_options'          => true,
		'edit_themes'                 => true,
		'edit_user'                   => true, // Alias for 'edit_users', but supported.
		'edit_user_meta'              => true,
		'edit_users'                  => true,
		'erase_others_personal_data'  => true,
		'export'                      => true,
		'export_others_personal_data' => true,
		'import'                      => true,
		'install_languages'           => true,
		'install_plugins'             => true,
		'install_themes'              => true,
		'list_app_passwords'          => true,
		'list_users'                  => true,
		'manage_categories'           => true,
		'manage_links'                => true,
		'manage_network'              => true,
		'manage_network_options'      => true,
		'manage_network_plugins'      => true,
		'manage_network_themes'       => true,
		'manage_network_users'        => true,
		'manage_options'              => true,
		'manage_post_tags'            => true,
		'manage_privacy_options'      => true,
		'manage_sites'                => true,
		'moderate_comments'           => true,
		'publish_blocks'              => true, // Alias for 'publish_posts', but supported.
		'publish_pages'               => true,
		'publish_post'                => true, // Alias, but supported.
		'publish_posts'               => true,
		'promote_user'                => true,
		'promote_users'               => true,
		'read'                        => true,
		'read_block'                  => true, // Only seen in tests.
		'read_post'                   => true, // Alias, but supported.
		'read_page'                   => true, // Alias, but supported.
		'read_app_password'           => true,
		'read_private_blocks'         => true, // Alias for 'read_private_posts', but supported.
		'read_private_pages'          => true,
		'read_private_posts'          => true,
		'remove_user'                 => true, // Alias for 'remove_users', but supported.
		'remove_users'                => true,
		'resume_plugin'               => true, // Alias for 'resume_plugins', but supported.
		'resume_plugins'              => true,
		'resume_theme'                => true, // Alias for 'resume_themes', but supported.
		'resume_themes'               => true,
		'setup_network'               => true,
		'switch_themes'               => true,
		'unfiltered_html'             => true,
		'unfiltered_upload'           => true,
		'update_core'                 => true,
		'update_https'                => true,
		'update_languages'            => true,
		'update_plugins'              => true,
		'update_php'                  => true,
		'update_themes'               => true,
		'upgrade_network'             => true,
		'upload_files'                => true,
		'upload_plugins'              => true,
		'upload_themes'               => true,
		'view_site_health_checks'     => true,
	);

	/**
	 * List of deprecated core capabilities.
	 *
	 * User Levels were deprecated in version 3.0.
	 *
	 * {@internal To be updated after every major release. Last updated for WordPress 6.1.0.}
	 *
	 * @link https://github.com/WordPress/wordpress-develop/blob/master/tests/phpunit/tests/user/capabilities.php
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, string> All deprecated capabilities in core.
	 */
	private $deprecated_capabilities = array(
		'level_10' => '3.0.0',
		'level_9'  => '3.0.0',
		'level_8'  => '3.0.0',
		'level_7'  => '3.0.0',
		'level_6'  => '3.0.0',
		'level_5'  => '3.0.0',
		'level_4'  => '3.0.0',
		'level_3'  => '3.0.0',
		'level_2'  => '3.0.0',
		'level_1'  => '3.0.0',
		'level_0'  => '3.0.0',
	);

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
		$function_details = $this->target_functions[ $matched_content ];

		$parameter = PassedParameters::getParameterFromStack(
			$parameters,
			$function_details['position'],
			$function_details['name']
		);

		if ( false === $parameter ) {
			return;
		}

		// If the parameter is anything other than T_CONSTANT_ENCAPSED_STRING throw a warning and bow out.
		$first_non_empty = null;
		for ( $i = $parameter['start']; $i <= $parameter['end']; $i++ ) {
			if ( isset( Tokens::$emptyTokens[ $this->tokens[ $i ]['code'] ] ) ) {
				continue;
			}

			if ( \T_CONSTANT_ENCAPSED_STRING !== $this->tokens[ $i ]['code']
				|| null !== $first_non_empty
			) {
				// Throw warning at low severity.
				$this->phpcsFile->addWarning(
					'Couldn\'t determine the value passed to the $%s parameter in function call to %s(). Please check if it matches a valid capability. Found: %s',
					$i,
					'Undetermined',
					array(
						$function_details['name'],
						$matched_content,
						$parameter['clean'],
					),
					3 // Message severity set to below default.
				);
				return;
			}

			$first_non_empty = $i;
		}

		if ( null === $first_non_empty ) {
			// Parse error. Bow out.
			return;
		}

		/*
		 * As of this point we know that the `$capabilities` parameter only contains the one token
		 * and that that token is a `T_CONSTANT_ENCAPSED_STRING`.
		 */
		$matched_parameter = TextStrings::stripQuotes( $this->tokens[ $first_non_empty ]['content'] );

		if ( isset( $this->core_capabilities[ $matched_parameter ] ) ) {
			return;
		}

		if ( empty( $matched_parameter ) ) {
			$this->phpcsFile->addError(
				'An empty string is not a valid capability. Empty string found as the $%s parameter in a function call to %s()"',
				$first_non_empty,
				'Invalid',
				array(
					$function_details['name'],
					$matched_content,
				)
			);
			return;
		}

		// Check if additional capabilities were registered via the ruleset and if the found capability matches any of those.
		$custom_capabilities = RulesetPropertyHelper::merge_custom_array( $this->custom_capabilities, array() );
		if ( isset( $custom_capabilities[ $matched_parameter ] ) ) {
			return;
		}

		if ( isset( $this->deprecated_capabilities[ $matched_parameter ] ) ) {
			$this->set_minimum_wp_version();
			$is_error = $this->wp_version_compare( $this->deprecated_capabilities[ $matched_parameter ], $this->minimum_wp_version, '<' );

			$data = array(
				$matched_parameter,
				$matched_content,
				$this->deprecated_capabilities[ $matched_parameter ],
			);

			MessageHelper::addMessage(
				$this->phpcsFile,
				'The capability "%s", found in the function call to %s(), has been deprecated since WordPress version %s.',
				$first_non_empty,
				$is_error,
				'Deprecated',
				$data
			);
			return;
		}

		if ( isset( $this->core_roles[ $matched_parameter ] ) ) {
			$this->phpcsFile->addError(
				'Capabilities should be used instead of roles. Found "%s" in function call to %s()',
				$first_non_empty,
				'RoleFound',
				array(
					$matched_parameter,
					$matched_content,
				)
			);
			return;
		}

		$this->phpcsFile->addWarning(
			'Found unknown capability "%s" in function call to %s(). Please check the spelling of the capability. If this is a custom capability, please verify the capability is registered with WordPress via a call to WP_Role(s)->add_cap().' . \PHP_EOL . 'Custom capabilities can be made known to this sniff by setting the "custom_capabilities" property in the PHPCS ruleset.',
			$first_non_empty,
			'Unknown',
			array(
				$matched_parameter,
				$matched_content,
			)
		);
	}
}
