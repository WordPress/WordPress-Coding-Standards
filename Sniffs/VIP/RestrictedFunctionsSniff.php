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

			'lambda' => array(
				'type' => 'error',
				'message' => '%s is prohibited, please use Anonymous functions instead.',
				'functions' => array( 
					'eval', 
					'create_function',
					),
				),

			'file_get_contents' => array(
				'type' => 'error',
				'message' => '%s is prohibited, please use wpcom_vip_file_get_contents() instead.',
				'functions' => array(
					'file_get_contents',
					'vip_wp_file_get_contents',
					),
				),

			'wp_remote_get' => array(
				'type' => 'error',
				'message' => '%s is prohibited, please use vip_safe_wp_remote_get() instead.',
				'functions' => array(
					'wp_remote_get',
					),
				),

			'curl' => array(
				'type' => 'error',
				'message' => 'Using cURL functions is not allowed within VIP context. Check (Fetching Remote Data) on VIP Documentation.',
				'functions' => array(
					'curl_*',
					)
				),

			'extract' => array(
				'type' => 'warning',
				'message' => '%s() usage is highly discouraged, due to the complexity and unintended issues it might cause.',
				'functions' => array(
					'extract'
					),
				),

			);
	}


}//end class
