<?php
/**
 * Enforces WordPress function name format, based upon Squiz code.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   John Godley <john@urbangiraffe.com>
 */

/**
 * Enforces WordPress array format.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   John Godley <john@urbangiraffe.com>
 */
class WordPress_Sniffs_NamingConventions_ValidFunctionNameSniff extends PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff {

	private $_magicMethods = array(
		'construct',
		'destruct',
		'call',
		'callStatic',
		'get',
		'set',
		'isset',
		'unset',
		'sleep',
		'wakeup',
		'toString',
		'set_state',
		'clone',
		'invoke',
		'debugInfo',
	);


	/**
	 * Processes the tokens outside the scope.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being processed.
	 * @param int                  $stackPtr  The position where this token was
	 *                                        found.
	 *
	 * @return void
	 */
	protected function processTokenOutsideScope( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$functionName = $phpcsFile->getDeclarationName( $stackPtr );

		if ( strtolower( $functionName ) !== $functionName ) {
			$suggested = preg_replace( '/([A-Z])/', '_$1', $functionName );
			$suggested = strtolower( $suggested );
			$suggested = str_replace( '__', '_', $suggested );

			$error = "Function name \"$functionName\" is in camel caps format, try '{$suggested}'";
			$phpcsFile->addError( $error, $stackPtr, 'FunctionNameInvalid' );
		}

	} // end processTokenOutsideScope()


	/**
	 * Processes the tokens within the scope.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being processed.
	 * @param int                  $stackPtr  The position where this token was
	 *                                        found.
	 * @param int                  $currScope The position of the current scope.
	 *
	 * @return void
	 */
	protected function processTokenWithinScope( PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope ) {
		$className	= $phpcsFile->getDeclarationName( $currScope );
		$methodName = $phpcsFile->getDeclarationName( $stackPtr );

		// Is this a magic method. IE. is prefixed with "__".
		if ( 0 === strpos( $methodName, '__' ) ) {
			$magicPart = substr( $methodName, 2 );
			if ( false === in_array( $magicPart, $this->_magicMethods, true ) ) {
				 $error = "Method name \"$className::$methodName\" is invalid; only PHP magic methods should be prefixed with a double underscore";
				 $phpcsFile->addError( $error, $stackPtr, 'MethodDoubleUnderscore' );
			}

			return;
		}

		// PHP4 constructors are allowed to break our rules.
		if ( $methodName === $className ) {
			return;
		}

		// PHP4 destructors are allowed to break our rules.
		if ( '_' . $className === $methodName ) {
			return;
		}

		// If this is a child class, it may have to use camelCase.
		if ( $phpcsFile->findExtendedClassName( $currScope ) || $this->findImplementedInterfaceName( $currScope, $phpcsFile ) ) {
			return;
		}

		$methodProps	= $phpcsFile->getMethodProperties( $stackPtr );
		$scope			= $methodProps['scope'];
		$scopeSpecified = $methodProps['scope_specified'];

		if ( 'private' === $methodProps['scope'] ) {
			$isPublic = false;
		} else {
			$isPublic = true;
		}

		// If the scope was specified on the method, then the method must be
		// camel caps and an underscore should be checked for. If it wasn't
		// specified, treat it like a public method and remove the underscore
		// prefix if there is one because we can't determine if it is private or
		// public.
		$testMethodName = $methodName;
		if ( false === $scopeSpecified && '_' === $methodName{0} ) {
			$testMethodName = substr( $methodName, 1 );
		}

		if ( strtolower( $testMethodName ) !== $testMethodName ) {
			$suggested = preg_replace( '/([A-Z])/', '_$1', $methodName );
			$suggested = strtolower( $suggested );
			$suggested = str_replace( '__', '_', $suggested );

			$error = "Function name \"$methodName\" is in camel caps format, try '{$suggested}'";
			$phpcsFile->addError( $error, $stackPtr, 'FunctionNameInvalid' );
		}

	} // end processTokenWithinScope()

	/**
	 * Returns the name of the class that the specified class implements.
	 *
	 * Returns FALSE on error or if there is no implemented class name.
	 *
	 * @param int                  $stackPtr  The stack position of the class.
	 * @param PHP_CodeSniffer_File $phpcsFile The stack position of the class.
	 *
	 * @see PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff::findExtendedClassName()
	 *
	 * @todo This needs to be upstreamed and made part of PHP_CodeSniffer_File.
	 *
	 * @return string
	 */
	public function findImplementedInterfaceName( $stackPtr, $phpcsFile ) {
		$tokens = $phpcsFile->getTokens();

		// Check for the existence of the token.
		if ( ! isset( $tokens[ $stackPtr ] ) ) {
			return false;
		}
		if ( T_CLASS !== $tokens[ $stackPtr ]['code'] ) {
			return false;
		}
		if ( ! isset( $tokens[ $stackPtr ]['scope_closer'] ) ) {
			return false;
		}
		$classOpenerIndex = $tokens[ $stackPtr ]['scope_opener'];
		$extendsIndex     = $phpcsFile->findNext( T_IMPLEMENTS, $stackPtr, $classOpenerIndex );
		if ( false === $extendsIndex ) {
			return false;
		}
		$find = array(
			T_NS_SEPARATOR,
			T_STRING,
			T_WHITESPACE,
		);
		$end  = $phpcsFile->findNext( $find, ( $extendsIndex + 1 ), ( $classOpenerIndex + 1 ), true );
		$name = $phpcsFile->getTokensAsString( ( $extendsIndex + 1 ), ( $end - $extendsIndex - 1 ) );
		$name = trim( $name );
		if ( '' === $name ) {
			return false;
		}
		return $name;
	} // end findExtendedClassName()

} // end class
