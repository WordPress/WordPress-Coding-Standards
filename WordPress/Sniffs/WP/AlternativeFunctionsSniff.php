<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WP;

use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\AbstractFunctionRestrictionsSniff;
use WordPressCS\WordPress\Helpers\MinimumWPVersionTrait;

/**
 * Discourages the use of various functions and suggests (WordPress) alternatives.
 *
 * @since 0.11.0
 * @since 0.13.0 Class name changed: this class is now namespaced.
 * @since 1.0.0  - Takes the minimum supported WP version into account.
 *               - Takes exceptions based on passed parameters into account.
 *
 * @uses \WordPressCS\WordPress\Helpers\MinimumWPVersionTrait::$minimum_wp_version
 */
final class AlternativeFunctionsSniff extends AbstractFunctionRestrictionsSniff {

	use MinimumWPVersionTrait;

	/**
	 * Local input streams which should not be flagged for the file system function checks.
	 *
	 * @link https://www.php.net/wrappers.php
	 *
	 * @since 2.1.0
	 * @since 3.0.0 The visibility was changed from `protected` to `private`.
	 *
	 * @var array
	 */
	private $allowed_local_streams = array(
		'php://input'  => true,
		'php://output' => true,
		'php://stdin'  => true,
		'php://stdout' => true,
		'php://stderr' => true,
	);

	/**
	 * Local input streams which should not be flagged for the file system function checks if
	 * the $filename starts with them.
	 *
	 * @link https://www.php.net/wrappers.php
	 *
	 * @since 2.1.0
	 * @since 3.0.0 The visibility was changed from `protected` to `private`.
	 *
	 * @var array
	 */
	private $allowed_local_stream_partials = array(
		'php://temp/',
		'php://fd/',
	);

