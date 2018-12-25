<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WP;

use WordPressCS\WordPress\Sniff;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Warns about overwriting WordPress native global variables.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.4.0  This class now extends the WordPressCS native `Sniff` class.
 * @since   0.12.0 The $wp_globals property has been moved to the `Sniff` class.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   1.0.0  This sniff has been moved from the `Variables` category to the `WP`
 *                 category and renamed from `GlobalVariables` to `GlobalVariablesOverride`.
 * @since   1.1.0  The sniff now also detects variables being overriden in the global namespace.
 *
 * @uses    \WordPressCS\WordPress\Sniff::$custom_test_class_whitelist
 */
class GlobalVariablesOverrideSniff extends Sniff {

	/**
	 * Whether to treat all files as if they were included from
	 * a within function.
	 *
	 * This is mostly useful for projects containing views which are being
	 * included from within a function in another file, like themes.
	 *
	 * Note: enabling this is discouraged as there is no guarantee that
	 * the file will *never* be included from the global scope.
	 *
	 * @since 1.1.0
	 *
	 * @var bool
	 */
	public $treat_files_as_scoped = false;

	/**
	 * Scoped object and function structures to skip over as
	 * variables will have a different scope within those.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	private $skip_over = array(
		\T_FUNCTION => true,
		\T_CLOSURE  => true,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since 0.3.0
	 * @since 1.1.0 Added class tokens for improved test classes skipping.
	 *
	 * @return array
	 */
	public function register() {
		// Add the OO scope tokens to the $skip_over property.
		$this->skip_over += Tokens::$ooScopeTokens;

		$targets = array(
			\T_GLOBAL,
			\T_VARIABLE,
		);

		// Only used to skip over test classes.
		$targets += Tokens::$ooScopeTokens;

		return $targets;
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @since 0.3.0
	 * @since 1.1.0 Split the token specific logic off into separate methods.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {

		$token = $this->tokens[ $stackPtr ];

		// Ignore variable overrides in test classes.
		if ( isset( Tokens::$ooScopeTokens[ $token['code'] ] ) ) {

			if ( true === $this->is_test_class( $stackPtr )
				&& $token['scope_condition'] === $stackPtr
				&& isset( $token['scope_closer'] )
			) {
				// Skip forward to end of test class.
				return $token['scope_closer'];
			}

			// Otherwise ignore the tokens as they were only registered to enable skipping over test classes.
			return;
		}

		/*
		 * Examine variables within a function scope based on a `global` statement in the
		 * function.
		 * Examine variable not within a function scope and access to the `$GLOBALS`
		 * variable based on the variable token.
		 */
		$in_function_scope = $this->phpcsFile->hasCondition( $stackPtr, array( \T_FUNCTION, \T_CLOSURE ) );

		if ( \T_VARIABLE === $token['code']
			&& ( '$GLOBALS' === $token['content']
				|| ( false === $in_function_scope && false === $this->treat_files_as_scoped ) )
		) {
			return $this->process_variable_assignment( $stackPtr );
		} elseif ( \T_GLOBAL === $token['code']
			&& ( true === $in_function_scope || true === $this->treat_files_as_scoped )
		) {
			return $this->process_global_statement( $stackPtr, $in_function_scope );
		}
	}

	/**
	 * Check that defined global variables are prefixed.
	 *
	 * @since 1.1.0 Logic was previously contained in the process_token() method.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	protected function process_variable_assignment( $stackPtr ) {

		if ( $this->has_whitelist_comment( 'override', $stackPtr ) === true ) {
			return;
		}

		$token    = $this->tokens[ $stackPtr ];
		$var_name = substr( $token['content'], 1 ); // Strip the dollar sign.
		$data     = array();

		// Determine the variable name for `$GLOBALS['array_key']`.
		if ( 'GLOBALS' === $var_name ) {
			$bracketPtr = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );

			if ( false === $bracketPtr || \T_OPEN_SQUARE_BRACKET !== $this->tokens[ $bracketPtr ]['code'] || ! isset( $this->tokens[ $bracketPtr ]['bracket_closer'] ) ) {
				return;
			}

			// Retrieve the array key and avoid getting tripped up by some simple obfuscation.
			$var_name = '';
			$start    = ( $bracketPtr + 1 );
			for ( $ptr = $start; $ptr < $this->tokens[ $bracketPtr ]['bracket_closer']; $ptr++ ) {
				/*
				 * If the globals array key contains a variable, constant, function call
				 * or interpolated variable, bow out.
				 */
				if ( \T_VARIABLE === $this->tokens[ $ptr ]['code']
					|| \T_STRING === $this->tokens[ $ptr ]['code']
					|| \T_DOUBLE_QUOTED_STRING === $this->tokens[ $ptr ]['code']
				) {
					return;
				}

				if ( \T_CONSTANT_ENCAPSED_STRING === $this->tokens[ $ptr ]['code'] ) {
					$var_name .= $this->strip_quotes( $this->tokens[ $ptr ]['content'] );
				}
			}

			if ( '' === $var_name ) {
				// Shouldn't happen, but just in case.
				return;
			}

