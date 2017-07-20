<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\Variables;

use WordPress\Sniff;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Warns about overwriting WordPress native global variables.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.4.0  This class now extends WordPress_Sniff.
 * @since   0.12.0 The $wp_globals property has been moved to the WordPress_Sniff.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 *
 * @uses    \WordPress\Sniff::$custom_test_class_whitelist
 */
class GlobalVariablesSniff extends Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_GLOBAL,
			T_VARIABLE,
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {
		$token = $this->tokens[ $stackPtr ];

		if ( T_VARIABLE === $token['code'] && '$GLOBALS' === $token['content'] ) {
			$bracketPtr = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );

			if ( false === $bracketPtr || T_OPEN_SQUARE_BRACKET !== $this->tokens[ $bracketPtr ]['code'] || ! isset( $this->tokens[ $bracketPtr ]['bracket_closer'] ) ) {
				return;
			}

			// Bow out if the array key contains a variable.
			$has_variable = $this->phpcsFile->findNext( T_VARIABLE, ( $bracketPtr + 1 ), $this->tokens[ $bracketPtr ]['bracket_closer'] );
			if ( false !== $has_variable ) {
				return;
			}

			// Retrieve the array key and avoid getting tripped up by some simple obfuscation.
			$var_name = '';
			$start    = ( $bracketPtr + 1 );
			for ( $ptr = $start; $ptr < $this->tokens[ $bracketPtr ]['bracket_closer']; $ptr++ ) {
				if ( T_CONSTANT_ENCAPSED_STRING === $this->tokens[ $ptr ]['code'] ) {
					$var_name .= $this->strip_quotes( $this->tokens[ $ptr ]['content'] );
				}
			}

			if ( ! isset( $this->wp_globals[ $var_name ] ) ) {
				return;
			}

			if ( true === $this->is_assignment( $this->tokens[ $bracketPtr ]['bracket_closer'] ) ) {
				$this->maybe_add_error( $stackPtr );
			}
		} elseif ( T_GLOBAL === $token['code'] ) {
			$search = array(); // Array of globals to watch for.
			$ptr    = ( $stackPtr + 1 );
			while ( $ptr ) {
				if ( ! isset( $this->tokens[ $ptr ] ) ) {
					break;
				}

				$var = $this->tokens[ $ptr ];

				// Halt the loop at end of statement.
				if ( T_SEMICOLON === $var['code'] ) {
					break;
				}

				if ( T_VARIABLE === $var['code'] ) {
					if ( isset( $this->wp_globals[ substr( $var['content'], 1 ) ] ) ) {
						$search[] = $var['content'];
					}
				}

				$ptr++;
			}
			unset( $var );

			if ( empty( $search ) ) {
				return;
			}

			// Only search from the end of the "global ...;" statement onwards.
			$start        = ( $this->phpcsFile->findEndOfStatement( $stackPtr ) + 1 );
			$end          = $this->phpcsFile->numTokens;
			$global_scope = true;

			// Is the global statement within a function call or closure ?
			// If so, limit the token walking to the function scope.
			$function_token = $this->phpcsFile->getCondition( $stackPtr, T_FUNCTION );
			if ( false === $function_token ) {
				$function_token = $this->phpcsFile->getCondition( $stackPtr, T_CLOSURE );
			}

			if ( false !== $function_token ) {
				if ( ! isset( $this->tokens[ $function_token ]['scope_closer'] ) ) {
					// Live coding, unfinished function.
					return;
				}

				$end          = $this->tokens[ $function_token ]['scope_closer'];
				$global_scope = false;
			}

			// Check for assignments to collected global vars.
			for ( $ptr = $start; $ptr < $end; $ptr++ ) {

				// If the global statement was in the global scope, skip over functions, classes and the likes.
				if ( true === $global_scope && in_array( $this->tokens[ $ptr ]['code'], array( T_FUNCTION, T_CLOSURE, T_CLASS, T_ANON_CLASS, T_INTERFACE, T_TRAIT ), true ) ) {
					if ( ! isset( $this->tokens[ $ptr ]['scope_closer'] ) ) {
						// Live coding, skip the rest of the file.
						return;
					}

					$ptr = $this->tokens[ $ptr ]['scope_closer'];
					continue;
				}

				if ( T_VARIABLE === $this->tokens[ $ptr ]['code']
					&& in_array( $this->tokens[ $ptr ]['content'], $search, true )
				) {
					// Don't throw false positives for static class properties.
					$previous = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $ptr - 1 ), null, true, null, true );
					if ( false !== $previous && T_DOUBLE_COLON === $this->tokens[ $previous ]['code'] ) {
						continue;
					}

					if ( true === $this->is_assignment( $ptr ) ) {
						$this->maybe_add_error( $ptr );
					}
				}
			}
		}

	} // End process_token().

	/**
	 * Add the error if there is no whitelist comment present and the assignment
	 * is not done from within a test method.
	 *
	 * @param int $stackPtr The position of the token to throw the error for.
	 *
	 * @return void
	 */
	public function maybe_add_error( $stackPtr ) {
		if ( ! $this->is_token_in_test_method( $stackPtr ) && ! $this->has_whitelist_comment( 'override', $stackPtr ) ) {
			$this->phpcsFile->addError( 'Overriding WordPress globals is prohibited', $stackPtr, 'OverrideProhibited' );
		}
	}

} // End class.
