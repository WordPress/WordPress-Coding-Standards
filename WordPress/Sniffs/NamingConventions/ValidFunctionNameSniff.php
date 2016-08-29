<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

if ( ! class_exists( 'PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff', true ) ) {
	throw new PHP_CodeSniffer_Exception( 'Class PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff not found' );
}

/**
 * Enforces WordPress function name and method name format, based upon Squiz code.
 *
 * @link    https://make.wordpress.org/core/handbook/coding-standards/php/#naming-conventions
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.1.0
 *
 * Last synced with parent class July 2016 up to commit 4fea2e651109e41066a81e22e004d851fb1287f6.
 * @link    https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/PEAR/Sniffs/NamingConventions/ValidFunctionNameSniff.php
 */
class WordPress_Sniffs_NamingConventions_ValidFunctionNameSniff extends PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff {

	/**
	 * Additional double underscore prefixed methods specific to certain PHP native extensions.
	 *
	 * Currently only handles the SoapClient Extension.
	 *
	 * @link http://php.net/manual/en/class.soapclient.php
	 *
	 * @var array <string method name> => <string class name>
	 */
	private $methodsDoubleUnderscore = array(
		'doRequest'              => 'SoapClient',
		'getFunctions'           => 'SoapClient',
		'getLastRequest'         => 'SoapClient',
		'getLastRequestHeaders'  => 'SoapClient',
		'getLastResponse'        => 'SoapClient',
		'getLastResponseHeaders' => 'SoapClient',
		'getTypes'               => 'SoapClient',
		'setCookie'              => 'SoapClient',
		'setLocation'            => 'SoapClient',
		'setSoapHeaders'         => 'SoapClient',
		'soapCall'               => 'SoapClient',
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

		if ( ! isset( $functionName ) ) {
			// Ignore closures.
			return;
		}

		if ( '' === ltrim( $functionName, '_' ) ) {
			// Ignore special functions.
			return;
		}

		// Is this a magic function ? I.e., it is prefixed with "__" ?
		// Outside class scope this basically just means __autoload().
		if ( 0 === strpos( $functionName, '__' ) ) {
			$magicPart = strtolower( substr( $functionName, 2 ) );
			if ( ! isset( $this->magicFunctions[ $magicPart ] ) ) {
				$error     = 'Function name "%s" is invalid; only PHP magic methods should be prefixed with a double underscore';
				$errorData = array( $functionName );
				$phpcsFile->addError( $error, $stackPtr, 'FunctionDoubleUnderscore', $errorData );
			}

			return;
		}

		if ( strtolower( $functionName ) !== $functionName ) {
			$suggested = preg_replace( '/([A-Z])/', '_$1', $functionName );
			$suggested = strtolower( $suggested );
			$suggested = str_replace( '__', '_', $suggested );
			$suggested = trim( $suggested, '_' );

			$error     = 'Function name "%s" is not in snake case format, try "%s"';
			$errorData = array(
				$functionName,
				$suggested,
			);
			$phpcsFile->addError( $error, $stackPtr, 'FunctionNameInvalid', $errorData );
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
		$methodName = $phpcsFile->getDeclarationName( $stackPtr );

		if ( ! isset( $methodName ) ) {
			// Ignore closures.
			return;
		}

		$className	= $phpcsFile->getDeclarationName( $currScope );

		// Ignore special functions.
		if ( '' === ltrim( $methodName, '_' ) ) {
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

		$extended  = $phpcsFile->findExtendedClassName( $currScope );
		$interface = $this->findImplementedInterfaceName( $currScope, $phpcsFile );

		// If this is a child class or interface implementation, it may have to use camelCase or double underscores.
		if ( $extended || $interface ) {
			return;
		}

		// Is this a magic method ? I.e. is it prefixed with "__" ?
		if ( 0 === strpos( $methodName, '__' ) ) {
			$magicPart = strtolower( substr( $methodName, 2 ) );
			if ( ! isset( $this->magicMethods[ $magicPart ] ) && ! isset( $this->methodsDoubleUnderscore[ $magicPart ] ) ) {
				 $error     = 'Method name "%s" is invalid; only PHP magic methods should be prefixed with a double underscore';
				 $errorData = array( $className . '::' . $methodName );
				 $phpcsFile->addError( $error, $stackPtr, 'MethodDoubleUnderscore', $errorData );
			}

			return;
		}

		// Check for all lowercase.
		if ( strtolower( $methodName ) !== $methodName ) {
			$suggested = preg_replace( '/([A-Z])/', '_$1', $methodName );
			$suggested = strtolower( $suggested );
			$suggested = str_replace( '__', '_', $suggested );
			$suggested = trim( $suggested, '_' );

			$error     = 'Method name "%s" in class %s is not in snake case format, try "%s"';
			$errorData = array(
				$methodName,
				$className,
				$suggested,
			);
			$phpcsFile->addError( $error, $stackPtr, 'MethodNameInvalid', $errorData );
		}

	} // end processTokenWithinScope()

	/**
	 * Returns the name of the interface that the specified class implements.
	 *
	 * Returns FALSE on error or if there is no implemented interface name.
	 *
	 * @since 0.5.0
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

} // End class.
