<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Suppress Filters need to be 'true' when getting posts using get_posts.
 *
 * @link    https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 */
class WordPress_Sniffs_VIP_SuppressFiltersSniff extends WordPress_AbstractFunctionParameterSniff {

	/**
	 * Groups of variables to restrict.
	 * This should be overridden in extending classes.
	 *
	 * @since 0.3.0
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
	 * @since 0.3.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 * @param array $group_name The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched.
	 * @param array $parameters Array with information about the parameters.
	 *
	 * @return void
	 */
	public function process_parameters( $stackPtr, $group_name, $matched_content, $parameters ) {

		if ( false === $this->target_functions[ $matched_content ] ) {
			return;
		}

		// Flag to check if suppress_filters is passed or not.
		$isUsed     = false;
		$arrayValue = '';

		// Retrieve the value parameter's details.
		$argumentData = $this->phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, $parameters[1]['start'], ( $parameters[1]['end'] + 1 ), true );

		/**
		 * When the list of argument were passed through variable.
		 *
		 * eg.  $args = array( 'foo' => 'bar' );
		 *      $args['foo'] => 'bar';
		 */
		if ( T_VARIABLE === $this->tokens[ $argumentData ]['code'] ) {

			// Get the function in which the code placed.
			$scope_function = $this->phpcsFile->getCondition( $stackPtr, T_FUNCTION );

			if ( false === $scope_function ) {
				$scope_function = $this->phpcsFile->getCondition( $stackPtr, T_CLOSURE );
			}

			if ( false !== $scope_function ) {

				$start = $this->tokens[ $scope_function ]['scope_opener'];
				$end   = $this->tokens[ $scope_function ]['scope_closer'];

				// Walking through the tokens.
				for ( $i = ( $start + 1 ); $i < $end; $i ++ ) {

					if ( $this->tokens[ $i ]['content'] === $this->tokens[ $argumentData ]['content'] ) {

						// Move to the next pointer where '=>' or ']' is placed;
						$varData = $this->phpcsFile->findNext( array( T_CLOSE_SQUARE_BRACKET, T_DOUBLE_ARROW, T_ARRAY, T_OPEN_SHORT_ARRAY ),  $i  );

						if ( in_array(  $this->tokens[ $varData ]['code'], array( T_CLOSE_SQUARE_BRACKET, T_DOUBLE_ARROW ), true ) ) {

							$operator = $varData; // T_DOUBLE_ARROW.

							if ( T_CLOSE_SQUARE_BRACKET ===  $this->tokens[ $varData ]['code'] ) {

								$operator = $this->phpcsFile->findNext( T_EQUAL, ( $varData ) );
							}

							$keyIdx = $this->phpcsFile->findPrevious( array( T_WHITESPACE, T_CLOSE_SQUARE_BRACKET ), ( $operator -1 ), null, true );

							if ( ! is_numeric( $this->tokens[ $keyIdx  ]['content'] ) ) {

								$key            = $this->strip_quotes( $this->tokens[ $keyIdx ]['content'] );
								$valStart       = $this->phpcsFile->findNext( array( T_WHITESPACE ), ( $operator + 1 ), null, true );
								$valEnd         = $this->phpcsFile->findNext( array( T_COMMA, T_SEMICOLON ), ( $valStart + 1 ), null, false, null, true );
								$val            = $this->phpcsFile->getTokensAsString( $valStart, ( $valEnd - $valStart ) );
								$val            = $this->strip_quotes( $val );
								$arrayValue = $val;

							}

						} else if ( in_array(  $this->tokens[ $varData ]['code'], array( T_ARRAY, T_OPEN_SHORT_ARRAY ), true ) ) {

							// Store array data into variable so that we can proceed later.
							$paramItems = $this->get_function_call_parameters( $varData );

						}

					}
				}
			}

		} else if ( T_ARRAY === $this->tokens[ $argumentData ]['code'] || T_OPEN_SHORT_ARRAY === $this->tokens[ $argumentData ]['code'] ) {

			/**
			 * It covers multiple array variable directly passed to function.
			 *
			 * eg. get_posts( array( 'foo' => 'bar', 'baz' => 'quux' ) )
			 */
			$paramItems = $this->get_function_call_parameters( $argumentData );


		} else if ( T_CONSTANT_ENCAPSED_STRING === $this->tokens[ $argumentData ]['code'] || T_DOUBLE_QUOTED_STRING === $this->tokens[ $argumentData ]['code'] ) {

			/**
			 * It handles when key value comes in '&query=arg' format.
			 *
			 * eg. get_posts( 'foo=bar&baz=quux' );
			 */

			if ( preg_match_all( '#(?:^|&)([a-z_]+)=([^&]*)#i', $this->strip_quotes( $this->tokens[ $argumentData ]['content'] ), $arrKey ) <= 0 ) {
				return;
			}

			foreach ( $arrKey[1] as $i => $queryKey ) {

				if ( 'suppress_filters' === $queryKey ) {

					$isUsed     = true;
					$arrayValue = $arrKey[2][ $i ];
				}

			}

		}

		// Process multi dimensional array.
		if ( ! empty( $paramItems ) ) {

			// Process the array.
			foreach ( $paramItems as $item ) {

				// If 'raw' contains suppress_filters value.
				if ( false !== strpos( $item['raw'], 'suppress_filters' ) ) {

					// Finding the value by token pointer.
					for ( $ptr = $item['start']; $ptr <= $item['end']; $ptr++ ) {

						// '=>' detected.
						if ( T_DOUBLE_ARROW === $this->tokens[ $ptr ]['code'] ) {

							$acceptType = array( T_DOUBLE_QUOTED_STRING ,T_CONSTANT_ENCAPSED_STRING, T_BOOL_CAST, T_INT_CAST );

							$keyValue = $this->phpcsFile->findNext( $acceptType, $ptr+1 );

							if ( in_array( $this->tokens[$keyValue]['code'], $acceptType,true ) ) {

								$isUsed = true;
								$arrayValue = $this->strip_quotes( $this->tokens[$keyValue]['content'] );
							}

						}

					}

				}

			}

		}

		// If so, expected value was not passed.
		if ( ! $isUsed || ! empty( $arrayValue ) || strlen( $arrayValue ) > 0 ) {

			// In case numeric value is passed.
			$item_value = ( '0' === $arrayValue ) ? 'false' : $arrayValue;

			if ( 'false' !== $item_value ) {

				$this->phpcsFile->addWarning(
					'%s() is discouraged in favor of creating a new WP_Query() so that Advanced Post Cache will cache the query, unless you explicitly supply suppress_filters => false.',
					$parameters[1]['start'],
					'SuppressFilters',
					array( $matched_content )
				);
			}
		}

		return;

	}

} // End class.
