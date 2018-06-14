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

			// Move to the next pointer where '=>' or ']' is placed.
			$variable = $this->phpcsFile->findNext( T_VARIABLE, $start, $stackPtr );

			// Check if the variable we're looking for was found.
			if ( $this->is_assignment( $variable ) ) {

				// Move to the next pointer where '=>' or ']' is placed.
				$varData = $this->phpcsFile->findNext( array(
					T_CLOSE_SQUARE_BRACKET,
					T_DOUBLE_ARROW,
					T_ARRAY,
					T_OPEN_SHORT_ARRAY,
				), $variable );

				if ( in_array( $this->tokens[ $varData ]['code'], array(
					T_CLOSE_SQUARE_BRACKET,
					T_DOUBLE_ARROW,
				), true ) ) {

					$operator = $varData; // T_DOUBLE_ARROW.

					if ( T_CLOSE_SQUARE_BRACKET === $this->tokens[ $varData ]['code'] ) {
						$operator = $this->phpcsFile->findNext( T_EQUAL, ( $varData ) );
					}

					$keyIdx = $this->phpcsFile->findPrevious( array(
						T_WHITESPACE,
						T_CLOSE_SQUARE_BRACKET,
					), ( $operator - 1 ), null, true );

					if ( ! is_numeric( $this->tokens[ $keyIdx ]['content'] ) ) {
						$valStart    = $this->phpcsFile->findNext( array( T_WHITESPACE ), ( $operator + 1 ), null, true );
						$valEnd      = $this->phpcsFile->findNext( array(
							T_COMMA,
							T_SEMICOLON,
						), ( $valStart + 1 ), null, false, null, true );
						$array_value = $this->phpcsFile->getTokensAsString( $valStart, ( $valEnd - $valStart ) );
					}

				} elseif ( in_array( $this->tokens[ $varData ]['code'], array( T_ARRAY, T_OPEN_SHORT_ARRAY ), true ) ) {
					// Store array data into variable so that we can proceed later.
					$param_items = $this->get_function_call_parameters( $varData );
					//  $this->phpcsFile->addWarning( print_r( $param_items, true ), $parameters[1]['start'], 'SuppressFilters' );
				}
			}
		} elseif ( T_ARRAY === $this->tokens[ $argument_data ]['code'] || T_OPEN_SHORT_ARRAY === $this->tokens[ $argument_data ]['code'] ) {

			// It covers multiple array variable directly passed to function.
			// eg. get_posts( array( 'foo' => 'bar', 'baz' => 'quux' ) ).
			$param_items = $this->get_function_call_parameters( $argument_data );

		} elseif ( isset( Tokens::$stringTokens[ $this->tokens[ $argument_data ]['code'] ] ) ) {
			// It handles when key value comes in '&query=arg' format.
			// eg. get_posts( 'foo=bar&baz=quux' ).
			$query_args = parse_str( $this->strip_quotes( $this->tokens[ $argument_data ]['content'] ), $arrKey );

			if ( isset( $query_args['suppress_filters'] ) ) {
				$array_value = $query_args['suppress_filters'];
			}
		}


		// Process multi dimensional array.
		if ( ! empty( $param_items ) ) {

			// Process the array.
			foreach ( $param_items as $item ) {

				// If 'raw' contains suppress_filters value.
				if ( false !== strpos( $item['raw'], 'suppress_filters' ) ) {

					// Move to the next pointer where '=>' or ']' is placed.
					$variable = $this->phpcsFile->findNext( T_DOUBLE_ARROW, $item['start'], $item['end'] );

					// Type of the suppress_filter's value can be.
					$accept_type = array(
						T_FALSE   => T_FALSE,
						T_TRUE    => T_TRUE,
						T_LNUMBER => T_LNUMBER,
					);

					// Get the value of the suppress_filters array key.
					$key_value = $this->phpcsFile->findNext( Tokens::$emptyTokens, $variable + 1, null, true );

					// Get the value of the suppress_filters array key.
					$key_value1 = $this->phpcsFile->findNext( array_keys($accept_type), $this->tokens[ $key_value ]['nested_parenthesis'] );
					if ( isset( $accept_type[ $this->tokens[ $key_value ]['code'] ] ) ) {
						$array_value = $this->tokens[ $key_value ]['content'];
					}
				}
			}
		}

		// In case numeric value is passed.
		$item_value = ( '0' === $array_value ) ? 'false' : $this->strip_quotes( $array_value );

		if ( 'false' !== $item_value ) {
			$this->phpcsFile->addWarning( '%s() is discouraged in favor of creating a new WP_Query() so that Advanced Post Cache will cache the query, unless you explicitly supply suppress_filters => false.', $parameters[1]['start'], 'SuppressFilters', array( $matched_content ) );
		}
	}

} // End class.
