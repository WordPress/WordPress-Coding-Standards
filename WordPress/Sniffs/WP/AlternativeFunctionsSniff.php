<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Discourages the use of various functions and suggests (WordPress) alternatives.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.11.0
 */
class WordPress_Sniffs_WP_AlternativeFunctionsSniff extends WordPress_AbstractFunctionRestrictionsSniff {

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
			'curl' => array(
				'type'      => 'warning',
				'message'   => 'Using cURL functions is highly discouraged. Use wp_remote_get() instead.',
				'functions' => array(
					'curl_close',
					'curl_copy_handle',
					'curl_errno',
					'curl_error',
					'curl_escape',
					'curl_exec',
					'curl_file_create',
					'curl_getinfo',
					'curl_init',
					'curl_multi_add_handle',
					'curl_multi_close',
					'curl_multi_exec',
					'curl_multi_getcontent',
					'curl_multi_info_read',
					'curl_multi_init',
					'curl_multi_remove_handle',
					'curl_multi_select',
					'curl_multi_setopt',
					'curl_multi_strerror',
					'curl_pause',
					'curl_reset',
					'curl_setopt_array',
					'curl_setopt',
					'curl_share_close',
					'curl_share_init',
					'curl_share_setopt',
					'curl_strerror',
					'curl_unescape',
					'curl_version',
				),
			),

			'parse_url' => array(
				'type'      => 'warning',
				'message'   => '%s() is discouraged due to a lack for backwards-compatibility in PHP versions; use wp_parse_url() instead.',
				'functions' => array(
					'parse_url',
				),
			),

			'json_encode' => array(
				'type'      => 'warning',
				'message'   => '%s() is discouraged. Use wp_json_encode() instead.',
				'functions' => array(
					'json_encode',
				),
			),

			'file_get_contents' => array(
				'type'      => 'warning',
				'message'   => '%s() is discouraged. Use wp_remote_get() instead.',
				'functions' => array(
					'file_get_contents',
				),
			),

		);
	} // end getGroups()

} // end class
