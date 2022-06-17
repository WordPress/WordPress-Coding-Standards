<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\PHP;

use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\AbstractFunctionParameterSniff;

/**
 * Detect use of the `ini_set()` function.
 *
 * - Won't throw notices for "safe" ini directives as listed in the safe-list.
 * - Throws errors for ini directives listed in the disallow-list.
 * - A warning will be thrown in all other cases.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since 2.1.0
 */
class IniSetSniff extends AbstractFunctionParameterSniff {

	/**
	 * Array of functions that must be checked.
	 *
	 * @since 2.1.0
	 *
	 * @var array Multidimensional array with parameter details.
	 *     $target_functions = array(
	 *         (string) Function name.
	 *     );
	 */
	protected $target_functions = array(
		'ini_set'   => true,
		'ini_alter' => true,
	);

	/**
	 * Array of PHP configuration options that are allowed to be manipulated.
	 *
	 * @since 2.1.0
	 * @since 3.0.0 Renamed from `$whitelisted_options` to `$safe_options`.
	 *
	 * @var array Multidimensional array with parameter details.
	 *     $safe_options = array(
	 *         (string) option name. = array(
	 *             (string[]) 'valid_values' = array()
	 *         )
	 *     );
	 */
	protected $safe_options = array(
		'auto_detect_line_endings' => array(),
		'highlight.bg'             => array(),
		'highlight.comment'        => array(),
		'highlight.default'        => array(),
		'highlight.html'           => array(),
		'highlight.keyword'        => array(),
		'highlight.string'         => array(),
		'short_open_tag'           => array(
			'valid_values' => array( 'true', '1', 'on' ),
		),
	);

	/**
	 * Array of PHP configuration options that are not allowed to be manipulated.
	 *
	 * @since 2.1.0
	 * @since 3.0.0 Renamed from `$blacklisted_options` to `$disallowed_options`.
	 *
	 * @var array Multidimensional array with parameter details.
	 *     $disallowed_options = array(
	 *         (string) option name. = array(
	 *             (string[]) 'invalid_values' = array()
	 *             (string) 'message'
	 *         )
	 *     );
	 */
	protected $disallowed_options = array(
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
			'message' => 'Changing the option value can break other plugins. Use the filter flag constants when calling the Filter functions instead.',
		),
		'filter.default_flags' => array(
			'message' => 'Changing the option value can break other plugins. Use the filter flag constants when calling the Filter functions instead.',
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
		'max_execution_time' => array(
			'message' => 'Use `set_time_limit()` instead.',
		),
		'memory_limit' => array(
			'message' => 'Use `wp_raise_memory_limit()` or hook into the filters in that function.',
		),
		'short_open_tag' => array(
			'invalid_values' => array( 'false', '0', 'off' ),
			'message'        => 'Turning off short_open_tag is prohibited as it can break other plugins.',
		),
	);

	/**
	 * Process the parameter of a matched function.
	 *
	 * Errors if an option is found in the disallow-list. Warns as
	 * 'risky' when the option is not found in the safe-list.
	 *
	 * @since 2.1.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched.
	 * @param array  $parameters      Array with information about the parameters.
	 *
	 * @return void
	 */
	public function process_parameters( $stackPtr, $group_name, $matched_content, $parameters ) {
		$option_name  = TextStrings::stripQuotes( $parameters[1]['raw'] );
		$option_value = TextStrings::stripQuotes( $parameters[2]['raw'] );
		if ( isset( $this->safe_options[ $option_name ] ) ) {
			$safe_option = $this->safe_options[ $option_name ];
			if ( ! isset( $safe_option['valid_values'] ) || in_array( strtolower( $option_value ), $safe_option['valid_values'], true ) ) {
				return;
			}
		}

		if ( isset( $this->disallowed_options[ $option_name ] ) ) {
			$disallowed_option = $this->disallowed_options[ $option_name ];
			if ( ! isset( $disallowed_option['invalid_values'] ) || in_array( strtolower( $option_value ), $disallowed_option['invalid_values'], true ) ) {
				$this->phpcsFile->addError(
					'%s(%s, %s) found. %s',
					$stackPtr,
					$this->string_to_errorcode( $option_name . '_Disallowed' ),
					array(
						$matched_content,
						$parameters[1]['raw'],
						$parameters[2]['raw'],
						$disallowed_option['message'],
					)
				);
				return;
			}
		}

		$this->phpcsFile->addWarning(
			'%s(%s, %s) found. Changing configuration values at runtime is strongly discouraged.',
			$stackPtr,
			'Risky',
			array(
				$matched_content,
				$parameters[1]['raw'],
				$parameters[2]['raw'],
			)
		);
	}
}
