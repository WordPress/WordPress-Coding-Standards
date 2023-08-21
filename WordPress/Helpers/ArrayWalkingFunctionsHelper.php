<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Helpers;

use PHP_CodeSniffer\Files\File;
use PHPCSUtils\Utils\PassedParameters;

/**
 * Helper functions and function lists for checking whether a function applies a callback to an array.
 *
 * @since 3.0.0 The property in this class was previously contained in the
 *              `WordPressCS\WordPress\Sniff` class and has been moved here.
 */
final class ArrayWalkingFunctionsHelper {

	/**
	 * List of array functions which apply a callback to the array.
	 *
	 * These are often used for sanitization/escaping an array variable.
	 *
	 * Note: functions which alter the array by reference are not listed here on purpose.
	 * These cannot easily be used for sanitization as they can't be combined with unslashing.
	 * Similarly, they cannot be used for late escaping as the return value is a boolean, not
	 * the altered array.
	 *
	 * @since 2.1.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - Visibility changed from protected to private and property made static.
	 *                Use the `get_functions()` method for access.
	 *              - The value has changed from an integer to an array containing the integer
	 *                parameter position + its name.
	 *
	 * @var array<string, array>
	 */
	private static $arrayWalkingFunctions = array(
		'array_map' => array(
			'position' => 1,
			'name'     => 'callback',
		),
		'map_deep'  => array(
			'position' => 2,
			'name'     => 'callback',
		),
	);

	/**
	 * Retrieve a list of the supported "array walking" functions.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, bool>
	 */
	public static function get_functions() {
		return \array_fill_keys( \array_keys( self::$arrayWalkingFunctions ), true );
	}

	/**
	 * Check if a particular function is an "array walking" function.
	 *
	 * @since 3.0.0
	 *
	 * @param string $functionName The name of the function to check.
	 *
	 * @return bool
	 */
	public static function is_array_walking_function( $functionName ) {
		return isset( self::$arrayWalkingFunctions[ strtolower( $functionName ) ] );
	}

	/**
	 * Retrieve the parameter information for the callback parameter for an array walking function.
	 *
	 * @since 3.0.0
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
	 * @param int                         $stackPtr  The position of function call name token.
	 *
	 * @return array|false Array with information on the callback parameter.
	 *                     Or `FALSE` if the parameter is not found.
	 *                     See the PHPCSUtils PassedParameters::getParameters() documentation
	 *                     for the format of the returned (single-dimensional) array.
	 */
	public static function get_callback_parameter( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		if ( isset( $tokens[ $stackPtr ] ) === false ) {
			return false;
		}

		$functionName = strtolower( $tokens[ $stackPtr ]['content'] );
		if ( isset( self::$arrayWalkingFunctions[ $functionName ] ) === false ) {
			return false;
		}

		return PassedParameters::getParameter(
			$phpcsFile,
			$stackPtr,
			self::$arrayWalkingFunctions[ $functionName ]['position'],
			self::$arrayWalkingFunctions[ $functionName ]['name']
		);
	}
}
