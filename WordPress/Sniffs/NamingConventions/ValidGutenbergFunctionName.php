<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\NamingConventions;

use PHPCSUtils\BackCompat\BCTokens;
use PHPCSUtils\Utils\FunctionDeclarations;
use PHPCSUtils\Utils\ObjectDeclarations;
use PHPCSUtils\Utils\Scopes;
use WordPressCS\WordPress\Helpers\DeprecationHelper;
use WordPressCS\WordPress\Sniff;

/**
 * Enforces WordPress function name and method name format, based upon Squiz code.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#naming-conventions
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.1.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   2.0.0  The `get_name_suggestion()` method has been moved to the
 *                 WordPress native `Sniff` base class as `get_snake_case_name_suggestion()`.
 * @since   2.2.0  Will now ignore functions and methods which are marked as @deprecated.
 * @since   3.0.0  This sniff has been refactored and no longer extends the upstream
 *                 PEAR.NamingConventions.ValidFunctionName sniff.
 */
class ValidGutenbergFunctionName extends Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function register() {
		return array( \T_FUNCTION );
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @since 3.0.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {

		$name = FunctionDeclarations::getName( $this->phpcsFile, $stackPtr );
		if ( empty( $name ) === true ) {
			// Live coding or parse error.
			return;
		}

		$ooPtr = Scopes::validDirectScope( $this->phpcsFile, $stackPtr, BCTokens::ooScopeTokens() );
		// Not interested in methods, functions only.
		if ( true === $ooPtr ) {
			return;
		}

		$this->process_function_declaration( $stackPtr, $name );
	}

	/**
	 * Processes a function declaration for a function in the global namespace.
	 *
	 * @since 0.1.0
	 * @since 3.0.0 Renamed from `processTokenOutsideScope()` to `process_function_declaration()`.
	 *              Method signature has been changed as well as this method no longer overloads
	 *              a method from the PEAR sniff which was previously the sniff parent.
	 *
	 * @param int    $stackPtr     The position where this token was found.
	 * @param string $functionName The name of the function.
	 *
	 * @return void
	 */
	protected function process_function_declaration( $stackPtr, $functionName ) {

		// PHP magic functions are exempt from our rules.
		if ( FunctionDeclarations::isMagicFunctionName( $functionName ) === true ) {
			return;
		}

		// Is the function name prefixed with "__" ?
		if ( preg_match( '`^__[^_]`', $functionName ) === 1 ) {
			$error     = 'Function name "%s" is invalid; only PHP magic methods should be prefixed with a double underscore';
			$errorData = array( $functionName );
			$this->phpcsFile->addError( $error, $stackPtr, 'FunctionDoubleUnderscore', $errorData );
		}

		if ( strtolower( $functionName ) !== $functionName ) {
			$error     = 'Function name "%s" is not in snake case format, try "%s"';
			$errorData = array(
				$functionName,
				$this->get_snake_case_name_suggestion( $functionName ),
			);
			$this->phpcsFile->addError( $error, $stackPtr, 'FunctionNameInvalid', $errorData );
		}
	}
}
