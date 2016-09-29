<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Restricts the use of various functions and suggests alternatives.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.11.0
 */
class WordPress_Sniffs_PHP_RestrictedFunctionsSniff extends WordPress_AbstractFunctionRestrictionsSniff {

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
			'eval' => array(
				'type'      => 'error',
				'message'   => '%s() is not allowed.',
				'functions' => array(
					'eval',
				),
			),

			'runtime_configuration' => array(
				'type'      => 'error',
				'message'   => '%s() is prohibited, changing configuration at runtime should not be done.',
				'functions' => array(
					'dl',
					'error_reporting',
					'ini_alter',
					'ini_restore',
					'ini_set',
					'magic_quotes_runtime',
					'set_magic_quotes_runtime',
					'apache_setenv',
					'putenv',
					'set_include_path',
					'restore_include_path',
				),
			),

			'system_calls' => array(
				'type'      => 'error',
				'message'   => 'PHP system calls are often disabled by server admins and should not be used. Found %s().',
				'functions' => array(
					'exec',
					'passthru',
					'proc_open',
					'shell_exec',
					'system',
					'popen',
				),
			),

			'obfuscation' => array(
				'type'      => 'error',
				'message'   => '%s() is not allowed.',
				'functions' => array(
					'base64_decode',
					'base64_encode',
					'convert_uudecode',
					'convert_uuencode',
					'str_rot13',
				),
			),

		);
	} // end getGroups()

} // End class.
