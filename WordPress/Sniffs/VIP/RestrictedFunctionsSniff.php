<?php
/**
 * Restricts usage of some functions in VIP context
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 */
class WordPress_Sniffs_VIP_RestrictedFunctionsSniff extends WordPress_Sniffs_Functions_FunctionRestrictionsSniff
{

	/**
	 * Groups of functions to restrict
	 *
	 * Example: groups => array(
	 * 	'lambda' => array(
	 * 		'type' => 'error' | 'warning',
	 * 		'message' => 'Use anonymous functions instead please!',
	 * 		'functions' => array( 'eval', 'create_function' ),
	 * 	)
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(

			'switch_to_blog' => array(
				'type'      => 'error',
				'message'   => '%s is not something you should ever need to do in a VIP theme context. Instead use an API (XML-RPC, REST) to interact with other sites if needed.',
				'functions' => array(
					'switch_to_blog',
					'restore_current_blog',
					'ms_is_switched',
					'wp_get_sites',
					),
			),

			'eval' => array(
				'type' => 'error',
				'message' => '%s is prohibited, please use Anonymous functions instead.',
				'functions' => array(
					'eval',
					'create_function',
				),
			),

			'file_get_contents' => array(
				'type' => 'warning',
				'message' => '%s is highly discouraged, please use wpcom_vip_file_get_contents() instead.',
				'functions' => array(
					'file_get_contents',
					'vip_wp_file_get_contents',
				),
			),

			'get_term_link' => array(
				'type' => 'error',
				'message' => '%s is prohibited as it is an uncached function. Please use wpcom_vip_get_term_link() instead.',
				'functions' => array(
					'get_term_link',
					'get_tag_link',
					'get_category_link',
				),
			),

			'get_page_by_path' => array(
				'type' => 'error',
				'message' => '%s is prohibited as it is an uncached function. Please use wpcom_vip_get_page_by_path() instead.',
				'functions' => array(
					'get_page_by_path',
				),
			),

			'get_page_by_title' => array(
				'type' => 'error',
				'message' => '%s is prohibited, please use wpcom_vip_get_page_by_title() instead.',
				'functions' => array(
					'get_page_by_title',
				),
			),

			'libxml_set_external_entity_loader' => array(
				'type' => 'error',
				'message' => '%s is prohibited for security reasons.',
				'functions' => array(
					'libxml_set_external_entity_loader',
				),
			),

			'sessions' => array(
				'type' => 'error',
				'message' => '%s is prohibited. Sessions are not available on WPCOM',
				'functions' => array(
					'session_start',
					'session_set_save_handler',
					'session_save_path',
					'session_set_cookie_params',
					'session_cache_expire',
					'session_cache_limiter',
					'session_encode',
					'session_id',
					'session_name',
					'session_regenerate_id',
					'session_register',
					'session_unregister',
					'session_status',
					'session_write_close',
					'session_unset',
				),
			),

			'get_term_by' => array(
				'type' => 'error',
				'message' => '%s is prohibited as it is an uncached function. Please use wpcom_vip_get_term_by() instead.',
				'functions' => array(
					'get_term_by',
				),
			),

			'wp_get_post_*' => array(
				'type' => 'error',
				'message' => '%s() usage is highly discouraged as it is an uncached function. Please use wpcom_vip_count_user_posts() instead.',
				'functions' => array(
					'wp_get_post_categories',
					'wp_get_post_tags',
				),
			),
			'wp_get_object_terms' => array(
				'type' => 'error',
				'message' => '%s() usage is highly discouraged as it is an uncached function. Please use get_the_terms() instead.',
				'functions' => array(
					'wp_get_post_categories',
					'wp_get_post_tags',
				),
			),

			'url_to_postid' => array(
				'type' => 'error',
				'message' => '%s() is prohibited as it is an uncached function. Please use wpcom_vip_url_to_postid() instead.',
				'functions' => array(
					'url_to_postid',
					'url_to_post_id',
				),
			),

			'term_exists' => array(
				'type' => 'error',
				'message' => '%s() is prohibited as it is uncached, please use wpcom_vip_term_exists() instead.',
				'functions' => array(
					'term_exists',
					'category_exists',
					'tag_exists',
				),
			),

			'attachment_url_to_postid' => array(
				'type' => 'error',
				'message' => '%s() is prohibited as it is uncached, please use wpcom_vip_attachment_url_to_postid() instead.',
				'functions' => array(
					'attachment_url_to_postid',
				),
			),

			'wp_remote_get' => array(
				'type' => 'error',
				'message' => '%s() is highly discouraged as it is uncached, please use wpcom_vip_file_get_contents() or vip_safe_wp_remote_get() instead.',
				'functions' => array(
					'wp_remote_get',
				),
			),
			'wp_oembed_get' => array(
				'type' => 'error',
				'message' => '%s() is highly discouraged as it is uncached, please use wpcom_vip_wp_oembed_get() instead.',
				'functions' => array(
					'wp_oembed_get',
				),
			),

			'curl' => array(
				'type' => 'error',
				'message' => 'Using cURL functions is highly discouraged within VIP context. Check (Fetching Remote Data) on VIP Documentation.',
				'functions' => array(
					'curl_*',
				),
			),

			'extract' => array(
				'type' => 'warning',
				'message' => '%s() usage is highly discouraged, due to the complexity and unintended issues it might cause.',
				'functions' => array(
					'extract',
				),
			),

			'custom_role' => array(
				'type' => 'error',
				'message' => 'Use wpcom_vip_add_role() instead of add_role()',
				'functions' => array(
					'add_role',
					'get_role',
					'remove_role',
					'add_cap',
					'remove_cap',
				),
			),

			'cookies' => array(
				'type' => 'warning',
				'message' => 'Due to using Batcache, server side based client related logic will most likely not work as intended, use JS instead.',
				'functions' => array(
					'setcookie',
				),
			),

			'wp_old_slug_redirect' => array(
				'type' => 'warning',
				'message' => '%s() is highly discouraged as it is uncached, please use wpcom_vip_old_slug_redirect() instead.',
				'functions' => array(
					'wp_old_slug_redirect',
				),
			),

			'query_posts' => array(
				'type' => 'error',
				'message' => '%s() is not allowed. Please use WP_Query or get_posts (with suppress_filters => false)',
				'functions' => array(
					'query_posts',
				),
			),

			'count_user_posts' => array(
				'type' => 'error',
				'message' => '%s() usage is highly discouraged as it is an uncached function. Please use wpcom_vip_count_user_posts() instead.',
				'functions' => array(
					'count_user_posts',
				),
			),

			'get_adjacent_post' => array(
				'type' => 'error',
				'message' => '%s() usage is highly discouraged as it is a very slow function and does not have proper caching. Please use wpcom_vip_get_adjacent_post() instead.',
				'functions' => array(
					'get_adjacent_post',
					'get_previous_post',
					'get_next_post',
				),
			),

			'wp_count_comments' => array(
				'type' => 'error',
				'message' => '%s() usage is highly discouraged as it is an uncached function. A replacement is not yet available.',
				'functions' => array(
					'get_adjacent_post',
					'get_previous_post',
					'get_next_post',
				),
			),

			'get_cat_ID' => array(
				'type' => 'error',
				'message' => '%s() usage is highly discouraged as it is an uncached function. Please use wpcom_vip_get_term_by() instead.',
				'functions' => array(
					'get_cat_ID',
				),
			),

			'get_posts' => array(
				'type' => 'warning',
				'message' => 'When using %s() Ensure suppress_filters is set to false to the query can be cached.',
				'functions' => array(
					'get_posts',
					'get_pages',
					'wp_get_recent_posts',
				),
			),
			
			'get_children' => array(
				'type' => 'warning',
				'message' => '%s() usage is highly discouraged as it does an uncached no limit query that can break if the parent page post_id = 0. Use a custom WP_Query',
				'functions' => array(
					'get_children',
				),
			),

			'user_meta' => array(
				'type' => 'error',
				'message' => '%s() usage is highly discouraged, check VIP documentation on "Working with wp_users"',
				'functions' => array(
					'get_user_meta',
					'update_user_meta',
					'delete_user_meta',
					'add_user_meta',
				),
			),

			'debugging_warning' => array(
				'type' => 'warning',
				'message' => '%s() usage is highly discouraged',
				'functions' => array(
					'var_dump',
					'var_export',
					'print_r',
				),
			),

			'debugging_error' => array(
				'type' => 'error',
				'message' => '%s() usage is prohibited.',
				'functions' => array(
					'error_log',
					'var_dump',
					'var_export',
					'wp_debug_backtrace_summary',
					'debug_backtrace',
					'debug_print_backtrace',
					'trigger_error',
					'set_error_handler',
					'error_reporting',
				),
			),
			
			'environment' => array(
				'type' => 'error',
				'message' => '%s() usage is prohibited.',
				'functions' => array(
					'date_default_timezone_set',
					'ini_set',
				),
			),
			
			'mobile' => array(
				'type' => 'warning',
				'message' => '%s() is not batcache-friendly, please use jetpack_is_mobile()',
				'functions' => array(
					'wp_is_mobile',
				),
			),
						
			'serialize' => array(
				'type' => 'warning',
				'message' => 'Serialized data hasknown vulnerability problems with Object Injection. See https://www.owasp.org/index.php/PHP_Object_Injection\ JSON is generally a better approach for serializing data.',
				'functions' => array(
					'serialize',
					'unserialize',
				),
			),

			'filesystem_error' => array(
				'type' => 'error',
				'message' => '%s() usage is prohibited.',
				'functions' => array(
					'chgrp',
					'chmod',
					'clearstatcache',
					'copy',
					'delete',
					'disk_free_space',
					'diskfreespace',
					'disk_total_space',
					'fflush',
					'fileatime',
					'filectime',
					'filegroup',
					'fileowner',
					'fileperms',
					'flock',
					'fnmatch',
					'glob',
					'mkdir',
					'link',
					'pclose',
					'rmdir',
					'tmpfile',
					'touch',
					'unlink',
				),
			),

			'filesystem_warning' => array(
				'type' => 'warning',
				'message' => '%s() should only be used in certain circumstances and with caution',
				'functions' => array(
					'fclose',
					'feof',
					'fgetc',
					'fgetcsv',
					'fgets',
					'fgetss',
					'file_put_contents',
					'file',
					'filesize',
					'filetype',
					'fopen',
					'fpassthru',
					'fputcsv',
					'fputs',
					'fputs',
					'fscanf',
					'fseek',
					'fstat',
					'ftell',
					'ftruncate',
					'fwrite',
					'is_executable',
					'is_uploaded_file',
					'move_uploaded_file',
					'is_writable',
					'is_writeable',
					'lchgrp',
					'lchown',
					'link',
					'linkinfo',
					'lstat',
					'readfile',
					'rename',
				),
			),

		);
	}
}//end class
