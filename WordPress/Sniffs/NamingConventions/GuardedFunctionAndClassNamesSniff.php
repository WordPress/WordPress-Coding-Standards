<?php

namespace WordPressCS\WordPress\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class GuardedFunctionAndClassNamesSniff implements Sniff {
	/**
	 * A list of functions to ignore.
	 *
	 * @var integer
	 */
	public $functionsWhiteList = array();

	/**
	 * A list of error codes to ignore.
	 *
	 * @var integer
	 */
	public $classesWhiteList = array();

	public function register() {
		$this->onRegisterHook();

		return array( T_FUNCTION, T_CLASS );
	}

	public function process( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$token  = $tokens[ $stackPtr ];

		if ( 'T_FUNCTION' === $token['type'] ) {
			$this->processFunction( $phpcsFile, $stackPtr );
		}

		if ( 'T_CLASS' === $token['type'] ) {
			$this->processClass( $phpcsFile, $stackPtr );
		}
	}

	private function processFunction( File $phpcsFile, $stackPtr ) {
		$tokens    = $phpcsFile->getTokens();
		$nameToken = $phpcsFile->findNext( T_STRING, $stackPtr );

		$wrappingClassToken = $phpcsFile->getCondition( $nameToken, T_CLASS, false );
		$wrappingInterfaceToken = $phpcsFile->getCondition( $nameToken, T_INTERFACE, false );
		$wrappingTraitToken = $phpcsFile->getCondition( $nameToken, T_TRAIT, false );
		if ( $wrappingClassToken || $wrappingInterfaceToken || $wrappingTraitToken ) {
			// This sniff only processes functions, not class methods.
			return;
		}

		$name      = $tokens[ $nameToken ]['content'];
		foreach ( $this->functionsWhiteList as $functionPrefix ) {
			if ( preg_match( $functionPrefix, $name ) ) {
				return;
			}
		}

		$errorMessage = sprintf( 'The "%s()" function should be guarded against redeclaration.', $name );

		$wrappingIfToken = $phpcsFile->getCondition( $nameToken, T_IF, true );
		if ( false === $wrappingIfToken ) {
			$phpcsFile->addError( $errorMessage, $nameToken, 'FunctionNotGuardedAgainstRedeclaration' );

			return;
		}

		$content = $phpcsFile->getTokensAsString( $wrappingIfToken, $nameToken - $wrappingIfToken );

		$regexp = sprintf( '/if\s*\(\s*!\s*function_exists\s*\(\s*(\'|")%s(\'|")/', preg_quote( $name ) );
		$result = preg_match( $regexp, $content );
		if ( 1 !== $result ) {
			$phpcsFile->addError( $errorMessage, $nameToken, 'FunctionNotGuardedAgainstRedeclaration' );
		}
	}

	private function processClass( File $phpcsFile, $stackPtr ) {
		$tokens    = $phpcsFile->getTokens();
		$nameToken = $phpcsFile->findNext( T_STRING, $stackPtr );
		$name      = $tokens[ $nameToken ]['content'];

		$errorMessage = sprintf( 'The "%s" class should be guarded against redeclaration.', $name );

		$wrappingIfToken = $phpcsFile->findPrevious( T_IF, $nameToken );
		if ( false === $wrappingIfToken ) {
			$phpcsFile->addError( $errorMessage, $nameToken, 'ClassNotGuardedAgainstRedeclaration' );

			return;
		}

		$content = $phpcsFile->getTokensAsString( $wrappingIfToken, $nameToken - $wrappingIfToken );

		$regexp = sprintf( '/if\s*\(\s*class_exists\s*\(\s*(\'|")%s(\'|")/', preg_quote( $name ) );
		$result = preg_match( $regexp, $content );
		if ( 1 === $result ) {
			$returnToken = $phpcsFile->findNext( T_RETURN, $wrappingIfToken );
			if ( false !== $returnToken && $this->checkIfTokenInsideControlStructure( $phpcsFile, $returnToken, $wrappingIfToken ) ) {
				// The class is guarded against redeclaration, so let's bail.
				return;
			}
		}

		$regexp = sprintf( '/if\s*\(\s*!\s*class_exists\s*\(\s*(\'|")%s(\'|")/', preg_quote( $name ) );
		$result = preg_match( $regexp, $content );
		if ( 1 !== $result ) {
			$phpcsFile->addError( $errorMessage, $nameToken, 'ClassNotGuardedAgainstRedeclaration' );
		}
	}

	private function onRegisterHook() {
		$this->functionsWhiteList = static::sanitizeArray($this->functionsWhiteList );
		$this->classesWhiteList   = static::sanitizeArray($this->classesWhiteList );
	}

	private static function sanitizeArray($array) {
		$array = array_map( 'trim', $array );
		return array_filter( $array );
	}
}
