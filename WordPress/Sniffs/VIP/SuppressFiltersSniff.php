<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\VIP;

use WordPress\AbstractFunctionParameterSniff;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Checks for suppress_filters=>false being supplied in get_posts(), wp_get_recent_posts() and get_children().
 *
 * @link    https://vip.wordpress.com/documentation/uncached-functions/
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 */
class SuppressFiltersSniff extends AbstractFunctionParameterSniff {

	/**
	 * Functions this sniff is looking for.
	 *
	 * @since 0.14.0
	 *
	 * @var array
	 */
	protected $target_functions = array(
		'get_posts'           => true,
		'wp_get_recent_posts' => true,
		'get_children'        => true,
	);

	/**
	 * Process the parameters of a matched function.
	 *
	 * @since 0.14.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param array  $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched.
	 * @param array  $parameters      Array with information about the parameters.
	 *
	 * @return void
	 */
	public function process_parameters( $stackPtr, $group_name, $matched_content, $parameters ) {

		if ( false === $this->target_functions[ $matched_content ] ) {
			return;
		}

		// Flag to check if suppress_filters is passed or not.
		$is_used     = false;
		$array_value = '';

		// Retrieve the value parameter's details.
		$argument_data = $this->phpcsFile->findNext( Tokens::$emptyTokens, $parameters[1]['start'], ( $parameters[1]['end'] + 1 ), true );

		// When the list of argument were passed through variable.
		// eg.  $args = array( 'foo' => 'bar' ), $args['foo'] => 'bar'.
		if ( T_VARIABLE === $this->tokens[ $argument_data ]['code'] ) {

			$start = 0;

			// Check if we are in a function.
			$function = $this->phpcsFile->getCondition( $stackPtr, T_FUNCTION );

			// If so, we check only within the function, otherwise the whole file.
			if ( false !== $function ) {
				$start = $this->tokens[ $function ]['scope_opener'];
			} else {
				// Check if we are in a closure.
				$closure = $this->phpcsFile->getCondition( $stackPtr, T_CLOSURE );

				// If so, we check only within the closure.
				if ( false !== $closure ) {
					$start = $this->tokens[ $closure ]['scope_opener'];
				}
			}

			$end = $stackPtr;

			// Walking through the tokens.
			for ( $i = ( $start + 1 ); $i < $end; $i ++ ) {

				if ( $this->tokens[ $i ]['content'] === $this->tokens[ $argument_data ]['content'] ) {

					// Move to the next pointer where '=>' or ']' is placed.
					$varData = $this->phpcsFile->findNext( array( T_CLOSE_SQUARE_BRACKET, T_DOUBLE_ARROW, T_ARRAY, T_OPEN_SHORT_ARRAY ),  $i );

					if ( in_array( $this->tokens[ $varData ]['code'], array( T_CLOSE_SQUARE_BRACKET, T_DOUBLE_ARROW ),true ) ) {

						$operator = $varData; // T_DOUBLE_ARROW.

						if ( T_CLOSE_SQUARE_BRACKET === $this->tokens[ $varData ]['code'] ) {
							$operator = $this->phpcsFile->findNext( T_EQUAL, ( $varData ) );
						}

						$keyIdx = $this->phpcsFile->findPrevious( array( T_WHITESPACE, T_CLOSE_SQUARE_BRACKET ), ( $operator - 1 ),null,true );

						if ( ! is_numeric( $this->tokens[ $keyIdx  ]['content'] ) ) {
							$key            = $this->strip_quotes( $this->tokens[ $keyIdx ]['content'] );
							$valStart       = $this->phpcsFile->findNext( array( T_WHITESPACE ), ( $operator + 1 ), null, true );
							$valEnd         = $this->phpcsFile->findNext( array( T_COMMA, T_SEMICOLON ), ( $valStart + 1 ), null, false, null, true );
							$val            = $this->phpcsFile->getTokensAsString( $valStart, ( $valEnd - $valStart ) );
							$val            = $this->strip_quotes( $val );
							$array_value = $val;
						}
					} elseif ( in_array( $this->tokens[ $varData ]['code'], array( T_ARRAY, T_OPEN_SHORT_ARRAY ), true ) ) {
						// Store array data into variable so that we can proceed later.
						$param_items = $this->get_function_call_parameters( $varData );
					}
				}
			}
		} elseif ( T_ARRAY === $this->tokens[ $argument_data ]['code'] || T_OPEN_SHORT_ARRAY === $this->tokens[ $argument_data ]['code'] ) {

			// It covers multiple array variable directly passed to function.
			// eg. get_posts( array( 'foo' => 'bar', 'baz' => 'quux' ) ).
			$param_items = $this->get_function_call_parameters( $argument_data );

		} elseif ( T_CONSTANT_ENCAPSED_STRING === $this->tokens[ $argument_data ]['code'] || T_DOUBLE_QUOTED_STRING === $this->tokens[ $argument_data ]['code'] ) {

			// It handles when key value comes in '&query=arg' format.
			// eg. get_posts( 'foo=bar&baz=quux' ).
			if ( preg_match_all( '#(?:^|&)([a-z_]+)=([^&]*)#i', $this->strip_quotes( $this->tokens[ $argument_data ]['content'] ), $arrKey ) <= 0 ) {
				return;
			}

			foreach ( $arrKey[1] as $i => $query_key ) {

				if ( 'suppress_filters' === $query_key ) {

					$is_used     = true;
					$array_value = $arrKey[2][ $i ];
				}
			}
		}

		// Process multi dimensional array.
		if ( ! empty( $param_items ) ) {

			// Process the array.
			foreach ( $param_items as $item ) {

				// If 'raw' contains suppress_filters value.
				if ( false !== strpos( $item['raw'], 'suppress_filters' ) ) {

					// Finding the value by token pointer.
					for ( $ptr = $item['start']; $ptr <= $item['end']; $ptr++ ) {

						// '=>' detected.
						if ( T_DOUBLE_ARROW === $this->tokens[ $ptr ]['code'] ) {

							$accept_type = array( T_DOUBLE_QUOTED_STRING, T_CONSTANT_ENCAPSED_STRING, T_FALSE, T_TRUE, T_LNUMBER, T_INT_CAST );

							$key_value = $this->phpcsFile->findNext( Tokens::$emptyTokens, $ptr + 1, null, true );

							if ( in_array( $this->tokens[ $key_value ]['code'], $accept_type,true ) ) {

								$is_used = true;
								$array_value = $this->strip_quotes( $this->tokens[ $key_value ]['content'] );
							}
						}
					}
				}
			}
		}

		// If so, expected value was not passed.
		if ( ! $is_used || ! empty( $array_value ) ) {

			// In case numeric value is passed.
			$item_value = ( '0' === $array_value ) ? 'false' : $array_value;

			if ( 'false' !== $item_value ) {

				$this->phpcsFile->addWarning(
					'%s() is discouraged in favor of creating a new WP_Query() so that Advanced Post Cache will cache the query, unless you explicitly supply suppress_filters => false.',
					$parameters[1]['start'],
					'SuppressFilters',
					array( $matched_content )
				);
			}
		}
	}

} // End class.