	/**
	 * Local input stream constants which should not be flagged for the file system function checks.
	 *
	 * @link https://www.php.net/wrappers.php
	 *
	 * @since 2.1.0
	 * @since 3.0.0 The visibility was changed from `protected` to `private`.
	 *
	 * @var array
	 */
	private $allowed_local_stream_constants = array(
		'STDIN'  => true,
		'STDOUT' => true,
		'STDERR' => true,
	);

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
				'allow'     => array(
					'curl_version' => true,
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

			'unlink' => array(
				'type'      => 'warning',
				'message'   => '%s() is discouraged. Use wp_delete_file() to delete a file.',
				'since'     => '4.2.0',
				'functions' => array(
					'unlink',
				),
			),

			'rename' => array(
				'type'      => 'warning',
				'message'   => '%s() is discouraged. Use WP_Filesystem::move() to rename a file.',
				'since'     => '2.5.0',
				'functions' => array(
					'rename',
				),
			),

			'file_system_operations' => array(
				'type'      => 'warning',
				'message'   => 'File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: %s().',
				'since'     => '2.5.0',
				'functions' => array(
					'chgrp',
					'chmod',
					'chown',
					'fclose',
					'file_put_contents',
					'fopen',
					'fputs',
					'fread',
					'fsockopen',
					'fwrite',
					'is_writable',
					'is_writeable',
					'mkdir',
					'pfsockopen',
					'readfile',
					'rmdir',
					'touch',
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
					'mt_srand',
					'srand',
				),
			),

			'rand' => array(
				'type'      => 'warning',
				'message'   => '%s() is discouraged. Use the far less predictable wp_rand() instead.',
				'since'     => '2.6.2',
				'functions' => array(
					'mt_rand',
					'rand',
				),
			),
		);
	}

	/**
	 * Process a matched token.
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched
	 *                                in lowercase.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_matched_token( $stackPtr, $group_name, $matched_content ) {

		$this->set_minimum_wp_version();

		/*
		 * Deal with exceptions.
		 */
		switch ( $matched_content ) {
			case 'strip_tags':
				/*
				 * The function `wp_strip_all_tags()` is only a valid alternative when
				 * only the first parameter, `$string`, is passed to `strip_tags()`.
				 */
				$has_allowed_tags = PassedParameters::getParameter( $this->phpcsFile, $stackPtr, 2, 'allowed_tags' );
				if ( false !== $has_allowed_tags ) {
					return;
				}

				unset( $has_allowed_tags );
				break;

			case 'parse_url':
				/*
				 * Before WP 4.7.0, the function `wp_parse_url()` was only a valid alternative
				 * if the second param - `$component` - was not passed to `parse_url()`.
				 *
				 * @see https://developer.wordpress.org/reference/functions/wp_parse_url/#changelog
				 */
				$has_component = PassedParameters::getParameter( $this->phpcsFile, $stackPtr, 2, 'component' );
				if ( false !== $has_component
					&& $this->wp_version_compare( $this->minimum_wp_version, '4.7.0', '<' )
				) {
					return;
				}

				unset( $has_component );
				break;

			case 'file_get_contents':
				/*
				 * Using `wp_remote_get()` will only work for remote URLs.
				 * See if we can determine is this function call is for a local file and if so, bow out.
				 */
				$params = PassedParameters::getParameters( $this->phpcsFile, $stackPtr );

				$use_include_path_param = PassedParameters::getParameterFromStack( $params, 2, 'use_include_path' );
				if ( false !== $use_include_path_param && 'true' === $use_include_path_param['clean'] ) {
					// Setting `$use_include_path` to `true` is only relevant for local files.
					return;
				}

				$filename_param = PassedParameters::getParameterFromStack( $params, 1, 'filename' );
				if ( false === $filename_param ) {
					// If the file to get is not set, this is a non-issue anyway.
					return;
				}

				if ( strpos( $filename_param['clean'], 'http:' ) !== false
					|| strpos( $filename_param['clean'], 'https:' ) !== false
				) {
					// Definitely a URL, throw notice.
					break;
				}

				$contains_wp_path_constant = preg_match(
					'`\b(?:ABSPATH|WP_(?:CONTENT|PLUGIN)_DIR|WPMU_PLUGIN_DIR|TEMPLATEPATH|STYLESHEETPATH|(?:MU)?PLUGINDIR)\b`',
					$filename_param['clean']
				);
				if ( 1 === $contains_wp_path_constant ) {
					// Using any of the constants matched in this regex is an indicator of a local file.
					return;
				}

				$contains_wp_path_function_call = preg_match(
					'`(?:get_home_path|plugin_dir_path|get_(?:stylesheet|template)_directory|wp_upload_dir)\s*\(`i',
					$filename_param['clean']
				);
				if ( 1 === $contains_wp_path_function_call ) {
					// Using any of the functions matched in the regex is an indicator of a local file.
					return;
				}

				if ( $this->is_local_data_stream( $filename_param['clean'] ) === true ) {
					// Local data stream.
					return;
				}

				unset( $params, $use_include_path_param, $filename_param, $contains_wp_path_constant, $contains_wp_path_function_call );
				break;

			case 'file_put_contents':
			case 'fopen':
			case 'readfile':
				/*
				 * Allow for handling raw data streams from the request body.
				 *
				 * Note: at this time (December 2022) these three functions use the same parameter name for their
				 * first parameter. If this would change at any point in the future, this code will need to
				 * be made more modular and will need to pass the parameter name based on the function call detected.
				 */
				$filename_param = PassedParameters::getParameter( $this->phpcsFile, $stackPtr, 1, 'filename' );
				if ( false === $filename_param ) {
					// If the file to work with is not set, local data streams don't come into play.
					break;
				}

				if ( $this->is_local_data_stream( $filename_param['clean'] ) === true ) {
					// Local data stream.
					return;
				}

				unset( $filename_param );
				break;
		}

		if ( ! isset( $this->groups[ $group_name ]['since'] ) ) {
			return parent::process_matched_token( $stackPtr, $group_name, $matched_content );
		}

		// Verify if the alternative is available in the minimum supported WP version.
		if ( $this->wp_version_compare( $this->groups[ $group_name ]['since'], $this->minimum_wp_version, '<=' ) ) {
			return parent::process_matched_token( $stackPtr, $group_name, $matched_content );
		}
	}

	/**
	 * Determine based on the "clean" parameter value, whether a file parameter points to
	 * a local data stream.
	 *
	 * @param string $clean_param_value Parameter value without comments.
	 *
	 * @return bool True if this is a local data stream. False otherwise.
	 */
	protected function is_local_data_stream( $clean_param_value ) {

		$stripped = TextStrings::stripQuotes( $clean_param_value );
		if ( isset( $this->allowed_local_streams[ $stripped ] )
			|| isset( $this->allowed_local_stream_constants[ $clean_param_value ] )
		) {
			return true;
		}

		foreach ( $this->allowed_local_stream_partials as $partial ) {
			if ( strpos( $stripped, $partial ) === 0 ) {
				return true;
			}
		}

		return false;
	}
}
