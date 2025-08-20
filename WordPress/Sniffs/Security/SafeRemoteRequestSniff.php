<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\Security;

use PHPCSUtils\Utils\PassedParameters;
use WordPressCS\WordPress\AbstractFunctionRestrictionsSniff;
use WordPressCS\WordPress\Helpers\ConstantsHelper;
use WordPressCS\WordPress\Helpers\StringLiteralHelper;

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
				'message'   => 'Found: %s(). Using wp_safe_remote_%s() instead can help avoid redirection and request forgery attacks when dealing with user-controlled URLs.',
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
		// Get the first parameter (`$url`) of the function.
		$url_param = PassedParameters::getParameter( $this->phpcsFile, $stackPtr, 1 );

		// If we can't find the parameter, trigger the warning (defensive).
		if ( false === $url_param ) {
			return $this->trigger_warning( $stackPtr, $group_name, $matched_content );
		}

		// Check if this is an expression containing only safe elements.
		if ( $this->is_safe_expression( $url_param ) ) {
			return;
		}

		// For all other cases, trigger the warning.
		return $this->trigger_warning( $stackPtr, $group_name, $matched_content );
	}

	/**
	 * Check if this is a safe expression containing only constant and literal values.
	 *
	 * @param array $url_param The URL parameter information.
	 * @return bool Whether the expression is safe.
	 */
	private function is_safe_expression( array $url_param ): bool {
		// If the URL is a string literal, it's not user-controlled so we don't trigger a warning.
		if ( StringLiteralHelper::is_string_literal( $url_param, $this->phpcsFile->getTokens() ) ) {
			return true;
		}

		// If the URL is a class constant, it's not user-controlled so we don't trigger a warning.
		if ( ConstantsHelper::is_use_of_class_constant( $this->phpcsFile, $url_param['start'], $url_param['end'] ) ) {
			return true;
		}

		$tokens = $this->phpcsFile->getTokens();

		for ( $i = $url_param['start']; $i <= $url_param['end']; $i++ ) {
			$token = $tokens[ $i ];

			// Safe tokens we can skip.
			if ( \T_WHITESPACE === $token['code'] || \T_STRING_CONCAT === $token['code'] ) {
				continue;
			}

			// Safe: string literals.
			if ( \in_array( $token['code'], array( \T_CONSTANT_ENCAPSED_STRING, \T_DOUBLE_QUOTED_STRING ), true ) ) {
				continue;
			}

			// Potentially safe: class constants and namespaces, but check for function calls.
			if ( \in_array(
				$token['code'],
				array(
					\T_STRING,
					\T_DOUBLE_COLON,
					\T_SELF,
					\T_STATIC,
					\T_NS_SEPARATOR,
					\T_PARENT
				),
				true
			) ) {
				// Unsafe: if followed by opening parenthesis, it's a function call.
				$next_meaningful = $this->phpcsFile->findNext( array( \T_WHITESPACE ), $i + 1, null, true );
				if ( false !== $next_meaningful && \T_OPEN_PARENTHESIS === $tokens[ $next_meaningful ]['code'] ) {
					return false;
				}
				continue;
			}

			// Unsafe: any other token type (variables, function calls, etc.).
			// Fail safe by default - unknown/unhandled tokens are treated as unsafe.
			return false;
		}

		// Safe: if we've examined all tokens and found only safe ones.
		return true;
	}

	/**
	 * Trigger the warning for unsafe remote request.
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched.
	 * @return void
	 */
	private function trigger_warning( $stackPtr, $group_name, $matched_content ) {
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