			// Set up the data for the error message.
			$data[] = '$GLOBALS[\'' . $var_name . '\']';
		}

		/*
		 * Is this one of the WP global variables ?
		 */
		if ( isset( $this->wp_globals[ $var_name ] ) === false ) {
			return;
		}

		/*
		 * Check if the variable value is being changed.
		 */
		if ( false === $this->is_assignment( $stackPtr )
			&& false === $this->is_foreach_as( $stackPtr )
		) {
			return;
		}

		/*
		 * Function parameters with the same name as a WP global variable are fine,
		 * including when they are being assigned a default value.
		 */
		if ( isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			foreach ( $this->tokens[ $stackPtr ]['nested_parenthesis'] as $opener => $closer ) {
				if ( isset( $this->tokens[ $opener ]['parenthesis_owner'] )
					&& ( \T_FUNCTION === $this->tokens[ $this->tokens[ $opener ]['parenthesis_owner'] ]['code']
						|| \T_CLOSURE === $this->tokens[ $this->tokens[ $opener ]['parenthesis_owner'] ]['code'] )
				) {
					return;
				}
			}
			unset( $opener, $closer );
		}

		/*
		 * Class property declarations with the same name as WP global variables are fine.
		 */
		if ( true === $this->is_class_property( $stackPtr ) ) {
			return;
		}

		// Still here ? In that case, the WP global variable is being tampered with.
		$this->add_error( $stackPtr, $data );
	}

	/**
	 * Check that global variables imported into a function scope using a global statement
	 * are not being overruled.
	 *
	 * @since 1.1.0 Logic was previously contained in the process_token() method.
	 *
	 * @param int  $stackPtr          The position of the current token in the stack.
	 * @param bool $in_function_scope Whether the global statement is within a scoped function/closure.
	 *
	 * @return void
	 */
	protected function process_global_statement( $stackPtr, $in_function_scope ) {
		/*
		 * Collect the variables to watch for.
		 */
		$search = array();
		$ptr    = ( $stackPtr + 1 );
		while ( isset( $this->tokens[ $ptr ] ) ) {
			$var = $this->tokens[ $ptr ];

			// Halt the loop at end of statement.
			if ( \T_SEMICOLON === $var['code'] ) {
				break;
			}

			if ( \T_VARIABLE === $var['code'] ) {
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

		/*
		 * Search for assignments to the imported global variables within the relevant scope.
		 */
		$start = $ptr;
		if ( true === $in_function_scope ) {
			$function_cond = $this->phpcsFile->getCondition( $stackPtr, \T_FUNCTION );
			$closure_cond  = $this->phpcsFile->getCondition( $stackPtr, \T_CLOSURE );
			$scope_cond    = max( $function_cond, $closure_cond ); // If false, it will evaluate as zero, so this is fine.
			if ( isset( $this->tokens[ $scope_cond ]['scope_closer'] ) === false ) {
				// Live coding or parse error.
				return;
			}
			$end = $this->tokens[ $scope_cond ]['scope_closer'];
		} else {
			// Global statement in the global namespace with file is being treated as scoped.
			$end = ( $this->phpcsFile->numTokens + 1 );
		}

		for ( $ptr = $start; $ptr < $end; $ptr++ ) {

			// Skip over nested functions, classes and the likes.
			if ( isset( $this->skip_over[ $this->tokens[ $ptr ]['code'] ] ) ) {
				if ( ! isset( $this->tokens[ $ptr ]['scope_closer'] ) ) {
					// Live coding or parse error.
					break;
				}

				$ptr = $this->tokens[ $ptr ]['scope_closer'];
				continue;
			}

			if ( \T_VARIABLE !== $this->tokens[ $ptr ]['code'] ) {
				continue;
			}

			if ( \in_array( $this->tokens[ $ptr ]['content'], $search, true ) === false ) {
				// Not one of the variables we're interested in.
				continue;
			}

			// Don't throw false positives for static class properties.
			$previous = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $ptr - 1 ), null, true, null, true );
			if ( false !== $previous && \T_DOUBLE_COLON === $this->tokens[ $previous ]['code'] ) {
				continue;
			}

			if ( true === $this->is_assignment( $ptr ) ) {
				$this->maybe_add_error( $ptr );
				continue;
			}

			// Check if this is a variable assignment within a `foreach()` declaration.
			if ( isset( $this->tokens[ $ptr ]['nested_parenthesis'] ) ) {
				$nested_parenthesis = $this->tokens[ $ptr ]['nested_parenthesis'];
				$close_parenthesis  = end( $nested_parenthesis );
				if ( isset( $this->tokens[ $close_parenthesis ]['parenthesis_owner'] )
					&& \T_FOREACH === $this->tokens[ $this->tokens[ $close_parenthesis ]['parenthesis_owner'] ]['code']
					&& ( false !== $previous
						&& ( \T_DOUBLE_ARROW === $this->tokens[ $previous ]['code']
						|| \T_AS === $this->tokens[ $previous ]['code'] ) )
				) {
					$this->maybe_add_error( $ptr );
				}
			}
		}
	}

	/**
	 * Add the error if there is no whitelist comment present.
	 *
	 * @since 0.11.0
	 * @since 1.1.0  - Visibility changed from public to protected.
	 *               - Check for being in a test class moved to the process_token() method.
	 *
	 * @param int $stackPtr The position of the token to throw the error for.
	 *
	 * @return void
	 */
	protected function maybe_add_error( $stackPtr ) {
		if ( $this->has_whitelist_comment( 'override', $stackPtr ) === false ) {
			$this->add_error( $stackPtr );
		}
	}

	/**
	 * Add the error.
	 *
	 * @since 1.1.0
	 *
	 * @param int   $stackPtr The position of the token to throw the error for.
	 * @param array $data     Optional. Array containing one entry holding the
	 *                        name of the variable being overruled.
	 *                        Defaults to the 'content' of the $stackPtr token.
	 *
	 * @return void
	 */
	protected function add_error( $stackPtr, $data = array() ) {
		if ( empty( $data ) ) {
			$data[] = $this->tokens[ $stackPtr ]['content'];
		}

		$this->phpcsFile->addError(
			'Overriding WordPress globals is prohibited. Found assignment to %s',
			$stackPtr,
			'Prohibited',
			$data
		);
	}

}
