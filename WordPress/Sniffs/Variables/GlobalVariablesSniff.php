<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * WordPress_Sniffs_Variables_GlobalVariablesSniff.
 *
 * Warns about usage of global variables used by WordPress
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.4.0 This class now extends WordPress_Sniff.
 */
class WordPress_Sniffs_Variables_GlobalVariablesSniff extends WordPress_Sniff {

	/**
	 * List of global WP variables.
	 *
	 * @var array
	 */
	public $globals = array(
		'comment',
		'comment_alt',
		'comment_depth',
		'comment_thread_alt',
		'wp_rewrite',
		'in_comment_loop',
		'wp_query',
		'withcomments',
		'post',
		'wpdb',
		'id',
		'user_login',
		'user_ID',
		'user_identity',
		'overridden_cpage',
		'wpcommentspopupfile',
		'wpcommentsjavascript',
		'shortcode_tags',
		'wp_version',
		'wp_scripts',
		'comments',
		'is_IE',
		'_wp_registered_nav_menus',
		'_menu_item_sort_prop',
		'wp_roles',
		'wp_object_cache',
		'currentcat',
		'previouscat',
		'blog_id',
		'is_macIE',
		'is_winIE',
		'plugin_page',
		'wp_themes',
		'wp_rich_edit_exists',
		'allowedposttags',
		'allowedtags',
		'allowedentitynames',
		'pass_allowed_html',
		'pass_allowed_protocols',
		'wp_post_statuses',
		'wp_post_types',
		'wp',
		'_wp_post_type_features',
		'_wp_suspend_cache_invalidation',
		'wp_theme_directories',
		'wp_locale',
		'locale',
		'l10n',
		'_wp_additional_image_sizes',
		'wp_embed',
		'wp_taxonomies',
		'sidebars_widgets',
		'wp_registered_widgets',
		'wp_registered_widget_controls',
		'wp_registered_sidebars',
		'wp_registered_widget_updates',
		'_wp_admin_css_colors',
		'concatenate_scripts',
		'compress_scripts',
		'wp_styles',
		'compress_css',
		'wp_the_query',
		'_updated_user_settings',
		'wp_filter',
		'wp_actions',
		'merged_filters',
		'wp_current_filter',
		'wp_plugin_paths',
		'GETID3_ERRORARRAY',
		'current_user',
		'phpmailer',
		'is_IIS',
		'wp_hasher',
		'rnd_value',
		'auth_secure_cookie',
		'userdata',
		'user_level',
		'user_email',
		'user_url',
		'wp_customize',
		'wp_widget_factory',
		'_wp_deprecated_widgets_callbacks',
		'_wp_sidebars_widgets',
		'error',
		'wp_cockneyreplace',
		'wpsmiliestrans',
		'wp_smiliessearch',
		'_links_add_base',
		'_links_add_target',
		'tinymce_version',
		'PHP_SELF',
		'required_php_version',
		'upgrading',
		'timestart',
		'timeend',
		'table_prefix',
		'_wp_using_ext_object_cache',
		'text_direction',
		'custom_image_header',
		'post_default_title',
		'post_default_category',
		'currentday',
		'previousday',
		'wp_header_to_desc',
		'wp_xmlrpc_server',
		'submenu',
		'is_apache',
		'is_iis7',
		'current_site',
		'domain',
		'm',
		'monthnum',
		'year',
		'posts',
		'previousweekday',
		'wp_rich_edit',
		'is_gecko',
		'is_opera',
		'is_safari',
		'is_chrome',
		'wp_local_package',
		'wp_user_roles',
		'super_admins',
		'_wp_default_headers',
		'editor_styles',
		'_wp_theme_features',
		'custom_background',
		'wp_did_header',
		'wp_admin_bar',
		'tag',
		'show_admin_bar',
		'pagenow',
		'HTTP_RAW_POST_DATA',
		'path',
		'wp_json',
		'page',
		'more',
		'preview',
		'pages',
		'multipage',
		'numpages',
		'paged',
		'authordata',
		'currentmonth',
		'EZSQL_ERROR',
		'required_mysql_version',
		'wp_db_version',
		'opml',
		'map',
		'updated_timestamp',
		'all_links',
		'names',
		'urls',
		'targets',
		'descriptions',
		'feeds',
		'wp_filesystem',
		'menu_order',
		'default_menu_order',
		'_wp_nav_menu_max_depth',
		'_nav_menu_placeholder',
		'wp_meta_boxes',
		'one_theme_location_no_menus',
		'nav_menu_selected_id',
		'post_ID',
		'link_id',
		'action',
		'link',
		'tabs',
		'tab',
		'type',
		'term',
		'redir_tab',
		'post_mime_types',
		'menu',
		'admin_page_hooks',
		'_registered_pages',
		'_parent_pages',
		'_wp_last_object_menu',
		'_wp_last_utility_menu',
		'_wp_real_parent_file',
		'_wp_submenu_nopriv',
		'parent_file',
		'typenow',
		'_wp_menu_nopriv',
		'title',
		'new_whitelist_options',
		'whitelist_options',
		'wp_list_table',
		's',
		'mode',
		'post_type_object',
		'avail_post_stati',
		'per_page',
		'locked_post_status',
		'cat',
		'lost',
		'avail_post_mime_types',
		'$var',
		'errors',
		'cat_id',
		'orderby',
		'order',
		'post_type',
		'taxonomy',
		'tax',
		'wp_queries',
		'charset_collate',
		'wp_current_db_version',
		'wp_importers',
		'wp_file_descriptions',
		'theme_field_defaults',
		'themes_allowedtags',
		'post_id',
		'comment_status',
		'search',
		'comment_type',
		'wp_settings_sections',
		'wp_settings_fields',
		'wp_settings_errors',
		'hook_suffix',
		'admin_body_class',
		'current_screen',
		'taxnow',
		'status',
		'totals',
		'_old_files',
		'_new_bundled_files',
		'usersearch',
		'role',
		'wp_dashboard_control_callbacks',
		'plugins',
		'self',
		'submenu_file',
		'blogname',
		'blog_title',
		'active_signup',
		'interim_login',
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_GLOBAL,
			T_VARIABLE,
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
		$this->init( $phpcsFile );
		$token  = $this->tokens[ $stackPtr ];

		$search = array(); // Array of globals to watch for.

		if ( T_VARIABLE === $token['code'] && '$GLOBALS' === $token['content'] ) {
			$bracketPtr = $phpcsFile->findNext( array( T_WHITESPACE ), ( $stackPtr + 1 ), null, true );

			if ( T_OPEN_SQUARE_BRACKET !== $this->tokens[ $bracketPtr ]['code'] ) {
				return;
			}

			$varPtr   = $phpcsFile->findNext( T_WHITESPACE, ( $bracketPtr + 1 ), $this->tokens[ $bracketPtr ]['bracket_closer'], true );
			$varToken = $this->tokens[ $varPtr ];

			if ( ! in_array( trim( $varToken['content'], '\'"' ), $this->globals, true ) ) {
				return;
			}

			$assignment = $phpcsFile->findNext( T_WHITESPACE, ( $this->tokens[ $bracketPtr ]['bracket_closer'] + 1 ), null, true );

			if ( $assignment && T_EQUAL === $this->tokens[ $assignment ]['code'] ) {
				if ( ! $this->has_whitelist_comment( 'override', $assignment ) ) {
					$phpcsFile->addError( 'Overriding WordPress globals is prohibited', $stackPtr, 'OverrideProhibited' );
					return;
				}
			}

			return;

		} elseif ( T_GLOBAL === $token['code'] ) {
			$ptr = ( $stackPtr + 1 );
			while ( $ptr ) {
				$ptr++;
				$var = $this->tokens[ $ptr ];
				if ( T_VARIABLE === $var['code'] ) {
					$varname = substr( $var['content'], 1 );
					if ( in_array( $varname, $this->globals, true ) ) {
						$search[] = $varname;
					}
				}
				// Halt the loop.
				if ( T_SEMICOLON === $var['code'] ) {
					$ptr = false;
				}
			}
			if ( empty( $search ) ) {
				return;
			}

			// Check for assignments to collected global vars.
			foreach ( $this->tokens as $ptr => $token ) {
				if ( T_VARIABLE === $token['code'] && in_array( substr( $token['content'], 1 ), $search, true ) ) {
					$next = $phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, ( $ptr + 1 ), null, true, null, true );
					if ( T_EQUAL === $this->tokens[ $next ]['code'] ) {
						if ( ! $this->has_whitelist_comment( 'override', $next ) ) {
							$phpcsFile->addError( 'Overriding WordPress globals is prohibited', $ptr, 'OverrideProhibited' );
						}
					}
				}
			}
		}
	} // end process()

} // End class.
