<?php

namespace WordPressCS\WordPress\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class GuardedFunctionAndClassNamesSniff implements Sniff {
	public function register() {
		return array( T_FUNCTION, T_CLASS );
	}

	public function process( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$token  = $tokens[ $stackPtr ];

		if ( 'T_FUNCTION' === $token['type'] ) {
			$this->processFunctions( $phpcsFile, $stackPtr );
		}

		if ( 'T_CLASS' === $token['type'] ) {
			$this->processClasses( $phpcsFile, $stackPtr );
		}
	}

	private function processFunctions( File $phpcsFile, $stackPtr ) {
		$tokens       = $phpcsFile->getTokens();
		$nameToken    = $phpcsFile->findNext( T_STRING, $stackPtr );
		$name         = $tokens[ $nameToken ]['content'];
		$errorMessage = sprintf( 'The "%s()" function should be guarded against redeclaration.', $name );

		$wrappingIfToken = $phpcsFile->findPrevious( T_IF, $nameToken );
		if ( false === $wrappingIfToken ) {
			$phpcsFile->addError( $errorMessage, $nameToken, 'FunctionNotGuardedAgainstRedeclaration' );

			return;
		}

		$content = $phpcsFile->getTokensAsString( $wrappingIfToken, $nameToken - $wrappingIfToken );

		$regexp = sprintf( '/if\s*\(\s*!\s*function_exists\s*\(\s*(\'|")%s(\'|")/', preg_quote( $name ) );
		$result = preg_match( $regexp, $content );
		if ( 1 !== $result ) {
			$phpcsFile->addError( $errorMessage, $nameToken, 'FunctionNotGuardedAgainstRedeclaration' );

			return;
		}

		if ( ! $this->checkIfTokenInsideControlStructure( $phpcsFile, $stackPtr, $wrappingIfToken ) ) {
			$phpcsFile->addError( $errorMessage, $nameToken, 'FunctionNotGuardedAgainstRedeclaration' );
		}
	}

	private function processClasses( File $phpcsFile, $stackPtr ) {
		$tokens       = $phpcsFile->getTokens();
		$nameToken    = $phpcsFile->findNext( T_STRING, $stackPtr );
		$name         = $tokens[ $nameToken ]['content'];
		$errorMessage = sprintf( 'The "%s" class should be guarded against redeclaration.', $name );

		$wrappingIfToken = $phpcsFile->findPrevious( T_IF, $nameToken );
		if ( false === $wrappingIfToken ) {
			$phpcsFile->addError( $errorMessage, $nameToken, 'ClassNotGuardedAgainstRedeclaration' );

			return;
		}

		$content = $phpcsFile->getTokensAsString( $wrappingIfToken, $nameToken - $wrappingIfToken );

		$regexp = sprintf( '/if\s*\(\s*!\s*class_exists\s*\(\s*(\'|")%s(\'|")/', preg_quote( $name ) );
		$result = preg_match( $regexp, $content );
		if ( 1 !== $result ) {
			$phpcsFile->addError( $errorMessage, $nameToken, 'ClassNotGuardedAgainstRedeclaration' );

			return;
		}

		if ( ! $this->checkIfTokenInsideControlStructure( $phpcsFile, $stackPtr, $wrappingIfToken ) ) {
			$phpcsFile->addError( $errorMessage, $nameToken, 'ClassNotGuardedAgainstRedeclaration' );
		}
	}

	private function checkIfTokenInsideControlStructure( File $phpcsFile, $tokenToCheck, $startToken ) {
		$tokens          = $phpcsFile->getTokens();
		$tokensToProcess = array_slice( $tokens, $startToken, $tokenToCheck - $startToken, true );

		$nestingLevel = 0;

		/** @var array $token */
		foreach ( $tokensToProcess as $token ) {
			if ( 'T_OPEN_CURLY_BRACKET' === $token['type'] ) {
				++ $nestingLevel;
			}

			if ( 'T_CLOSE_CURLY_BRACKET' === $token['type'] ) {
				-- $nestingLevel;
				if ( 0 === $nestingLevel ) {
					return false;
				}
			}
		}

		return 0 < $nestingLevel;
	}
}
