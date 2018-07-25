<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\NamingConventions;

use PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff as PHPCS_PEAR_ValidFunctionNameSniff;
use PHP_CodeSniffer_File as File;

/**
 * Enforces WordPress function name and method name format, based upon Squiz code.
 *
 * @link    https://make.wordpress.org/core/handbook/coding-standards/php/#naming-conventions
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.1.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 *
 * Last synced with parent class July 2016 up to commit 4fea2e651109e41066a81e22e004d851fb1287f6.
 * @link    https://github.com/squizlabs/PHP_CodeSniffer/blob/master/CodeSniffer/Standards/PEAR/Sniffs/NamingConventions/ValidFunctionNameSniff.php
 *
 * {@internal While this class extends the PEAR parent, it does not actually use the checks
 * contained in the parent. It only uses the properties and the token registration from the parent.}}
 */
class ValidFunctionNameSniff extends PHPCS_PEAR_ValidFunctionNameSniff {

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
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being processed.
	 * @param int                         $stackPtr  The position where this token was
	 *                                               found.
	 *
	 * @return void
	 */
	protected function processTokenOutsideScope( File $phpcsFile, $stackPtr ) {
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
			if ( isset( $this->magicFunctions[ $magicPart ] ) ) {
				return;
			}

			$error     = 'Function name "%s" is invalid; only PHP magic methods should be prefixed with a double underscore';
			$errorData = array( $functionName );
			$phpcsFile->addError( $error, $stackPtr, 'FunctionDoubleUnderscore', $errorData );
		}

		if ( strtolower( $functionName ) !== $functionName ) {
			$error     = 'Function name "%s" is not in snake case format, try "%s"';
			$errorData = array(
				$functionName,
				$this->get_name_suggestion( $functionName ),
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
		$methodName = $phpcsFile->getDeclarationName( $stackPtr );

		if ( ! isset( $methodName ) ) {
			// Ignore closures.
			return;
		}

		$className = $phpcsFile->getDeclarationName( $currScope );

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

		$extended   = $phpcsFile->findExtendedClassName( $currScope );
		$interfaces = $phpcsFile->findImplementedInterfaceNames( $currScope );

		// If this is a child class or interface implementation, it may have to use camelCase or double underscores.
		if ( ! empty( $extended ) || ! empty( $interfaces ) ) {
			return;
		}

		// Is this a magic method ? I.e. is it prefixed with "__" ?
		if ( 0 === strpos( $methodName, '__' ) ) {
			$magicPart = strtolower( substr( $methodName, 2 ) );
			if ( isset( $this->magicMethods[ $magicPart ] ) || isset( $this->methodsDoubleUnderscore[ $magicPart ] ) ) {
				return;
			}

			$error     = 'Method name "%s" is invalid; only PHP magic methods should be prefixed with a double underscore';
			$errorData = array( $className . '::' . $methodName );
			$phpcsFile->addError( $error, $stackPtr, 'MethodDoubleUnderscore', $errorData );
		}

		// Check for all lowercase.
		if ( strtolower( $methodName ) !== $methodName ) {
			$error     = 'Method name "%s" in class %s is not in snake case format, try "%s"';
			$errorData = array(
				$methodName,
				$className,
				$this->get_name_suggestion( $methodName ),
			);
			$phpcsFile->addError( $error, $stackPtr, 'MethodNameInvalid', $errorData );
		}
	}

	/**
	 * Transform the existing function/method name to one which complies with the naming conventions.
	 *
	 * @param string $name The function/method name.
	 * @return string
	 */
	protected function get_name_suggestion( $name ) {
		$suggested = preg_replace( '/([A-Z])/', '_$1', $name );
		$suggested = strtolower( $suggested );
		$suggested = str_replace( '__', '_', $suggested );
		$suggested = trim( $suggested, '_' );
		return $suggested;
	}

}
