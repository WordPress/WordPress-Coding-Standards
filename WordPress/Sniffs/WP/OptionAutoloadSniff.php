<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WP;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Utils\Arrays;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\AbstractFunctionParameterSniff;

/**
 * Warns when calls to `add_option()`, `update_option()`, `wp_set_options_autoload()`,
 * `wp_set_option_autoload()`, `wp_set_option_autoload_values()` are missing the `$autoload` param or
 * contain an invalid, internal or deprecated value.
 *
 * @link https://github.com/WordPress/WordPress-Coding-Standards/issues/2473
 * @link https://felix-arntz.me/blog/autoloading-wordpress-options-efficiently-and-responsibly/ For more information on how to use the autoload flag.
 *
 * @since 3.2.0
 */
final class OptionAutoloadSniff extends AbstractFunctionParameterSniff {

	/**
	 * The phrase to use for the metric recorded by this sniff.
	 *
	 * @since 3.2.0
	 *
	 * @var string
	 */
	const METRIC_NAME = 'Value of the `$autoload` parameter in the option functions';

	/**
	 * Valid values for the `$autoload` parameter in the `add_option()` and `update_option()` functions.
	 *
	 * @since 3.2.0
	 *
	 * @var array<string, string>
	 */
	private $valid_values_add_and_update = array(
		'true'  => 'true',
		'false' => 'false',
		'null'  => 'null',
	);

	/**
	 * Valid values for the `$autoload` parameter in the `wp_set_options_autoload()`,
	 * `wp_set_option_autoload()`, and `wp_set_option_autoload_values()` functions.
	 *
	 * @since 3.2.0
	 *
	 * @var array<string, string>
	 */
	private $valid_values_wp_set_option_functions = array(
		'true'  => 'true',
		'false' => 'false',
	);

	/**
	 * Deprecated values for the `$autoload` parameter.
	 *
	 * @since 3.2.0
	 *
	 * @var array<string, true> The key is the name of the deprecated value. The array value is irrelevant.
	 */
	private $deprecated_values = array(
		'yes' => true,
		'no'  => true,
	);

	/**
	 * Internal-use only values for `$autoload` that cannot be fixed automatically by the sniff.
	 *
	 * @since 3.2.0
	 *
	 * @var array<string, true> The key is the non-fixable value. The array value is irrelevant.
	 */
	private $internal_values_non_fixable = array(
		'auto'     => true,
		'auto-on'  => true,
		'auto-off' => true,
	);

	/**
	 * Internal-use only values for `$autoload` that can be fixed automatically by the sniff.
	 *
	 * @since 3.2.0
	 *
	 * @var array<string, true> The key is the fixable value. The array value is irrelevant.
	 */
	private $internal_values_fixable = array(
		'on'  => true,
		'off' => true,
	);

	/**
	 * Replacements for fixable values.
	 *
	 * @since 3.2.0
	 *
	 * @var array<string, string>
	 */
	private $fixable_values = array(
		'yes' => 'true',
		'no'  => 'false',
		'on'  => 'true',
		'off' => 'false',
	);

	/**
	 * Functions for which the `$autoload` parameter is optional.
	 *
	 * @since 3.2.0
	 *
	 * @var array<string, true> The key is the function name. The value is irrelevant.
	 */
	private $autoload_is_optional = array(
		'add_option'    => true,
		'update_option' => true,
	);

	/**
	 * The group name for this group of functions.
	 *
	 * @since 3.2.0
	 *
	 * @var string
	 */
	protected $group_name = 'option_autoload';

	/**
	 * List of functions this sniff should examine.
	 *
	 * @link https://developer.wordpress.org/reference/functions/add_option/
	 * @link https://developer.wordpress.org/reference/functions/update_option/
	 * @link https://developer.wordpress.org/reference/functions/wp_set_option_autoload/
	 * @link https://developer.wordpress.org/reference/functions/wp_set_option_autoload_values/
	 * @link https://developer.wordpress.org/reference/functions/wp_set_options_autoload/
	 *
	 * @since 3.2.0
	 *
	 * @var array<string, array<string, string|int>> Key is the function name, value is an array
	 *                                               containing the name and the position of the
	 *                                               autoload parameter.
	 */
	protected $target_functions = array(
		'add_option'                    => array(
			'param_name' => 'autoload',
			'position'   => 4,
		),
		'update_option'                 => array(
			'param_name' => 'autoload',
			'position'   => 3,
		),
		'wp_set_options_autoload'       => array(
			'param_name' => 'autoload',
			'position'   => 2,
		),
		'wp_set_option_autoload'        => array(
			'param_name' => 'autoload',
			'position'   => 2,
		),
		// Special case as it takes an array of option names and autoload values as the first param.
		'wp_set_option_autoload_values' => array(
			'param_name' => 'options',
			'position'   => 1,
		),
	);

