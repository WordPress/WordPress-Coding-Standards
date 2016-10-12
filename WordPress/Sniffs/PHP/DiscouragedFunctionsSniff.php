<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Discourages the use of various functions and suggests alternatives.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.1.0
 * @since   0.10.0 The checks for the POSIX functions have been replaced by the stand-alone
 *                 sniff WordPress_Sniffs_PHP_POSIXFunctionsSniff.
 * @since   0.11.0 The checks for the PHP development functions have been replaced by the
 *                 stand-alone sniff WordPress_Sniffs_PHP_DevelopmentFunctionsSniff.
 *                 The check for the `register_globals` has been removed as there is no such
 *                 function. To check for `register_globals` ini directive use
 *                 PHPCompatibility_Sniffs_PHP_DeprecatedIniDirectivesSniff.
 *                 The checks for the WP deprecated functions have been replaced by the
 *                 stand-alone sniff WordPress_Sniffs_WP_DeprecatedFunctionsSniff.
 *                 The checks for the PHP functions which have a WP alternative has been replaced
 *                 by the stand-alone sniff WordPress_Sniffs_WP_AlternativeFunctionsSniff.
 *                 The checks for the WP discouraged functions have been replaced by the
 *                 stand-alone sniff WordPress_Sniffs_WP_DiscouragedFunctionsSniff.
 */
class WordPress_Sniffs_PHP_DiscouragedFunctionsSniff extends WordPress_AbstractFunctionRestrictionsSniff {

	/**
	 * Groups of functions to discourage.
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
			'create_function' => array(
				'type'      => 'warning',
				'message'   => '%s() is discouraged, please use anonymous functions instead.',
				'functions' => array(
					'create_function',
				),
			),

			'serialize' => array(
				'type'      => 'warning',
				'message'   => '%s() Serialized data has <a href=\'https://www.owasp.org/index.php/PHP_Object_Injection\'>known vulnerability problems</a> with Object Injection. JSON is generally a better approach for serializing data.',
				'functions' => array(
					'serialize',
					'unserialize',
				),
			),

			'urlencode' => array(
				'type'      => 'warning',
				'message'   => '%s() should only be used when dealing with legacy applications rawurlencode should now be used instead. See http://php.net/manual/en/function.rawurlencode.php and http://www.faqs.org/rfcs/rfc3986.html',
				'functions' => array(
					'urlencode',
				),
			),

		);
	} // end getGroups()

} // End class.
