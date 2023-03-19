<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\NamingConventions;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Utils\FunctionDeclarations;
use PHPCSUtils\Utils\NamingConventions;
use PHPCSUtils\Utils\ObjectDeclarations;
use PHPCSUtils\Utils\Scopes;
use WordPressCS\WordPress\Helpers\DeprecationHelper;
use WordPressCS\WordPress\Helpers\SnakeCaseHelper;
use WordPressCS\WordPress\Sniff;

/**
 * Enforces WordPress function name and method name format, based upon Squiz code.
 *
 * @link https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/#naming-conventions
 *
 * @since 0.1.0
 * @since 0.13.0 Class name changed: this class is now namespaced.
 * @since 2.0.0  The `get_name_suggestion()` method has been moved to the
 *               WordPress native `Sniff` base class as `get_snake_case_name_suggestion()`.
 * @since 2.2.0  Will now ignore functions and methods which are marked as @deprecated.
 * @since 3.0.0  This sniff has been refactored and no longer extends the upstream
 *               PEAR.NamingConventions.ValidFunctionName sniff.
 */
final class ValidFunctionNameSniff extends Sniff {

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

		if ( DeprecationHelper::is_function_deprecated( $this->phpcsFile, $stackPtr ) === true ) {
			/*
			 * Deprecated functions don't have to comply with the naming conventions,
			 * otherwise functions deprecated in favour of a function with a compliant
			 * name would still trigger an error.
			 */
			return;
		}

		$name = FunctionDeclarations::getName( $this->phpcsFile, $stackPtr );
		if ( empty( $name ) === true ) {
			// Live coding or parse error.
			return;
		}

		if ( '' === ltrim( $name, '_' ) ) {
			// Ignore special functions, like __().
			return;
		}

		$ooPtr = Scopes::validDirectScope( $this->phpcsFile, $stackPtr, Tokens::$ooScopeTokens );
		if ( false === $ooPtr ) {
			$this->process_function_declaration( $stackPtr, $name );
		} else {
			$this->process_method_declaration( $stackPtr, $name, $ooPtr );
		}
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

		$suggested_name = SnakeCaseHelper::get_suggestion( $functionName );
		if ( $suggested_name !== $functionName ) {
			$error     = 'Function name "%s" is not in snake case format, try "%s"';
			$errorData = array(
				$functionName,
				$suggested_name,
			);
			$this->phpcsFile->addError( $error, $stackPtr, 'FunctionNameInvalid', $errorData );
		}
	}

	/**
	 * Processes a method declaration.
	 *
	 * @since 0.1.0
	 * @since 3.0.0 Renamed from `processTokenWithinScope()` to `process_method_declaration()`.
	 *              Method signature has been changed as well, as this method no longer overloads
	 *              a method from the PEAR sniff which was previously the sniff parent.
	 *
	 * @param int    $stackPtr   The position where this token was found.
	 * @param string $methodName The name of the method.
	 * @param int    $currScope  The position of the current scope.
	 *
	 * @return void
	 */
	protected function process_method_declaration( $stackPtr, $methodName, $currScope ) {

		if ( \T_ANON_CLASS === $this->tokens[ $currScope ]['code'] ) {
			$className = '[Anonymous Class]';
		} else {
			$className = ObjectDeclarations::getName( $this->phpcsFile, $currScope );

			// PHP4 constructors are allowed to break our rules.
			if ( NamingConventions::isEqual( $methodName, $className ) === true ) {
				return;
			}

			// PHP4 destructors are allowed to break our rules.
			if ( NamingConventions::isEqual( $methodName, '_' . $className ) === true ) {
				return;
			}
		}

		// PHP magic methods are exempt from our rules.
		if ( FunctionDeclarations::isMagicMethodName( $methodName ) === true ) {
			return;
		}

		$extended   = ObjectDeclarations::findExtendedClassName( $this->phpcsFile, $currScope );
		$interfaces = ObjectDeclarations::findImplementedInterfaceNames( $this->phpcsFile, $currScope );

		// If this is a child class or interface implementation, it may have to use camelCase or double underscores.
		if ( ! empty( $extended ) || ! empty( $interfaces ) ) {
			return;
		}

		// Is the method name prefixed with "__" ?
		if ( preg_match( '`^__[^_]`', $methodName ) === 1 ) {
			$error     = 'Method name "%s" is invalid; only PHP magic methods should be prefixed with a double underscore';
			$errorData = array( $className . '::' . $methodName );
			$this->phpcsFile->addError( $error, $stackPtr, 'MethodDoubleUnderscore', $errorData );
		}

		// Check for all lowercase.
		$suggested_name = SnakeCaseHelper::get_suggestion( $methodName );
		if ( $suggested_name !== $methodName ) {
			$error     = 'Method name "%s" in class %s is not in snake case format, try "%s"';
			$errorData = array(
				$methodName,
				$className,
				$suggested_name,
			);
			$this->phpcsFile->addError( $error, $stackPtr, 'MethodNameInvalid', $errorData );
		}
	}
}
