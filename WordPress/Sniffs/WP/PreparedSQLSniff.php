<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Sniff for prepared SQL.
 *
 * Makes sure that variables aren't directly interpolated into SQL statements.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#formatting-sql-statements
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.8.0
 */
class WordPress_Sniffs_WP_PreparedSQLSniff extends WordPress_Sniff {

	/**
	 * The lists of $wpdb methods.
	 *
	 * @since 0.8.0
	 *
	 * @var array
	 */
	protected static $methods = array(
		'get_var'     => true,
		'get_col'     => true,
		'get_row'     => true,
		'get_results' => true,
		'prepare'     => true,
		'query'       => true,
	);

	/**
	 * Tokens that we don't flag when they are found in a $wpdb method call.
	 *
	 * @since 0.9.0
	 *
	 * @var array
	 */
	protected $ignored_tokens = array(
		T_OBJECT_OPERATOR          => true,
		T_OPEN_PARENTHESIS         => true,
		T_CLOSE_PARENTHESIS        => true,
		T_WHITESPACE               => true,
		T_STRING_CONCAT            => true,
		T_CONSTANT_ENCAPSED_STRING => true,
		T_OPEN_SQUARE_BRACKET      => true,
		T_CLOSE_SQUARE_BRACKET     => true,
		T_COMMA                    => true,
		T_LNUMBER                  => true,
	);

	/**
	 * A loop pointer.
	 *
	 * It is a property so that we can access it in all of our methods.
	 *
	 * @since 0.9.0
	 *
	 * @var int
	 */
	protected $i;

	/**
	 * The loop end marker.
	 *
	 * It is a property so that we can access it in all of our methods.
	 *
	 * @since 0.9.0
	 *
	 * @var int
	 */
	protected $end;

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since 0.8.0
	 *
	 * @return array
	 */
	public function register() {
	    return array(
		    T_VARIABLE,
	    );
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @since 0.8.0
	 *
	 * @return int|void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

		$this->init( $phpcsFile );

		// Check for $wpdb variable.
		if ( '$wpdb' !== $this->tokens[ $stackPtr ]['content'] ) {
			return;
		}

		if ( ! $this->is_wpdb_method_call( $stackPtr ) ) {
			return;
		}

		if ( $this->has_whitelist_comment( 'unprepared SQL', $stackPtr ) ) {
			return;
		}

		for ( $this->i; $this->i < $this->end; $this->i++ ) {

			if ( isset( $this->ignored_tokens[ $this->tokens[ $this->i ]['code'] ] ) ) {
				continue;
			}

			if ( T_DOUBLE_QUOTED_STRING === $this->tokens[ $this->i ]['code'] ) {

				$bad_variables = array_filter(
					$this->get_interpolated_variables( $this->tokens[ $this->i ]['content'] ),
					create_function( '$symbol', 'return ! in_array( $symbol, array( "wpdb" ), true );' ) // Replace this with closure once 5.3 is minimum requirement.
				);

				foreach ( $bad_variables as $bad_variable ) {
					$phpcsFile->addError(
						'Use placeholders and $wpdb->prepare(); found interpolated variable $%s at %s',
						$this->i,
						'NotPrepared',
						array(
							$bad_variable,
							$this->tokens[ $this->i ]['content'],
						)
					);
				}
				continue;
			}

			if ( T_VARIABLE === $this->tokens[ $this->i ]['code'] ) {
				if ( '$wpdb' === $this->tokens[ $this->i ]['content'] ) {
					$this->is_wpdb_method_call( $this->i );
					continue;
				}
			}

			if ( T_STRING === $this->tokens[ $this->i ]['code'] ) {

				if (
					isset( self::$SQLEscapingFunctions[ $this->tokens[ $this->i ]['content'] ] )
					|| isset( self::$SQLAutoEscapedFunctions[ $this->tokens[ $this->i ]['content'] ] )
				) {

					// Find the opening parenthesis.
					$opening_paren = $this->phpcsFile->findNext( T_WHITESPACE, ( $this->i + 1 ), null, true, null, true );

					if (
						$opening_paren
						&& T_OPEN_PARENTHESIS === $this->tokens[ $opening_paren ]['code']
						&& isset( $this->tokens[ $opening_paren ]['parenthesis_closer'] )
					) {
						// Skip past the end of the function.
						$this->i = $this->tokens[ $opening_paren ]['parenthesis_closer'];
						continue;
					}
				} elseif ( isset( self::$formattingFunctions[ $this->tokens[ $this->i ]['content'] ] ) ) {
					continue;
				}
			}

			$phpcsFile->addError(
				'Use placeholders and $wpdb->prepare(); found %s',
				$this->i,
				'NotPrepared',
				array( $this->tokens[ $this->i ]['content'] )
			);
		}

		return $this->end;

	} // end process().

	/**
	 * Checks whether this is a call to a $wpdb method that we want to sniff.
	 *
	 * The $i and $end properties are automatically set to correspond to the start
	 * and end of the method call. The $i property is also set if this is not a
	 * method call but rather the use of a $wpdb property.
	 *
	 * @since 0.8.0
	 * @since 0.9.0 The return value is now always boolean. The $end and $i member
	 *              vars are automatically updated.
	 *
	 * @param int $stackPtr The index of the $wpdb variable.
	 *
	 * @return bool Whether this is a $wpdb method call.
	 */
	protected function is_wpdb_method_call( $stackPtr ) {

		// Check that this is a method call.
		$is_object_call = $this->phpcsFile->findNext( array( T_OBJECT_OPERATOR ), ( $stackPtr + 1 ), null, null, null, true );
		if ( false === $is_object_call ) {
			return false;
		}

		$methodPtr = $this->phpcsFile->findNext( array( T_WHITESPACE ), ( $is_object_call + 1 ), null, true, null, true );
		$method = $this->tokens[ $methodPtr ]['content'];

		// Find the opening parenthesis.
		$opening_paren = $this->phpcsFile->findNext( T_WHITESPACE, ( $methodPtr + 1 ), null, true, null, true );

		if ( ! $opening_paren ) {
			return false;
		}

		$this->i = $opening_paren;

		if ( T_OPEN_PARENTHESIS !== $this->tokens[ $opening_paren ]['code'] ) {
			return false;
		}

		// Check that this is one of the methods that we are interested in.
		if ( ! isset( self::$methods[ $method ] ) ) {
			return false;
		}

		// Find the end of the first parameter.
		$this->end = $this->phpcsFile->findEndOfStatement( $opening_paren + 1 );

		if ( T_COMMA !== $this->tokens[ $this->end ]['code'] ) {
			$this->end += 1;
		}

		return true;
	} // is_wpdb_method_call()

} // End class.
