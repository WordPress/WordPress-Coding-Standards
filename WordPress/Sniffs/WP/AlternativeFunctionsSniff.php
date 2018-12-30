<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WP;

use WordPressCS\WordPress\AbstractFunctionRestrictionsSniff;

/**
 * Discourages the use of various functions and suggests (WordPress) alternatives.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.11.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   1.0.0  - Takes the minimum supported WP version into account.
 *                 - Takes exceptions based on passed parameters into account.
 *
 * @uses    \WordPressCS\WordPress\Sniff::$minimum_supported_version
 */
class AlternativeFunctionsSniff extends AbstractFunctionRestrictionsSniff {

	/**
	 * Groups of functions to restrict.
	 *
	 * Example: groups => array(
	 *  'lambda' => array(
	 *      'type'      => 'error' | 'warning',
	 *      'message'   => 'Use anonymous functions instead please!',
	 *      'since'     => '4.9.0', //=> the WP version in which the alternative became available.
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
				'since'     => '2.7.0',
				'functions' => array(
					'curl_*',
				),
			),

			'parse_url' => array(
				'type'      => 'warning',
				'message'   => '%s() is discouraged because of inconsistency in the output across PHP versions; use wp_parse_url() instead.',
				'since'     => '4.4.0',
				'functions' => array(
					'parse_url',
				),
			),

			'json_encode' => array(
				'type'      => 'warning',
				'message'   => '%s() is discouraged. Use wp_json_encode() instead.',
				'since'     => '4.1.0',
				'functions' => array(
					'json_encode',
				),
			),

			'file_get_contents' => array(
				'type'      => 'warning',
				'message'   => '%s() is discouraged. Use wp_remote_get() for remote URLs instead.',
				'since'     => '2.7.0',
				'functions' => array(
					'file_get_contents',
				),
			),

			'file_system_read' => array(
				'type'      => 'warning',
				'message'   => 'File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: %s()',
				'since'     => '2.5.0',
				'functions' => array(
					'readfile',
					'fopen',
					'fsockopen',
					'pfsockopen',
					'fclose',
					'fread',
					'fwrite',
					'file_put_contents',
				),
			),

			'strip_tags' => array(
				'type'      => 'warning',
				'message'   => '%s() is discouraged. Use the more comprehensive wp_strip_all_tags() instead.',
				'since'     => '2.9.0',
				'functions' => array(
					'strip_tags',
				),
			),

			'rand_seeding' => array(
				'type'      => 'warning',
				'message'   => '%s() is discouraged. Rand seeding is not necessary when using the wp_rand() function (as you should).',
				'since'     => '2.6.2',
				'functions' => array(
					'srand',
					'mt_srand',
				),
			),

			'rand' => array(
				'type'      => 'warning',
				'message'   => '%s() is discouraged. Use the far less predictable wp_rand() instead.',
				'since'     => '2.6.2',
				'functions' => array(
					'rand',
					'mt_rand',
				),
			),
		);
	}

	/**
	 * Process a matched token.
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_matched_token( $stackPtr, $group_name, $matched_content ) {

		$this->get_wp_version_from_cl();

		/*
		 * Deal with exceptions.
		 */
		switch ( $matched_content ) {
			case 'strip_tags':
				/*
				 * The function `wp_strip_all_tags()` is only a valid alternative when
				 * only the first parameter is passed to `strip_tags()`.
				 */
				if ( $this->get_function_call_parameter_count( $stackPtr ) !== 1 ) {
					return;
				}

				break;

			case 'wp_parse_url':
				/*
				 * Before WP 4.7.0, the function `wp_parse_url()` was only a valid alternative
				 * if no second param was passed to `parse_url()`.
				 *
				 * @see https://developer.wordpress.org/reference/functions/wp_parse_url/#changelog
				 */
				if ( $this->get_function_call_parameter_count( $stackPtr ) !== 1
					&& version_compare( $this->minimum_supported_version, '4.7.0', '<' )
				) {
					return;
				}

				break;

			case 'file_get_contents':
				/*
				 * Using `wp_remote_get()` will only work for remote URLs.
				 * See if we can determine is this function call is for a local file and if so, bow out.
				 */
				$params = $this->get_function_call_parameters( $stackPtr );

				if ( isset( $params[2] ) && 'true' === $params[2]['raw'] ) {
					// Setting `$use_include_path` to `true` is only relevant for local files.
					return;
				}

				if ( isset( $params[1] ) === false ) {
					// If the file to get is not set, this is a non-issue anyway.
					return;
				}

				if ( strpos( $params[1]['raw'], 'http:' ) !== false
					|| strpos( $params[1]['raw'], 'https:' ) !== false
				) {
					// Definitely a URL, throw notice.
					break;
				}

				if ( preg_match( '`\b(?:ABSPATH|WP_(?:CONTENT|PLUGIN)_DIR|WPMU_PLUGIN_DIR|TEMPLATEPATH|STYLESHEETPATH|(?:MU)?PLUGINDIR)\b`', $params[1]['raw'] ) === 1 ) {
					// Using any of the constants matched in this regex is an indicator of a local file.
					return;
				}

				if ( preg_match( '`(?:get_home_path|plugin_dir_path|get_(?:stylesheet|template)_directory|wp_upload_dir)\s*\(`i', $params[1]['raw'] ) === 1 ) {
					// Using any of the functions matched in the regex is an indicator of a local file.
					return;
				}

				unset( $params );

				break;
		}

		if ( ! isset( $this->groups[ $group_name ]['since'] ) ) {
			return parent::process_matched_token( $stackPtr, $group_name, $matched_content );
		}

		// Verify if the alternative is available in the minimum supported WP version.
		if ( version_compare( $this->groups[ $group_name ]['since'], $this->minimum_supported_version, '<=' ) ) {
			return parent::process_matched_token( $stackPtr, $group_name, $matched_content );
		}
	}

}
