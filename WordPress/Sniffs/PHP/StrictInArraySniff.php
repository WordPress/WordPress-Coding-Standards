<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\PHP;

use PHPCSUtils\Utils\PassedParameters;
use WordPressCS\WordPress\AbstractFunctionParameterSniff;

/**
 * Flag calling in_array(), array_search() and array_keys() without true as the third parameter.
 *
 * @since 0.9.0
 * @since 0.10.0 - This sniff not only checks for `in_array()`, but also `array_search()`
 *                 and `array_keys()`.
 *               - The sniff no longer needlessly extends the `ArrayAssignmentRestrictionsSniff`
 *                 class which it didn't use.
 * @since 0.11.0 Refactored to extend the new WordPressCS native `AbstractFunctionParameterSniff` class.
 * @since 0.13.0 Class name changed: this class is now namespaced.
 */
final class StrictInArraySniff extends AbstractFunctionParameterSniff {

	/**
	 * The group name for this group of functions.
	 *
	 * @since 0.11.0
	 *
	 * @var string
	 */
	protected $group_name = 'strict';

	/**
	 * List of array functions to which a $strict parameter can be passed.
	 *
	 * The array_keys() function only requires the $strict parameter when the optional
	 * second parameter $filter_value has been set.
	 *
	 * @link https://www.php.net/in-array
	 * @link https://www.php.net/array-search
	 * @link https://www.php.net/array-keys
	 *
	 * @since 0.10.0
	 * @since 0.11.0 Renamed from $array_functions to $target_functions.
	 * @since 3.0.0  The format of the array value has changed from boolean to array.
	 *
	 * @var array<string, array{param_position: int, param_name: string, always_needed: bool}> Key is the function name.
	 */
	protected $target_functions = array(
		'in_array'     => array(
			'param_position' => 3,
			'param_name'     => 'strict',
			'always_needed'  => true,
		),
		'array_search' => array(
			'param_position' => 3,
			'param_name'     => 'strict',
			'always_needed'  => true,
		),
		'array_keys'   => array(
			'param_position' => 3,
			'param_name'     => 'strict',
			'always_needed'  => false,
		),
	);

	/**
	 * Process the parameters of a matched function.
	 *
	 * @since 0.11.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched
	 *                                in lowercase.
	 * @param array  $parameters      Array with information about the parameters.
	 *
	 * @return void
	 */
	public function process_parameters( $stackPtr, $group_name, $matched_content, $parameters ) {
		$param_info = $this->target_functions[ $matched_content ];

		/*
		 * Check if the strict check is actually needed.
		 *
		 * Important! This check only applies to array_keys() in the current form of the sniff
		 * and has been written to be specific to that function.
		 * If more functions would be added with 'always_needed' set to `false`,
		 * this code will need to be adjusted to handle those.
		 */
		if ( false === $param_info['always_needed'] ) {
			$has_filter_value = PassedParameters::getParameterFromStack( $parameters, 2, 'filter_value' );
			if ( false === $has_filter_value ) {
				return;
			}
		}

		$found_parameter = PassedParameters::getParameterFromStack( $parameters, $param_info['param_position'], $param_info['param_name'] );
		if ( false === $found_parameter || 'true' !== strtolower( $found_parameter['clean'] ) ) {
			$errorcode = 'MissingTrueStrict';

			/*
			 * Use a different error code when `false` is found to allow for excluding
			 * the warning as this will be a conscious choice made by the dev.
			 */
			if ( is_array( $found_parameter ) && 'false' === strtolower( $found_parameter['clean'] ) ) {
				$errorcode = 'FoundNonStrictFalse';
			}

			$this->phpcsFile->addWarning(
				'Not using strict comparison for %s; supply true for $%s argument.',
				( isset( $found_parameter['start'] ) ? $found_parameter['start'] : $stackPtr ),
				$errorcode,
				array( $matched_content, $param_info['param_name'] )
			);
		}
	}
}
