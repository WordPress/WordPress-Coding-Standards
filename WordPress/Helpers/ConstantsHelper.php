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
 * Helper utilities for identifying the use of global constants in PHP code.
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
	 * @param int                         $stackPtr  The position of the T_STRING token.
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

	/**
	 * Check if a token range represents a class constant usage (e.g., MyClass::CONSTANT).
	 *
	 * This method detects when a class constant is being *accessed/used*, not when it's being *declared*.
	 * For detecting class constant *declarations* (e.g., `const API_URL = '...'` inside a class),
	 * use `PHPCSUtils\Utils\Scopes::isOOConstant()` instead.
	 *
	 * @since x.y.z
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $startPtr  The start position of the token range.
	 * @param int                         $endPtr    The end position of the token range.
	 * @return bool Whether the token range represents class constant usage.
	 */
	public static function is_use_of_class_constant( File $phpcsFile, $startPtr, $endPtr ) {
		$tokens = $phpcsFile->getTokens();

		if ( ! isset( $tokens[ $startPtr ] ) || ! isset( $tokens[ $endPtr ] ) ) {
			return false;
		}

		$invalid_tokens = array(
			\T_STRING_CONCAT        => true,
			\T_PLUS                 => true,
			\T_MINUS                => true,
			\T_MULTIPLY             => true,
			\T_DIVIDE               => true,
			\T_MODULUS              => true,
			\T_VARIABLE             => true,
			\T_OPEN_PARENTHESIS     => true,
			\T_OPEN_SQUARE_BRACKET  => true,
		);

		// Check if the token range contains any invalid tokens for a pure class constant.
		for ( $i = $startPtr; $i <= $endPtr; $i++ ) {
			if ( isset( $invalid_tokens[ $tokens[ $i ]['code'] ] ) ) {
				return false;
			}
		}

		$double_colon_pos = null;
		for ( $i = $startPtr; $i <= $endPtr; $i++ ) {
			if ( \T_DOUBLE_COLON === $tokens[ $i ]['code'] ) {
				$double_colon_pos = $i;
				break;
			}
		}

		// If no double colon found, it's not a class constant.
		if ( null === $double_colon_pos ) {
			return false;
		}

		// Ensure there's exactly one double colon in the range.
		for ( $i = $double_colon_pos + 1; $i <= $endPtr; $i++ ) {
			if ( \T_DOUBLE_COLON === $tokens[ $i ]['code'] ) {
				// Multiple double colons found.
				return false;
			}
		}

		// Check that there's a valid constant name after the double colon within the range.
		$after_colon = $phpcsFile->findNext( Tokens::$emptyTokens, ( $double_colon_pos + 1 ), ( $endPtr + 1 ), true );
		if ( false === $after_colon || $after_colon > $endPtr || \T_STRING !== $tokens[ $after_colon ]['code'] ) {
			return false;
		}

		// Check that there are no more significant tokens after the constant name within the range.
		$after_constant = $phpcsFile->findNext( Tokens::$emptyTokens, ( $after_colon + 1 ), ( $endPtr + 1 ), true );
		if ( false !== $after_constant && $after_constant <= $endPtr ) {
			// There are more tokens after the constant name within the range.
			return false;
		}

		// Check that there's a valid class reference before the double colon within the range.
		$before_colon = $phpcsFile->findPrevious( Tokens::$emptyTokens, ( $double_colon_pos - 1 ), ( $startPtr - 1 ), true );
		if ( false === $before_colon || $before_colon < $startPtr ) {
			return false;
		}

		// Validate all tokens before the double colon are valid class reference tokens.
		for ( $i = $startPtr; $i < $double_colon_pos; $i++ ) {
			if ( ! isset( Tokens::$emptyTokens[ $tokens[ $i ]['code'] ] ) ) {
				$valid_tokens = array(
					\T_STRING        => true,
					\T_SELF          => true,
					\T_STATIC        => true,
					\T_PARENT        => true,
					\T_NS_SEPARATOR  => true,
				);

				if ( ! isset( $valid_tokens[ $tokens[ $i ]['code'] ] ) ) {
					return false;
				}
			}
		}

		return true;
	}
}
