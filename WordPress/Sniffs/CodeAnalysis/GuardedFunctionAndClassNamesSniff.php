<?php

namespace WordPressCS\WordPress\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Implements the Gutenberg coding standard for checking if
 * functions and classes are wrapped with !function_exists() and !class_exists().
 * This sniff checks if functions and classes are already defined
 * and recommends wrapping them with !function_exists() and !class_exists()
 * to avoid fatal errors that may occur when merging the feature to the Core.
 *
 * @link https://github.com/WordPress/gutenberg/blob/trunk/lib/README.md#wrap-functions-and-classes-with--function_exists-and--class_exists
 */
class GuardedFunctionAndClassNamesSniff implements Sniff {
	/**
	 * A list of functions to ignore.
	 *
	 * @var integer
	 */
	public $functionsWhiteList = array();

	/**
	 * A list of classes to ignore.
	 *
	 * @var integer
	 */
	public $classesWhiteList = array();

	public function register() {
		$this->onRegisterEvent();

		return array( T_FUNCTION, T_CLASS );
	}

	public function process( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$token  = $tokens[ $stackPtr ];

		if ( 'T_FUNCTION' === $token['type'] ) {
			$this->processFunction( $phpcsFile, $stackPtr );
			return;
		}

		if ( 'T_CLASS' === $token['type'] ) {
			$this->processClass( $phpcsFile, $stackPtr );
		}
	}

	/**
	 * Functions should be wrapped with !function_exists() to avoid fatal errors.
	 * E.g.:
	 * if ( ! function_exists( 'wp_get_navigation' ) ) {
	 *     function wp_get_navigation( $slug ) { ... }
	 * }
	 */
	private function processFunction( File $phpcsFile, $stackPointer ) {
		$tokens        = $phpcsFile->getTokens();
		$functionToken = $phpcsFile->findNext( T_STRING, $stackPointer );

		$wrappingTokensToCheck = array(
			T_CLASS,
			T_INTERFACE,
			T_TRAIT,
		);

		foreach ( $wrappingTokensToCheck as $wrappingTokenToCheck ) {
			if ( false !== $phpcsFile->getCondition( $functionToken, $wrappingTokenToCheck, false ) ) {
				// This sniff only processes functions, not class methods.
				return;
			}
		}

		$name = $tokens[ $functionToken ]['content'];
		foreach ( $this->functionsWhiteList as $functionRegExp ) {
			if ( preg_match( $functionRegExp, $name ) ) {
				// Ignore whitelisted function names.
				return;
			}
		}

		$errorMessage = sprintf( 'The "%s()" function should be guarded against redeclaration.', $name );

		$wrappingIfToken = $phpcsFile->getCondition( $functionToken, T_IF, false );
		if ( false === $wrappingIfToken ) {
			$phpcsFile->addError( $errorMessage, $functionToken, 'FunctionNotGuardedAgainstRedeclaration' );

			return;
		}

		$content = $phpcsFile->getTokensAsString( $wrappingIfToken, $functionToken - $wrappingIfToken );

		$regexp = sprintf( '/if\s*\(\s*!\s*function_exists\s*\(\s*(\'|")%s(\'|")/', preg_quote( $name ) );
		$result = preg_match( $regexp, $content );
		if ( 1 !== $result ) {
			$phpcsFile->addError( $errorMessage, $functionToken, 'FunctionNotGuardedAgainstRedeclaration' );
		}
	}

	/**
	 * Classes should be wrapped with !function_exists() to avoid fatal errors.
	 * E.g.:
	 * if ( class_exists( 'WP_Navigation' ) ) {
	 *     return;
	 * }
	 *
	 * or, alternatively:
	 *
	 * if ( ! class_exists( 'WP_Navigation' ) ) {
	 *    class WP_Navigation { ... }
	 * }
	 */
	private function processClass( File $phpcsFile, $stackPointer ) {
		$tokens     = $phpcsFile->getTokens();
		$classToken = $phpcsFile->findNext( T_STRING, $stackPointer );
		$className  = $tokens[ $classToken ]['content'];

		foreach ( $this->classesWhiteList as $classnameRegExp ) {
			if ( preg_match( $classnameRegExp, $className ) ) {
				// Ignore whitelisted class names.
				return;
			}
		}

		$errorMessage = sprintf( 'The "%s" class should be guarded against redeclaration.', $className );

		$wrappingIfToken = $phpcsFile->getCondition( $classToken, T_IF, false );
		if ( false !== $wrappingIfToken ) {
			$endOfWrappingIfToken = $phpcsFile->findEndOfStatement( $wrappingIfToken );
			$content              = $phpcsFile->getTokensAsString( $wrappingIfToken, $endOfWrappingIfToken - $wrappingIfToken );
			$regexp               = sprintf( '/if\s*\(\s*!\s*class_exists\s*\(\s*(\'|")%s(\'|")/', preg_quote( $className ) );
			$result               = preg_match( $regexp, $content );
			if ( 1 === $result ) {
				return;
			}
		}

		$previousIfToken = $phpcsFile->findPrevious( T_IF, $classToken );
		if ( false === $previousIfToken ) {
			$phpcsFile->addError( $errorMessage, $classToken, 'ClassNotGuardedAgainstRedeclaration' );

			return;
		}

		$endOfPreviousIfToken = $phpcsFile->findEndOfStatement( $previousIfToken );
		$content              = $phpcsFile->getTokensAsString( $previousIfToken, $endOfPreviousIfToken - $previousIfToken );
		$regexp               = sprintf( '/if\s*\(\s*class_exists\s*\(\s*(\'|")%s(\'|")/', preg_quote( $className ) );
		$result               = preg_match( $regexp, $content );

		if ( 1 === $result ) {
			$returnToken = $phpcsFile->findNext( T_RETURN, $previousIfToken, $endOfPreviousIfToken );
			if ( false !== $returnToken ) {
				return;
			}
		}

		$phpcsFile->addError( $errorMessage, $classToken, 'ClassNotGuardedAgainstRedeclaration' );
	}

	private function onRegisterEvent() {
		$this->functionsWhiteList = static::sanitizeArray( $this->functionsWhiteList );
		$this->classesWhiteList   = static::sanitizeArray( $this->classesWhiteList );
	}

	/**
	 * Input data needs to be sanitized.
	 */
	private static function sanitizeArray( $array ) {
		$array = array_map( 'trim', $array );

		return array_filter( $array );
	}
}
