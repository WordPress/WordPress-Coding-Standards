<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\PHP;

use WordPressCS\WordPress\AbstractFunctionParameterSniff;
use \PHP_CodeSniffer\Util\Tokens;

/**
 * Checks use of ini_set function with a blacklist for errors.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   x.x.x
 */
class IniSetSniff extends AbstractFunctionParameterSniff {

	/**
	 * Array of functions that must be checked
	 *
	 * @since x.x.x
	 *
	 * @var array Multidimensional array with parameter details.
	 *     $target_functions = array(
	 *         (string) Function name.
	 *     );
	 */
	protected $target_functions = array(
		'ini_set' => array(),
	);

	/**
	 * Array of options that are allowed to be manipulated
	 *
	 * @since x.x.x
	 *
	 * @var array Multidimensional array with parameter details.
	 *     $whitelisted_options = array(
	 *         (string) option name. = array(
	 *             (string[]) 'valid_values' = array()
	 *         )
	 *     );
	 */
	protected $whitelisted_options = array(
		'auto_detect_line_endings' => array(),
		'highlight.bg'             => array(),
		'highlight.comment'        => array(),
		'highlight.default'        => array(),
		'highlight.html'           => array(),
		'highlight.keyword'        => array(),
		'highlight.string'         => array(),
		'short_open_tag'           => array(
			'valid_values' => array( 'true', '1', 'On' ),
		),
	);

	/**
	 * Array of options that are not allowed to be manipulated
	 *
	 * @since x.x.x
	 *
	 * @var array Multidimensional array with parameter details.
	 *     $blacklisted_options = array(
	 *         (string) option name. = array(
	 *             (string[]) 'invalid_values' = array()
	 *             (string) 'message'
	 *         )
	 *     );
	 */
	protected $blacklisted_options = array(
		'max_execution_time' => array(
			'message' => 'Use `set_time_limit()` instead.',
		),
		'short_open_tag' => array(
			'invalid_values' => array( 'false', '0', 'Off' ),
			'message'        => 'Turning off short_open_tag is prohibited as it might possibily break other plugins.',
		),
		'bcmath.scale' => array(
			'message' => 'Use `bcscale()` instead.',
		),
		'display_errors' => array(
			'message' => 'Use `WP_DEBUG_DISPLAY` instead.',
		),
		'error_reporting' => array(
			'message' => 'Use `WP_DEBUG` instead.',
		),
		'filter.default' => array(
			'message' => 'Use the filter flag constants when calling the functions instead (as you will possibly break other plugins if you change this).',
		),
		'filter.default_flags' => array(
			'message' => 'Use the filter flag constants when calling the functions instead (as you will possibly break other plugins if you change this).',
		),
		'iconv.input_encoding' => array(
			'message' => 'PHP < 5.6 only - use `iconv_set_encoding()` instead.',
		),
		'iconv.internal_encoding' => array(
			'message' => 'PHP < 5.6 only - use `iconv_set_encoding()` instead.',
		),
		'iconv.output_encoding' => array(
			'message' => 'PHP < 5.6 only - use `iconv_set_encoding()` instead.',
		),
		'ignore_user_abort' => array(
			'message' => 'Use `ignore_user_abort()` instead.',
		),
		'log_errors' => array(
			'message' => 'Use `WP_DEBUG_LOG` instead.',
		),
		'memory_limit' => array(
			'message' => 'Use `wp_raise_memory_limit()` or hook into the filters in that function.',
		),
	);

	/**
	 * Process the parameter of a matched function. Errors if an option
	 * is found in the blacklist. Warns as 'risky' when the option is not
	 * found in the whitelist.
	 *
	 * @since x.x.x
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param array  $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched.
	 * @param array  $parameters      Array with information about the parameters.
	 *
	 * @return void
	 */
	public function process_parameters( $stackPtr, $group_name, $matched_content, $parameters ) {
		$ini_set_function = $this->tokens[ $stackPtr ];
		$option_name      = $this->strip_quotes( $parameters[1]['raw'] );
		$option_value     = $this->strip_quotes( $parameters[2]['raw'] );
		if ( array_key_exists( $option_name, $this->whitelisted_options ) ) {
			$whitelisted_option = $this->whitelisted_options[ $option_name ];
			if ( ! isset( $whitelisted_option['valid_values'] ) || in_array( $option_value, $whitelisted_option['valid_values'], true ) ) {
				return;
			}
		}

		if ( array_key_exists( $option_name, $this->blacklisted_options ) ) {
			$blacklisted_option = $this->blacklisted_options[ $option_name ];
			if ( ! isset( $blacklisted_option['invalid_values'] ) || in_array( $option_value, $blacklisted_option['invalid_values'], true ) ) {
				$this->phpcsFile->addError(
					'%s(%s, %s) found. %s',
					$stackPtr,
					'Blacklisted',
					array(
						$ini_set_function['content'],
						$parameters[1]['raw'],
						$parameters[2]['raw'],
						$blacklisted_option['message'],
					)
				);
				return;
			}
		}
		$this->phpcsFile->addWarning(
			'%s(%s, %s) found. Changing these configuration values at runtime is rarely necessary.',
			$stackPtr,
			'Risky',
			array(
				$ini_set_function['content'],
				$parameters[1]['raw'],
				$parameters[2]['raw'],
			)
		);
		return;
	}
}
