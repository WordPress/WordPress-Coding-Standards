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
 * Warns about overwriting WordPress native global variables.
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
	 * @since 0.3.0
	 * @since 0.11.0 Changed visibility from public to protected.
	 * @since 0.12.0 Renamed from `$globals` to `$wp_globals` to be more descriptive.
	 *
	 * @var array
	 */
	protected $wp_globals = array(
		'_links_add_base'                  => true,
		'_links_add_target'                => true,
		'_menu_item_sort_prop'             => true,
		'_nav_menu_placeholder'            => true,
		'_new_bundled_files'               => true,
		'_old_files'                       => true,
		'_parent_pages'                    => true,
		'_registered_pages'                => true,
		'_updated_user_settings'           => true,
		'_wp_additional_image_sizes'       => true,
		'_wp_admin_css_colors'             => true,
		'_wp_default_headers'              => true,
		'_wp_deprecated_widgets_callbacks' => true,
		'_wp_last_object_menu'             => true,
		'_wp_last_utility_menu'            => true,
		'_wp_menu_nopriv'                  => true,
		'_wp_nav_menu_max_depth'           => true,
		'_wp_post_type_features'           => true,
		'_wp_real_parent_file'             => true,
		'_wp_registered_nav_menus'         => true,
		'_wp_sidebars_widgets'             => true,
		'_wp_submenu_nopriv'               => true,
		'_wp_suspend_cache_invalidation'   => true,
		'_wp_theme_features'               => true,
		'_wp_using_ext_object_cache'       => true,
		'action'                           => true,
		'active_signup'                    => true,
		'admin_body_class'                 => true,
		'admin_page_hooks'                 => true,
		'all_links'                        => true,
		'allowedentitynames'               => true,
		'allowedposttags'                  => true,
		'allowedtags'                      => true,
		'auth_secure_cookie'               => true,
		'authordata'                       => true,
		'avail_post_mime_types'            => true,
		'avail_post_stati'                 => true,
		'blog_id'                          => true,
		'blog_title'                       => true,
		'blogname'                         => true,
		'cat'                              => true,
		'cat_id'                           => true,
		'charset_collate'                  => true,
		'comment'                          => true,
		'comment_alt'                      => true,
		'comment_depth'                    => true,
		'comment_status'                   => true,
		'comment_thread_alt'               => true,
		'comment_type'                     => true,
		'comments'                         => true,
		'compress_css'                     => true,
		'compress_scripts'                 => true,
		'concatenate_scripts'              => true,
		'current_screen'                   => true,
		'current_site'                     => true,
		'current_user'                     => true,
		'currentcat'                       => true,
		'currentday'                       => true,
		'currentmonth'                     => true,
		'custom_background'                => true,
		'custom_image_header'              => true,
		'default_menu_order'               => true,
		'descriptions'                     => true,
		'domain'                           => true,
		'editor_styles'                    => true,
		'error'                            => true,
		'errors'                           => true,
		'EZSQL_ERROR'                      => true,
		'feeds'                            => true,
		'GETID3_ERRORARRAY'                => true,
		'hook_suffix'                      => true,
		'HTTP_RAW_POST_DATA'               => true,
		'id'                               => true,
		'in_comment_loop'                  => true,
		'interim_login'                    => true,
		'is_apache'                        => true,
		'is_chrome'                        => true,
		'is_gecko'                         => true,
		'is_IE'                            => true,
		'is_IIS'                           => true,
		'is_iis7'                          => true,
		'is_macIE'                         => true,
		'is_opera'                         => true,
		'is_safari'                        => true,
		'is_winIE'                         => true,
		'l10n'                             => true,
		'link'                             => true,
		'link_id'                          => true,
		'locale'                           => true,
		'locked_post_status'               => true,
		'lost'                             => true,
		'm'                                => true,
		'map'                              => true,
		'menu'                             => true,
		'menu_order'                       => true,
		'merged_filters'                   => true,
		'mode'                             => true,
		'monthnum'                         => true,
		'more'                             => true,
		'multipage'                        => true,
		'names'                            => true,
		'nav_menu_selected_id'             => true,
		'new_whitelist_options'            => true,
		'numpages'                         => true,
		'one_theme_location_no_menus'      => true,
		'opml'                             => true,
		'order'                            => true,
		'orderby'                          => true,
		'overridden_cpage'                 => true,
		'page'                             => true,
		'paged'                            => true,
		'pagenow'                          => true,
		'pages'                            => true,
		'parent_file'                      => true,
		'pass_allowed_html'                => true,
		'pass_allowed_protocols'           => true,
		'path'                             => true,
		'per_page'                         => true,
		'PHP_SELF'                         => true,
		'phpmailer'                        => true,
		'plugin_page'                      => true,
		'plugins'                          => true,
		'post'                             => true,
		'post_default_category'            => true,
		'post_default_title'               => true,
		'post_ID'                          => true,
		'post_id'                          => true,
		'post_mime_types'                  => true,
		'post_type'                        => true,
		'post_type_object'                 => true,
		'posts'                            => true,
		'preview'                          => true,
		'previouscat'                      => true,
		'previousday'                      => true,
		'previousweekday'                  => true,
		'redir_tab'                        => true,
		'required_mysql_version'           => true,
		'required_php_version'             => true,
		'rnd_value'                        => true,
		'role'                             => true,
		's'                                => true,
		'search'                           => true,
		'self'                             => true,
		'shortcode_tags'                   => true,
		'show_admin_bar'                   => true,
		'sidebars_widgets'                 => true,
		'status'                           => true,
		'submenu'                          => true,
		'submenu_file'                     => true,
		'super_admins'                     => true,
		'tab'                              => true,
		'table_prefix'                     => true,
		'tabs'                             => true,
		'tag'                              => true,
		'targets'                          => true,
		'tax'                              => true,
		'taxnow'                           => true,
		'taxonomy'                         => true,
		'term'                             => true,
		'text_direction'                   => true,
		'theme_field_defaults'             => true,
		'themes_allowedtags'               => true,
		'timeend'                          => true,
		'timestart'                        => true,
		'tinymce_version'                  => true,
		'title'                            => true,
		'totals'                           => true,
		'type'                             => true,
		'typenow'                          => true,
		'updated_timestamp'                => true,
		'upgrading'                        => true,
		'urls'                             => true,
		'user_email'                       => true,
		'user_ID'                          => true,
		'user_identity'                    => true,
		'user_level'                       => true,
		'user_login'                       => true,
		'user_url'                         => true,
		'userdata'                         => true,
		'usersearch'                       => true,
		'whitelist_options'                => true,
		'withcomments'                     => true,
		'wp'                               => true,
		'wp_actions'                       => true,
		'wp_admin_bar'                     => true,
		'wp_cockneyreplace'                => true,
		'wp_current_db_version'            => true,
		'wp_current_filter'                => true,
		'wp_customize'                     => true,
		'wp_dashboard_control_callbacks'   => true,
		'wp_db_version'                    => true,
		'wp_did_header'                    => true,
		'wp_embed'                         => true,
		'wp_file_descriptions'             => true,
		'wp_filesystem'                    => true,
		'wp_filter'                        => true,
		'wp_hasher'                        => true,
		'wp_header_to_desc'                => true,
		'wp_importers'                     => true,
		'wp_json'                          => true,
		'wp_list_table'                    => true,
		'wp_local_package'                 => true,
		'wp_locale'                        => true,
		'wp_meta_boxes'                    => true,
		'wp_object_cache'                  => true,
		'wp_plugin_paths'                  => true,
		'wp_post_statuses'                 => true,
		'wp_post_types'                    => true,
		'wp_queries'                       => true,
		'wp_query'                         => true,
		'wp_registered_sidebars'           => true,
		'wp_registered_widget_controls'    => true,
		'wp_registered_widget_updates'     => true,
		'wp_registered_widgets'            => true,
		'wp_rewrite'                       => true,
		'wp_rich_edit'                     => true,
		'wp_rich_edit_exists'              => true,
		'wp_roles'                         => true,
		'wp_scripts'                       => true,
		'wp_settings_errors'               => true,
		'wp_settings_fields'               => true,
		'wp_settings_sections'             => true,
		'wp_smiliessearch'                 => true,
		'wp_styles'                        => true,
		'wp_taxonomies'                    => true,
		'wp_the_query'                     => true,
		'wp_theme_directories'             => true,
		'wp_themes'                        => true,
		'wp_user_roles'                    => true,
		'wp_version'                       => true,
		'wp_widget_factory'                => true,
		'wp_xmlrpc_server'                 => true,
		'wpcommentsjavascript'             => true,
		'wpcommentspopupfile'              => true,
		'wpdb'                             => true,
		'wpsmiliestrans'                   => true,
		'year'                             => true,
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
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {
		$token = $this->tokens[ $stackPtr ];

		if ( T_VARIABLE === $token['code'] && '$GLOBALS' === $token['content'] ) {
			$bracketPtr = $this->phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );

			if ( false === $bracketPtr || T_OPEN_SQUARE_BRACKET !== $this->tokens[ $bracketPtr ]['code'] || ! isset( $this->tokens[ $bracketPtr ]['bracket_closer'] ) ) {
				return;
			}

			// Bow out if the array key contains a variable.
			$has_variable = $this->phpcsFile->findNext( T_VARIABLE, ( $bracketPtr + 1 ), $this->tokens[ $bracketPtr ]['bracket_closer'] );
			if ( false !== $has_variable ) {
				return;
			}

			// Retrieve the array key and avoid getting tripped up by some simple obfuscation.
			$var_name = '';
			$start    = ( $bracketPtr + 1 );
			for ( $ptr = $start; $ptr < $this->tokens[ $bracketPtr ]['bracket_closer']; $ptr++ ) {
				if ( T_CONSTANT_ENCAPSED_STRING === $this->tokens[ $ptr ]['code'] ) {
					$var_name .= $this->strip_quotes( $this->tokens[ $ptr ]['content'] );
				}
			}

			if ( ! isset( $this->wp_globals[ $var_name ] ) ) {
				return;
			}

			if ( true === $this->is_assignment( $this->tokens[ $bracketPtr ]['bracket_closer'] ) ) {
				$this->maybe_add_error( $stackPtr );
			}
		} elseif ( T_GLOBAL === $token['code'] ) {
			$search = array(); // Array of globals to watch for.
			$ptr    = ( $stackPtr + 1 );
			while ( $ptr ) {
				if ( ! isset( $this->tokens[ $ptr ] ) ) {
					break;
				}

				$var = $this->tokens[ $ptr ];

				// Halt the loop at end of statement.
				if ( T_SEMICOLON === $var['code'] ) {
					break;
				}

				if ( T_VARIABLE === $var['code'] ) {
					if ( isset( $this->wp_globals[ substr( $var['content'], 1 ) ] ) ) {
						$search[] = $var['content'];
					}
				}

				$ptr++;
			}
			unset( $var );

			if ( empty( $search ) ) {
				return;
			}

			// Only search from the end of the "global ...;" statement onwards.
			$start        = ( $this->phpcsFile->findEndOfStatement( $stackPtr ) + 1 );
			$end          = $this->phpcsFile->numTokens;
			$global_scope = true;

			// Is the global statement within a function call or closure ?
			// If so, limit the token walking to the function scope.
			$function_token = $this->phpcsFile->getCondition( $stackPtr, T_FUNCTION );
			if ( false === $function_token ) {
				$function_token = $this->phpcsFile->getCondition( $stackPtr, T_CLOSURE );
			}

			if ( false !== $function_token ) {
				if ( ! isset( $this->tokens[ $function_token ]['scope_closer'] ) ) {
					// Live coding, unfinished function.
					return;
				}

				$end          = $this->tokens[ $function_token ]['scope_closer'];
				$global_scope = false;
			}

			// Check for assignments to collected global vars.
			for ( $ptr = $start; $ptr < $end; $ptr++ ) {

				// If the global statement was in the global scope, skip over functions, classes and the likes.
				if ( true === $global_scope && in_array( $this->tokens[ $ptr ]['code'], array( T_FUNCTION, T_CLOSURE, T_CLASS, T_ANON_CLASS, T_INTERFACE, T_TRAIT ), true ) ) {
					if ( ! isset( $this->tokens[ $ptr ]['scope_closer'] ) ) {
						// Live coding, skip the rest of the file.
						return;
					}

					$ptr = $this->tokens[ $ptr ]['scope_closer'];
					continue;
				}

				if ( T_VARIABLE === $this->tokens[ $ptr ]['code']
					&& in_array( $this->tokens[ $ptr ]['content'], $search, true )
				) {
					// Don't throw false positives for static class properties.
					$previous = $this->phpcsFile->findPrevious( PHP_CodeSniffer_Tokens::$emptyTokens, ( $ptr - 1 ), null, true, null, true );
					if ( false !== $previous && T_DOUBLE_COLON === $this->tokens[ $previous ]['code'] ) {
						continue;
					}

					if ( true === $this->is_assignment( $ptr ) ) {
						$this->maybe_add_error( $ptr );
					}
				}
			}
		} // End if().

	} // End process_token().

	/**
	 * Add the error if there is no whitelist comment present and the assignment
	 * is not done from within a test method.
	 *
	 * @param int $stackPtr The position of the token to throw the error for.
	 *
	 * @return void
	 */
	public function maybe_add_error( $stackPtr ) {
		if ( ! $this->is_token_in_test_method( $stackPtr ) && ! $this->has_whitelist_comment( 'override', $stackPtr ) ) {
			$this->phpcsFile->addError( 'Overriding WordPress globals is prohibited', $stackPtr, 'OverrideProhibited' );
		}
	}

} // End class.
