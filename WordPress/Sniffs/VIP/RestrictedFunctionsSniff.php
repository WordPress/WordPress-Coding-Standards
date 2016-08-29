<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Restricts usage of some functions in VIP context.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.10.0 The checks for `extract()` and the POSIX functions have been replaced by
 *                 the stand-alone sniffs WordPress_Sniffs_Functions_DontExtractSniff and
 *                 WordPress_Sniffs_PHP_POSIXFunctionsSniff respectively.
 */
class WordPress_Sniffs_VIP_RestrictedFunctionsSniff extends WordPress_AbstractFunctionRestrictionsSniff {

	/**
	 * Groups of functions to restrict.
	 *
	 * Example: groups => array(
	 * 	'lambda' => array(
	 * 		'type'      => 'error' | 'warning',
	 * 		'message'   => 'Use anonymous functions instead please!',
	 * 		'functions' => array( 'eval', 'create_function' ),
	 * 	)
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(
			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#switch_to_blog
			'switch_to_blog' => array(
				'type'      => 'error',
				'message'   => '%s is not something you should ever need to do in a VIP theme context. Instead use an API (XML-RPC, REST) to interact with other sites if needed.',
				'functions' => array( 'switch_to_blog' ),
			),

			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#eval-and-create_function
			'create_function' => array(
				'type'      => 'warning',
				'message'   => '%s is discouraged, please use Anonymous functions instead.',
				'functions' => array(
					'create_function',
				),
			),

			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#eval-and-create_function
			'eval' => array(
				'type'      => 'error',
				'message'   => '%s is prohibited, please use Anonymous functions instead.',
				'functions' => array(
					'eval',
				),
			),

			'file_get_contents' => array(
				'type'      => 'warning',
				'message'   => '%s is highly discouraged, please use wpcom_vip_file_get_contents() instead.',
				'functions' => array(
					'file_get_contents',
					'vip_wp_file_get_contents',
				),
			),

			'get_term_link' => array(
				'type'      => 'error',
				'message'   => '%s is prohibited, please use wpcom_vip_get_term_link() instead.',
				'functions' => array(
					'get_term_link',
					'get_tag_link',
					'get_category_link',
				),
			),

			'get_page_by_path' => array(
				'type'      => 'error',
				'message'   => '%s is prohibited, please use wpcom_vip_get_page_by_path() instead.',
				'functions' => array(
					'get_page_by_path',
				),
			),

			'get_page_by_title' => array(
				'type'      => 'error',
				'message'   => '%s is prohibited, please use wpcom_vip_get_page_by_title() instead.',
				'functions' => array(
					'get_page_by_title',
				),
			),

			'get_term_by' => array(
				'type'      => 'error',
				'message'   => '%s is prohibited, please use wpcom_vip_get_term_by() instead.',
				'functions' => array(
					'get_term_by',
					'get_cat_ID',
				),
			),

			'get_category_by_slug' => array(
				'type'      => 'error',
				'message'   => '%s is prohibited, please use wpcom_vip_get_category_by_slug() instead.',
				'functions' => array(
					'get_category_by_slug',
				),
			),

			'url_to_postid' => array(
				'type'      => 'error',
				'message'   => '%s is prohibited, please use wpcom_vip_url_to_postid() instead.',
				'functions' => array(
					'url_to_postid',
					'url_to_post_id',
				),
			),

			'attachment_url_to_postid' => array(
				'type'      => 'error',
				'message'   => '%s is prohibited, please use wpcom_vip_attachment_url_to_postid() instead.',
				'functions' => array(
					'attachment_url_to_postid',
				),
			),

			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#remote-calls
			'wp_remote_get' => array(
				'type'      => 'warning',
				'message'   => '%s is highly discouraged, please use vip_safe_wp_remote_get() instead.',
				'functions' => array(
					'wp_remote_get',
				),
			),

			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#remote-calls
			'curl' => array(
				'type'      => 'warning',
				'message'   => 'Using cURL functions is highly discouraged within VIP context. Check (Fetching Remote Data) on VIP Documentation.',
				'functions' => array(
					'curl_*',
				),
			),

			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#custom-roles
			'custom_role' => array(
				'type'      => 'error',
				'message'   => 'Use wpcom_vip_add_role() instead of add_role()',
				'functions' => array(
					'add_role',
				),
			),

			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#caching-constraints
			'cookies' => array(
				'type'      => 'warning',
				'message'   => 'Due to using Batcache, server side based client related logic will not work, use JS instead.',
				'functions' => array(
					'setcookie',
				),
			),

			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#working-with-wp_users-and-user_meta
			'user_meta' => array(
				'type'      => 'error',
				'message'   => '%s() usage is highly discouraged, check VIP documentation on "Working with wp_users"',
				'functions' => array(
					'get_user_meta',
					'update_user_meta',
					'delete_user_meta',
					'add_user_meta',
				),
			),

			// @todo Introduce a sniff specific to get_posts() that checks for suppress_filters=>false being supplied.
			'get_posts' => array(
				'type'      => 'warning',
				'message'   => '%s is discouraged in favor of creating a new WP_Query() so that Advanced Post Cache will cache the query, unless you explicitly supply suppress_filters => false.',
				'functions' => array(
					'get_posts',
					'wp_get_recent_posts',
					'get_children',
				),
			),

			'wp_get_post_terms' => array(
				'type'      => 'error',
				'message'   => '%s is highly discouraged due to not being cached; please use get_the_terms() along with wp_list_pluck() to extract the IDs.',
				'functions' => array(
					'wp_get_post_terms',
					'wp_get_post_categories',
					'wp_get_post_tags',
					'wp_get_object_terms',
				),
			),

			'term_exists' => array(
				'type'      => 'error',
				'message'   => '%s is highly discouraged due to not being cached; please use wpcom_vip_term_exists() instead.',
				'functions' => array(
					'term_exists',
				),
			),

			'count_user_posts' => array(
				'type'      => 'error',
				'message'   => '%s is highly discouraged due to not being cached; please use wpcom_vip_count_user_posts() instead.',
				'functions' => array(
					'count_user_posts',
				),
			),

			'wp_old_slug_redirect' => array(
				'type'      => 'error',
				'message'   => '%s is highly discouraged due to not being cached; please use wpcom_vip_old_slug_redirect() instead.',
				'functions' => array(
					'wp_old_slug_redirect',
				),
			),

			'get_adjacent_post' => array(
				'type'      => 'error',
				'message'   => '%s is highly discouraged due to not being cached; please use wpcom_vip_get_adjacent_post() instead.',
				'functions' => array(
					'get_adjacent_post',
					'get_previous_post',
					'get_previous_post_link',
					'get_next_post',
					'get_next_post_link',
				),
			),

			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#use-wp_parse_url-instead-of-parse_url
			'parse_url' => array(
				'type'      => 'warning',
				'message'   => '%s is discouraged due to a lack for backwards-compatibility in PHP versions; please use wp_parse_url() instead.',
				'functions' => array(
					'parse_url',
				),
			),

			'get_intermediate_image_sizes' => array(
				'type'      => 'error',
				'message'   => 'Intermediate images do not exist on the VIP platform, and thus get_intermediate_image_sizes() returns an empty array() on the platform. This behavior is intentional to prevent WordPress from generating multiple thumbnails when images are uploaded.',
				'functions' => array(
					'get_intermediate_image_sizes',
				),
			),

			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#serializing-data
			'serialize' => array(
				'type'      => 'warning',
				'message'   => '%s Serialized data has <a href=\'https://www.owasp.org/index.php/PHP_Object_Injection\'>known vulnerability problems</a> with Object Injection. JSON is generally a better approach for serializing data.',
				'functions' => array(
					'serialize',
					'unserialize',
				),
			),

			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#commented-out-code-debug-code-or-output
			'error_log' => array(
				'type'      => 'error',
				'message'   => '%s Debug code is not allowed on VIP Production',
				'functions' => array(
					'error_log',
					'var_dump',
					'print_r',
					'trigger_error',
					'set_error_handler',
				),
			),

			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#use-wp_safe_redirect-instead-of-wp_redirect
			'wp_redirect' => array(
				'type'     => 'warning',
				'message'   => '%s Using wp_safe_redirect(), along with the allowed_redirect_hosts filter, can help avoid any chances of malicious redirects within code. It’s also important to remember to call exit() after a redirect so that no other unwanted code is executed.',
				'functions' => array(
					'wp_redirect',
				),
			),

			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#mobile-detection
			'wp_is_mobile' => array(
				'type'      => 'error',
				'message'   => '%s When targeting mobile visitors, jetpack_is_mobile() should be used instead of wp_is_mobile. It is more robust and works better with full page caching.',
				'functions' => array(
					'wp_is_mobile',
				),
			),

			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#encoding-values-used-when-creating-a-url-or-passed-to-add_query_arg
			'urlencode' => array(
				'type'      => 'warning',
				'message'   => '%s should only be used when dealing with legacy applications, rawurlencode() should now be used instead. See http://php.net/manual/en/function.rawurlencode.php and http://www.faqs.org/rfcs/rfc3986.html',
				'functions' => array(
					'urlencode',
				),
			),

			// @link https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#settings-alteration
			'runtime_configuration' => array(
				'type'      => 'error',
				'message'   => '%s is prohibited, changing configuration at runtime is not allowed on VIP Production.',
				'functions' => array(
					'dl',
					'error_reporting',
					'ini_alter',
					'ini_restore',
					'ini_set',
					'magic_quotes_runtime',
					'set_magic_quotes_runtime',
					'apache_setenv',
					'putenv',
					'set_include_path',
					'restore_include_path',
				),
			),

			'prevent_path_disclosure' => array(
				'type'      => 'error',
				'message'   => '%s is prohibited as it can lead to full path disclosure.',
				'functions' => array(
					'error_reporting',
					'phpinfo',
				),
			),

		);
	} // end getGroups()

} // End class.