	/**
	 * Process the parameters of a matched function.
	 *
	 * @since 3.2.0
	 *
	 * @param int                                   $stackPtr      The position of the current token in the stack.
	 * @param string                                $group_name    The name of the group which was matched.
	 * @param string                                $function_name The token content (function name) which was matched
	 *                                                             in lowercase.
	 * @param array<int, array<string, int|string>> $parameters    Array with information about the parameters.
	 *
	 * @return void
	 */
	public function process_parameters( $stackPtr, $group_name, $function_name, $parameters ) {
		$function_info = $this->target_functions[ $function_name ];

		$target_param = PassedParameters::getParameterFromStack(
			$parameters,
			$function_info['position'],
			$function_info['param_name']
		);

		if ( ! is_array( $target_param ) ) {
			$this->maybe_display_missing_autoload_warning( $stackPtr, $function_name );
			return;
		}

		if ( 'wp_set_option_autoload_values' === $function_name ) {
			$this->handle_wp_set_option_autoload_values( $target_param );
			return;
		}

		$this->check_autoload_value( $target_param, $function_name );
	}

	/**
	 * Process the function if no parameters were found.
	 *
	 * @since 3.2.0
	 *
	 * @param int    $stackPtr      The position of the current token in the stack.
	 * @param string $group_name    The name of the group which was matched.
	 * @param string $function_name The token content (function name) which was matched
	 *                              in lowercase.
	 *
	 * @return void
	 */
	public function process_no_parameters( $stackPtr, $group_name, $function_name ) {
		$this->maybe_display_missing_autoload_warning( $stackPtr, $function_name );
	}

	/**
	 * Handle the `wp_set_option_autoload_values()` function. It requires special treatment as it
	 * takes an array of option names and autoload values instead of the autoload value of a single
	 * option as a separate parameter.
	 *
	 * @since 3.2.0
	 *
	 * @param array<string, int|string> $options_param Options parameter information.
	 *
	 * @return void
	 */
	private function handle_wp_set_option_autoload_values( array $options_param ) {
		$array_token = $this->phpcsFile->findNext(
			Tokens::$emptyTokens,
			$options_param['start'],
			( $options_param['end'] + 1 ),
			true
		);

		if ( false === $array_token || false === isset( Collections::arrayOpenTokensBC()[ $this->tokens[ $array_token ]['code'] ] ) ) {
			// Bail if the first non-empty token in the parameter is not an array opener as this
			// means it is not possible to determine the option names and autoload values passed to
			// wp_set_option_autoload_values().
			return;
		}

		$options = PassedParameters::getParameters( $this->phpcsFile, $array_token );

		if ( empty( $options ) ) {
			// Bail if the array is empty.
			return;
		}

		foreach ( $options as $array_item ) {
			$double_arrow_pointer = Arrays::getDoubleArrowPtr( $this->phpcsFile, $array_item['start'], $array_item['end'] );

			if ( false === $double_arrow_pointer ) {
				$start = $array_item['start'];
				$clean = $array_item['clean'];
			} else {
				$array_item_parts = explode( '=>', $array_item['clean'] );
				$start            = $double_arrow_pointer + 1;
				$clean            = trim( $array_item_parts[1] );
			}

			$array_value_info = array(
				'start' => $start,
				'end'   => $array_item['end'],
				'clean' => $clean,
			);

			$this->check_autoload_value( $array_value_info, 'wp_set_option_autoload_values' );
		}
	}

	/**
	 * Adds a PHPCS warning when autoload parameter is missing for the functions where
	 * this parameter is optional.
	 *
	 * @since 3.2.0
	 *
	 * @param int    $stackPtr      The position of the current token in the stack.
	 * @param string $function_name The token content (function name) which was matched
	 *                              in lowercase.
	 *
	 * @return void
	 */
	private function maybe_display_missing_autoload_warning( $stackPtr, $function_name ) {
		$this->phpcsFile->recordMetric( $stackPtr, self::METRIC_NAME, 'param missing' );

		// Only display a warning for the functions in which the `$autoload` parameter is optional.
		if ( isset( $this->autoload_is_optional[ $function_name ] ) ) {
			$this->phpcsFile->addWarning(
				'It is recommended to always pass the `$autoload` parameter when using %s() function.',
				$stackPtr,
				'Missing',
				array( $this->tokens[ $stackPtr ]['content'] )
			);
		}
	}

