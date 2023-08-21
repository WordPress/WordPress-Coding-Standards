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
use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\Tokens\Collections;

/**
 * Helper utilities for sniffs which examine WPDB method calls.
 *
 * @since 3.0.0 The method in this trait was previously contained in the
 *              `WordPressCS\WordPress\Sniff` class and has been moved here.
 */
trait WPDBTrait {

	/**
	 * Checks whether this is a call to a $wpdb method that we want to sniff.
	 *
	 * If available in the class using this trait, the $methodPtr, $i and $end properties
	 * are automatically set to correspond to the start and end of the method call.
	 * The $i property is also set if this is not a method call but rather the
	 * use of a $wpdb property.
	 *
	 * @since 0.8.0
	 * @since 0.9.0  The return value is now always boolean. The $end and $i member
	 *               vars are automatically updated.
	 * @since 0.14.0 Moved this method from the `PreparedSQL` sniff to the base WP sniff.
	 * @since 3.0.0  - Moved from the Sniff class to this dedicated Trait.
	 *               - The `$phpcsFile` parameter was added.
	 *
	 * {@internal This method should be refactored to not exhibit "magic" behaviour
	 *            for properties in the sniff class(es) using it.}}
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile      The file being scanned.
	 * @param int                         $stackPtr       The index of the $wpdb variable.
	 * @param array                       $target_methods Array of methods. Key(s) should be method name
	 *                                                    in lowercase.
	 *
	 * @return bool Whether this is a $wpdb method call.
	 */
	final protected function is_wpdb_method_call( File $phpcsFile, $stackPtr, array $target_methods ) {
		$tokens = $phpcsFile->getTokens();
		if ( isset( $tokens[ $stackPtr ] ) === false ) {
			return false;
		}

		// Check for wpdb.
		if ( ( \T_VARIABLE === $tokens[ $stackPtr ]['code'] && '$wpdb' !== $tokens[ $stackPtr ]['content'] )
			|| ( \T_STRING === $tokens[ $stackPtr ]['code'] && 'wpdb' !== strtolower( $tokens[ $stackPtr ]['content'] ) )
		) {
			return false;
		}

		// Check that this is a method call.
		$is_object_call = $phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );
		if ( false === $is_object_call
			|| isset( Collections::objectOperators()[ $tokens[ $is_object_call ]['code'] ] ) === false
		) {
			return false;
		}

		$methodPtr = $phpcsFile->findNext( Tokens::$emptyTokens, ( $is_object_call + 1 ), null, true, null, true );
		if ( false === $methodPtr ) {
			return false;
		}

		if ( \T_STRING === $tokens[ $methodPtr ]['code'] && property_exists( $this, 'methodPtr' ) ) {
			$this->methodPtr = $methodPtr;
		}

		// Find the opening parenthesis.
		$opening_paren = $phpcsFile->findNext( Tokens::$emptyTokens, ( $methodPtr + 1 ), null, true, null, true );

		if ( false === $opening_paren ) {
			return false;
		}

		if ( property_exists( $this, 'i' ) ) {
			$this->i = $opening_paren;
		}

		if ( \T_OPEN_PARENTHESIS !== $tokens[ $opening_paren ]['code']
			|| ! isset( $tokens[ $opening_paren ]['parenthesis_closer'] )
		) {
			return false;
		}

		// Check that this is one of the methods that we are interested in.
		if ( ! isset( $target_methods[ strtolower( $tokens[ $methodPtr ]['content'] ) ] ) ) {
			return false;
		}

		// Find the end of the first parameter.
		$end = BCFile::findEndOfStatement( $phpcsFile, $opening_paren + 1 );

		if ( \T_COMMA !== $tokens[ $end ]['code'] ) {
			++$end;
		}

		if ( property_exists( $this, 'end' ) ) {
			$this->end = $end;
		}

		return true;
	}
}
