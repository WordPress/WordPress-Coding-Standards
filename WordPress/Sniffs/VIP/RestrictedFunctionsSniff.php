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
				'functions' => array( 'switch_to_blog' ),
				),

			'create_function' => array(
				'type' => 'warning',
				'message' => '%s is discouraged, please use Anonymous functions instead.',
				'functions' => array(
					'create_function',
				),
			),

			'eval' => array(
				'type' => 'error',
				'message' => '%s is prohibited, please use Anonymous functions instead.',
				'functions' => array(
					'eval',
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
				'message' => '%s is prohibited, please use wpcom_vip_get_term_link() instead.',
				'functions' => array(
					'get_term_link',
				),
			),

			'get_tag_link' => array(
				'type' => 'error',
				'message' => '%s is prohibited, please use wpcom_vip_get_term_link() instead.',
				'functions' => array(
					'get_tag_link',
				),
			),

			'get_page_by_path' => array(
				'type' => 'error',
				'message' => '%s is prohibited, please use wpcom_vip_get_page_by_path() instead.',
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

			'get_term_by' => array(
				'type' => 'error',
				'message' => '%s is prohibited, please use wpcom_vip_get_term_by() instead.',
				'functions' => array(
					'get_term_by',
				),
			),

			'get_category_by_slug' => array(
				'type' => 'error',
				'message' => '%s is prohibited, please use wpcom_vip_get_category_by_slug() instead.',
				'functions' => array(
					'get_category_by_slug',
				),
			),

			'url_to_postid' => array(
				'type' => 'error',
				'message' => '%s is prohibited, please use wpcom_vip_url_to_postid() instead.',
				'functions' => array(
					'url_to_postid',
				),
			),

			'wp_remote_get' => array(
				'type' => 'warning',
				'message' => '%s is highly discouraged, please use vip_safe_wp_remote_get() instead.',
				'functions' => array(
					'wp_remote_get',
					),
				),

			'curl' => array(
				'type' => 'warning',
				'message' => 'Using cURL functions is highly discouraged within VIP context. Check (Fetching Remote Data) on VIP Documentation.',
				'functions' => array(
					'curl_*',
					)
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
					),
				),

			'cookies' => array(
				'type' => 'warning',
				'message' => 'Due to using Batcache, server side based client related logic will not work, use JS instead.',
				'functions' => array(
					'setcookie',
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

			);
	}


}//end class
