<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

if ( ! class_exists( 'Generic_Sniffs_PHP_ForbiddenFunctionsSniff', true ) ) {
	throw new PHP_CodeSniffer_Exception( 'Class Generic_Sniffs_PHP_ForbiddenFunctionsSniff not found' );
}

/**
 * Disallow the changing of timezone.
 *
 * @link    http://vip.wordpress.com/documentation/use-current_time-not-date_default_timezone_set/
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 */
class WordPress_Sniffs_VIP_TimezoneChangeSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff {

	/**
	 * A list of forbidden functions with their alternatives.
	 *
	 * The value is NULL if no alternative exists. IE, the
	 * function should just not be used.
	 *
	 * @var array(string => string|null)
	 */
	public $forbiddenFunctions = array(
		'date_default_timezone_set' => null,
	);

	/**
	 * Generates the error or warning for this sniff.
	 *
	 * Overloads parent addError method.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the forbidden function
	 *                                        in the token array.
	 * @param string               $function  The name of the forbidden function.
	 * @param string               $pattern   The pattern used for the match.
	 *
	 * @return void
	 */
	protected function addError( $phpcsFile, $stackPtr, $function, $pattern = null ) {
		$error = 'Using date_default_timezone_set() and similar isn\'t allowed, instead use WP internal timezone support.';
		$phpcsFile->addError( $error, $stackPtr, $function );

	}

} // End class.
