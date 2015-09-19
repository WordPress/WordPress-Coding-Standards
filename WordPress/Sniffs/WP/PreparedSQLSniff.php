<?php
/**
 * Sniff for prepared SQL.
 *
 * Makes sure that variables aren't directly interpolated into SQL statements.
 *
 * @package WordPress-Coding-Standards
 * @since 0.8.0
 */
class WordPress_Sniffs_WP_PreparedSQLSniff extends WordPress_Sniff {

	/**
	 * The lists of $wpdb methods.
	 *
	 * @since 0.8.0
	 *
	 * @var array[]
	 */
	protected static $methods = array(
		'get_var' => true,
		'get_col' => true,
		'get_row' => true,
		'get_results' => true,
		'prepare' => true,
		'query' => true,
	);

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
	 * @since 0.8.0
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @return int|void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

		$tokens = $phpcsFile->getTokens();

		// Check for $wpdb variable.
		if ( '$wpdb' !== $tokens[ $stackPtr ]['content'] ) {
			return;
		}

		$this->init( $phpcsFile );

		$method_call_end = $this->is_wpdb_method_call( $stackPtr );

		if ( ! $method_call_end ) {
			return;
		}

		if ( $this->has_whitelist_comment( 'unprepared SQL', $stackPtr ) ) {
			return;
		}

		for ( $i = $stackPtr + 1; $i < $method_call_end; $i++ ) {

			if ( T_DOUBLE_QUOTED_STRING === $tokens[ $i ]['code'] ) {

				$string = str_replace( '$wpdb', '', $tokens[ $i ]['content'] );

				if ( false !== strpos( $string, '$' ) ) {

					$phpcsFile->addError(
						'Use placeholders and $wpdb->prepare(); found %s',
						$i,
						'NotPrepared',
						array( $tokens[ $i ]['content'] )
					);
				}

				continue;
			}

			if ( T_VARIABLE !== $tokens[ $i ]['code'] ) {
				continue;
			}

			if ( '$wpdb' === $tokens[ $i ]['content'] ) {

				$is_method_call = $this->is_wpdb_method_call( $i );

				if ( $is_method_call ) {
					$method_call_end = $is_method_call;
				}

			} else {

				 $phpcsFile->addError(
					 'Use placeholders and $wpdb->prepare(); found %s',
					 $i,
					 'NotPrepared',
					 array( $tokens[ $i ]['content'] )
				 );
			}
		}

		return $method_call_end;

	} // end process().

	/**
	 * Checks whether this is a call to a $wpdb method that we want to sniff.
	 *
	 * @since 0.8.0
	 *
	 * @param int $stackPtr The index of the $wpdb variable.
	 *
	 * @return int|false The index of the end of the method call, or false.
	 */
	protected function is_wpdb_method_call( $stackPtr ) {

		// Check that this is a method call.
		$is_object_call = $this->phpcsFile->findNext( array( T_OBJECT_OPERATOR ), $stackPtr + 1, null, null, null, true );
		if ( false === $is_object_call ) {
			return false;
		}

		$methodPtr = $this->phpcsFile->findNext( array( T_WHITESPACE ), $is_object_call + 1, null, true, null, true );
		$method = $this->tokens[ $methodPtr ]['content'];

		// Check that this is one of the methods that we are interested in.
		if ( ! isset( self::$methods[ $method ] ) ) {
			return false;
		}

		// Find the opening parenthesis.
		$opening_paren = $this->phpcsFile->findNext( T_WHITESPACE, $methodPtr + 1, null, true, null, true );

		if ( ! $opening_paren || T_OPEN_PARENTHESIS !== $this->tokens[ $opening_paren ]['code'] ) {
			return false;
		}

		// Find the end of the first parameter.
		$end = $this->phpcsFile->findEndOfStatement( $opening_paren + 1 );

		return $end + 1;
	}

} // end class.