	/**
	 * Check the autoload value for possible violations.
	 *
	 * @since 3.2.0
	 *
	 * @param array<string, int|string> $autoload_info Information about the autoload value (start and end tokens, the
	 *                                                 clean value and potentially the "raw" value - which isn't used).
	 * @param string                    $function_name The token content (function name) which was matched
	 *                                                 in lowercase.
	 *
	 * @return void
	 */
	private function check_autoload_value( array $autoload_info, $function_name ) {
		$param_first_token  = $this->phpcsFile->findNext(
			Tokens::$emptyTokens,
			$autoload_info['start'],
			( $autoload_info['end'] + 1 ),
			true
		);
		$param_second_token = $this->phpcsFile->findNext(
			Tokens::$emptyTokens,
			$param_first_token + 1,
			( $autoload_info['end'] + 1 ),
			true
		);

		$normalized_value = strtolower( $autoload_info['clean'] );

		if ( \T_NS_SEPARATOR === $this->tokens[ $param_first_token ]['code'] && $param_second_token ) {
			$token_content_lowercase = strtolower( $this->tokens[ $param_second_token ]['content'] );

			if ( isset( $this->valid_values_add_and_update[ $token_content_lowercase ] ) ) {
				// Ensure the sniff handles correctly `true`, `false` and `null` when they are
				// namespaced (preceded by a backslash).
				$param_first_token  = $param_second_token;
				$param_second_token = false;
				$normalized_value   = substr( $normalized_value, 1 );
			}
		}

		if ( isset( $this->autoload_is_optional[ $function_name ] ) ) {
			$valid_values = $this->valid_values_add_and_update;
		} else {
			$valid_values = $this->valid_values_wp_set_option_functions;
		}

		if ( isset( $valid_values[ $normalized_value ] ) ) {
			$this->phpcsFile->recordMetric( $param_first_token, self::METRIC_NAME, $normalized_value );
			return;
		}

		if ( in_array( $this->tokens[ $param_first_token ]['code'], array( \T_VARIABLE, \T_STRING ), true )
			&& 'null' !== strtolower( $this->tokens[ $param_first_token ]['content'] )
		) {
			// Bail early if the first non-empty token in the parameter is T_VARIABLE or T_STRING as
			// this means it is not possible to determine the value.
			$this->phpcsFile->recordMetric( $param_first_token, self::METRIC_NAME, 'undetermined value' );
			return;
		}

		if ( $param_second_token
			&& false === isset( Collections::arrayOpenTokensBC()[ $this->tokens[ $param_first_token ]['code'] ] )
		) {
			// Bail early if the parameter has two or more non-empty tokens and the first token is
			// not an array opener as this means an undetermined param value or a value that is not
			// easy to determine.
			$this->phpcsFile->recordMetric( $param_first_token, self::METRIC_NAME, 'undetermined value' );
			return;
		}

		$autoload_value = TextStrings::stripQuotes( $autoload_info['clean'] );

		$known_discouraged_values = array_merge( $this->deprecated_values, $this->internal_values_non_fixable, $this->internal_values_fixable );

		if ( isset( $known_discouraged_values[ $autoload_value ] ) ) {
			$metric_value = $autoload_value;
		} else {
			$metric_value = 'other value';
		}

		$this->phpcsFile->recordMetric( $param_first_token, self::METRIC_NAME, $metric_value );

		if ( isset( $this->deprecated_values[ $autoload_value ] ) ) {
			$message    = 'The use of `%s` as the value of the `$autoload` parameter is deprecated. Use `%s` instead.';
			$error_code = 'Deprecated';
			$data       = array( $autoload_info['clean'], $this->fixable_values[ $autoload_value ] );
		} elseif ( isset( $this->internal_values_fixable[ $autoload_value ] ) ) {
			$message    = 'The use of `%s` as the value of the `$autoload` parameter is discouraged. Use `%s` instead.';
			$error_code = 'InternalUseOnly';
			$data       = array( $autoload_info['clean'], $this->fixable_values[ $autoload_value ] );
		} elseif ( isset( $this->internal_values_non_fixable [ $autoload_value ] ) ) {
			$message    = 'The use of `%s` as the value of the `$autoload` parameter is discouraged.';
			$error_code = 'InternalUseOnly';
			$data       = array( $autoload_info['clean'] );
		} else {
			$valid_values_string = '`' . implode( '`, `', $valid_values ) . '`';
			$valid_values_string = substr_replace( $valid_values_string, ' or', strrpos( $valid_values_string, ',' ), 1 );
			$message             = 'The use of `%s` as the value of the `$autoload` parameter is invalid. Use %s instead.';
			$error_code          = 'InvalidValue';
			$data                = array( $autoload_info['clean'], $valid_values_string );
		}

		if ( isset( $this->fixable_values[ $autoload_value ] ) ) {
			$fix = $this->phpcsFile->addFixableWarning(
				$message,
				$param_first_token,
				$error_code,
				$data
			);

			if ( true === $fix ) {
				$this->phpcsFile->fixer->replaceToken( $param_first_token, $this->fixable_values[ $autoload_value ] );
			}

			return;
		}

		$this->phpcsFile->addWarning(
			$message,
			$param_first_token,
			$error_code,
			$data
		);
	}
}
