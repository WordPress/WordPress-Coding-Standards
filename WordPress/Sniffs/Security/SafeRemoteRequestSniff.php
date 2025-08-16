<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\Security;

use WordPressCS\WordPress\AbstractFunctionRestrictionsSniff;

/**
 * Encourages use of wp_safe_remote_* functions to avoid potential security issues.
 *
 * The wp_remote_* functions do not validate URLs, which can lead to potential
 * malicious requests if the URL is user-controlled. The wp_safe_remote_* functions
 * validate URLs to avoid redirection and request forgery attacks.
 *
 * @since x.y.z
 *
 * @link https://github.com/WordPress/WordPress-Coding-Standards/issues/1288
 */
final class SafeRemoteRequestSniff extends AbstractFunctionRestrictionsSniff {

	/**
	 * Groups of functions to restrict.
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(
			'unsafe_remote_requests' => array(
				'type'      => 'warning',
				'message'   => '%s() found. Using wp_safe_remote_%s() instead can help avoid redirection and request forgery attacks when dealing with user-controlled URLs.',
				'functions' => array(
					'wp_remote_get',
					'wp_remote_post',
					'wp_remote_head',
					'wp_remote_request',
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
	 * @return void
	 */
	public function process_matched_token( $stackPtr, $group_name, $matched_content ) {
		// Extract the function type (get, post, head, request) from the matched function name.
		$function_type = str_replace( 'wp_remote_', '', $matched_content );

		// Build the custom message with the specific safe alternative.
		$message = sprintf(
			$this->groups[ $group_name ]['message'],
			$matched_content,
			$function_type
		);

		$this->phpcsFile->addWarning( $message, $stackPtr, 'Found' );
	}
}
