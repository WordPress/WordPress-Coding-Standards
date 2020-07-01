<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\NamingConventions;

use PHP_CodeSniffer\Standards\PEAR\Sniffs\NamingConventions\ValidFunctionNameSniff as PHPCS_PEAR_ValidFunctionNameSniff;
use PHP_CodeSniffer\Files\File;
use WordPressCS\WordPress\Sniff;
use PHPCSUtils\Utils\FunctionDeclarations;
use PHPCSUtils\Utils\ObjectDeclarations;

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
 *
 * Last synced with parent class December 2018 up to commit ee167761d7756273b8ad0ad68bf3db1f2c211bb8.
 * @link    https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/PEAR/Sniffs/NamingConventions/ValidFunctionNameSniff.php
 *
 * {@internal While this class extends the PEAR parent, it does not actually use the checks
 * contained in the parent. It only uses the properties and the token registration from the parent.}}
 */
class ValidFunctionNameSniff extends PHPCS_PEAR_ValidFunctionNameSniff {

	/**
	 * Processes the tokens outside the scope.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being processed.
	 * @param int                         $stackPtr  The position where this token was
	 *                                               found.
	 *
	 * @return void
	 */
	protected function processTokenOutsideScope( File $phpcsFile, $stackPtr ) {

		if ( Sniff::is_function_deprecated( $phpcsFile, $stackPtr ) === true ) {
			/*
			 * Deprecated functions don't have to comply with the naming conventions,
			 * otherwise functions deprecated in favour of a function with a compliant
			 * name would still trigger an error.
			 */
			return;
		}

		$functionName = FunctionDeclarations::getName( $phpcsFile, $stackPtr );

		if ( ! isset( $functionName ) ) {
			// Ignore closures.
			return;
		}

		if ( '' === ltrim( $functionName, '_' ) ) {
			// Ignore special functions, like __().
			return;
		}

		// PHP magic functions are exempt from our rules.
		if ( FunctionDeclarations::isMagicFunctionName( $functionName ) === true ) {
			return;
		}

		// Is the function name prefixed with "__" ?
		if ( 0 === strpos( $functionName, '__' ) ) {
			$error     = 'Function name "%s" is invalid; only PHP magic methods should be prefixed with a double underscore';
			$errorData = array( $functionName );
			$phpcsFile->addError( $error, $stackPtr, 'FunctionDoubleUnderscore', $errorData );
		}

		if ( strtolower( $functionName ) !== $functionName ) {
			$error     = 'Function name "%s" is not in snake case format, try "%s"';
			$errorData = array(
				$functionName,
				Sniff::get_snake_case_name_suggestion( $functionName ),
			);
			$phpcsFile->addError( $error, $stackPtr, 'FunctionNameInvalid', $errorData );
		}
	}

	/**
	 * Processes the tokens within the scope.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being processed.
	 * @param int                         $stackPtr  The position where this token was
	 *                                               found.
	 * @param int                         $currScope The position of the current scope.
	 *
	 * @return void
	 */
	protected function processTokenWithinScope( File $phpcsFile, $stackPtr, $currScope ) {

		$tokens = $phpcsFile->getTokens();

		// Determine if this is a function which needs to be examined.
		$conditions = $tokens[ $stackPtr ]['conditions'];
		end( $conditions );
		$deepestScope = key( $conditions );
		if ( $deepestScope !== $currScope ) {
			return;
		}

		if ( Sniff::is_function_deprecated( $phpcsFile, $stackPtr ) === true ) {
			/*
			 * Deprecated functions don't have to comply with the naming conventions,
			 * otherwise functions deprecated in favour of a function with a compliant
			 * name would still trigger an error.
			 */
			return;
		}

		$methodName = FunctionDeclarations::getName( $phpcsFile, $stackPtr );

		if ( ! isset( $methodName ) ) {
			// Ignore closures.
			return;
		}

		$className = ObjectDeclarations::getName( $phpcsFile, $currScope );
		if ( isset( $className ) === false ) {
			$className = '[Anonymous Class]';
		}

		$methodNameLc = strtolower( $methodName );
		$classNameLc  = strtolower( $className );

		// Ignore special functions.
		if ( '' === ltrim( $methodName, '_' ) ) {
			return;
		}

		// PHP4 constructors are allowed to break our rules.
		if ( $methodNameLc === $classNameLc ) {
			return;
		}

		// PHP4 destructors are allowed to break our rules.
		if ( '_' . $classNameLc === $methodNameLc ) {
			return;
		}

		// PHP magic methods are exempt from our rules.
		if ( FunctionDeclarations::isMagicMethodName( $methodName ) === true ) {
			return;
		}

		$extended   = ObjectDeclarations::findExtendedClassName( $phpcsFile, $currScope );
		$interfaces = ObjectDeclarations::findImplementedInterfaceNames( $phpcsFile, $currScope );

		// If this is a child class or interface implementation, it may have to use camelCase or double underscores.
		if ( ! empty( $extended ) || ! empty( $interfaces ) ) {
			return;
		}

		// Is the method name prefixed with "__" ?
		if ( 0 === strpos( $methodName, '__' ) ) {
			$error     = 'Method name "%s" is invalid; only PHP magic methods should be prefixed with a double underscore';
			$errorData = array( $className . '::' . $methodName );
			$phpcsFile->addError( $error, $stackPtr, 'MethodDoubleUnderscore', $errorData );
		}

		// Check for all lowercase.
		if ( $methodNameLc !== $methodName ) {
			$error     = 'Method name "%s" in class %s is not in snake case format, try "%s"';
			$errorData = array(
				$methodName,
				$className,
				Sniff::get_snake_case_name_suggestion( $methodName ),
			);
			$phpcsFile->addError( $error, $stackPtr, 'MethodNameInvalid', $errorData );
		}
	}

}
