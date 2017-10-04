<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\WP;

use WordPress\AbstractFunctionRestrictionsSniff;

/**
 * Discourages the use of various functions and suggests (WordPress) alternatives.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.11.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 */
class AlternativeFunctionsSniff extends AbstractFunctionRestrictionsSniff {

	/**
	 * Groups of functions to restrict.
	 *
	 * Example: groups => array(
	 *  'lambda' => array(
	 *      'type'      => 'error' | 'warning',
	 *      'message'   => 'Use anonymous functions instead please!',
	 *      'functions' => array( 'file_get_contents', 'create_function' ),
	 *  )
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
					'curl_*',
				),
			),

			'parse_url' => array(
				'type'      => 'warning',
				'message'   => '%s() is discouraged because of inconsistency in the output across PHP versions; use wp_parse_url() instead.',
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

			'file_system_read' => array(
				'type'      => 'warning',
				'message'   => 'File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: %s()',
				'functions' => array(
					'readfile',
					'fopen',
					'fsockopen',
					'pfsockopen',
					'fclose',
					'fread',
					'fwrite',
					'file_put_contents',
					'file_get_contents',
				),
			),

		);
	} // End getGroups().

} // End class.
