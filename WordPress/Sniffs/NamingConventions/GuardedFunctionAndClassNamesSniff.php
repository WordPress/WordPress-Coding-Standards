<?php

namespace WordPressCS\WordPress\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class GuardedFunctionAndClassNamesSniff implements Sniff {
	public function register() {
		return array( T_FUNCTION );
	}

	public function process( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$token  = $tokens[ $stackPtr ];

		if ( ! in_array( $token['type'], array( 'T_FUNCTION', true ) ) ) {
			return;
		}

		$nameToken    = $phpcsFile->findNext( T_STRING, $stackPtr );
		$name         = $tokens[ $nameToken ]['content'];
		$errorMessage = sprintf( 'The "%s()" function should be guarded against redeclaration.', $name );

		$wrappingIfToken = $phpcsFile->findPrevious( T_IF, $nameToken );
		if ( 0 === $wrappingIfToken ) {
			$phpcsFile->addError( $errorMessage, $nameToken, 'FunctionNotGuardedAgainstRedeclaration' );

			return;
		}

		$content = $phpcsFile->getTokensAsString( $wrappingIfToken, $nameToken - $wrappingIfToken );

		$regexp = sprintf( '/!\s*function_exists\s*\(\s*(\'|")%s(\'|")/', preg_quote( $name ) );
		$result = preg_match( $regexp, $content, $matches );
		if ( 0 === $result ) {
			$phpcsFile->addError( $errorMessage, $nameToken, 'FunctionNotGuardedAgainstRedeclaration' );

			return;
		}

		if ( ! $this->checkIfTokenInsideControlStructure( $phpcsFile, $stackPtr, $wrappingIfToken ) ) {
			$phpcsFile->addError( $errorMessage, $nameToken, 'FunctionNotGuardedAgainstRedeclaration' );
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
			}
		}

		return 0 < $nestingLevel;
	}
}
