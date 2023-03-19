<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Helpers;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Scopes;
use WordPressCS\WordPress\Helpers\ContextHelper;

/**
 * Helper utilities for checking the context in which a token is used.
 *
 * ---------------------------------------------------------------------------------------------
 * This class is only intended for internal use by WordPressCS and is not part of the public API.
 * This also means that it has no promise of backward compatibility. Use at your own risk.
 * ---------------------------------------------------------------------------------------------
 *
 * {@internal The functionality in this class will likely be replaced at some point in
 * the future by functions from PHPCSUtils.}
 *
 * @internal
 *
 * @since 3.0.0 The method in this class was previously contained in the
 *              `WordPressCS\WordPress\Sniff` class and has been moved here.
 */
final class ConstantsHelper {

	/**
	 * Determine whether an arbitrary T_STRING token is the use of a global constant.
	 *
	 * @since 1.0.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The method was changed to be `static`.
	 *              - The `$phpcsFile` parameter was added.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The position of the function call token.
	 *
	 * @return bool
	 */
	public static function is_use_of_global_constant( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		// Check for the existence of the token.
		if ( ! isset( $tokens[ $stackPtr ] ) ) {
			return false;
		}

		// Is this one of the tokens this function handles ?
		if ( \T_STRING !== $tokens[ $stackPtr ]['code'] ) {
			return false;
		}

		$next = $phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );
		if ( false !== $next
			&& ( \T_OPEN_PARENTHESIS === $tokens[ $next ]['code']
				|| \T_DOUBLE_COLON === $tokens[ $next ]['code'] )
		) {
			// Function call or declaration.
			return false;
		}

		// Array of tokens which if found preceding the $stackPtr indicate that a T_STRING is not a global constant.
		$tokens_to_ignore  = array(
			\T_NAMESPACE  => true,
			\T_USE        => true,
			\T_EXTENDS    => true,
			\T_IMPLEMENTS => true,
			\T_NEW        => true,
			\T_FUNCTION   => true,
			\T_INSTANCEOF => true,
			\T_INSTEADOF  => true,
			\T_GOTO       => true,
		);
		$tokens_to_ignore += Tokens::$ooScopeTokens;
		$tokens_to_ignore += Collections::objectOperators();
		$tokens_to_ignore += Tokens::$scopeModifiers;

		$prev = $phpcsFile->findPrevious( Tokens::$emptyTokens, ( $stackPtr - 1 ), null, true );
		if ( isset( $tokens_to_ignore[ $tokens[ $prev ]['code'] ] ) ) {
			// Not the use of a constant.
			return false;
		}

		if ( ContextHelper::is_token_namespaced( $phpcsFile, $stackPtr ) === true ) {
			// Namespaced constant of the same name.
			return false;
		}

		if ( \T_CONST === $tokens[ $prev ]['code']
			&& Scopes::isOOConstant( $phpcsFile, $prev )
		) {
			// Class constant declaration of the same name.
			return false;
		}

		/*
		 * Deal with a number of variations of use statements.
		 */
		for ( $i = $stackPtr; $i > 0; $i-- ) {
			if ( $tokens[ $i ]['line'] !== $tokens[ $stackPtr ]['line'] ) {
				break;
			}
		}

		$firstOnLine = $phpcsFile->findNext( Tokens::$emptyTokens, ( $i + 1 ), null, true );
		if ( false !== $firstOnLine && \T_USE === $tokens[ $firstOnLine ]['code'] ) {
			$nextOnLine = $phpcsFile->findNext( Tokens::$emptyTokens, ( $firstOnLine + 1 ), null, true );
			if ( false !== $nextOnLine ) {
				if ( \T_STRING === $tokens[ $nextOnLine ]['code']
					&& 'const' === $tokens[ $nextOnLine ]['content']
				) {
					$hasNsSep = $phpcsFile->findNext( \T_NS_SEPARATOR, ( $nextOnLine + 1 ), $stackPtr );
					if ( false !== $hasNsSep ) {
						// Namespaced const (group) use statement.
						return false;
					}
				} else {
					// Not a const use statement.
					return false;
				}
			}
		}

		return true;
	}
}
