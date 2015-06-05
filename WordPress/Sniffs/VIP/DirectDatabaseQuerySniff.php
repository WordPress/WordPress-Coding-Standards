<?php
/**
 * Flag Database direct queries
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 * @link     https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/69
 */
class WordPress_Sniffs_VIP_DirectDatabaseQuerySniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * The lists of $wpdb methods.
	 *
	 * @since 0.6.0
	 *
	 * @var array[]
	 */
	protected static $methods = array(
		'cachable' => array(
			'delete' => true,
			'get_var' => true,
			'get_col' => true,
			'get_row' => true,
			'get_results' => true,
			'query' => true,
			'replace' => true,
			'update' => true,
		),
		'noncachable' => array(
			'insert' => true,
		),
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
				T_VARIABLE,
			   );

	}//end register()


	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @return int|void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr )
	{
		if ( ! isset( self::$methods['all'] ) ) {
			self::$methods['all'] = array_merge( self::$methods['cachable'], self::$methods['noncachable'] );
		}

		$tokens = $phpcsFile->getTokens();

		// Check for $wpdb variable
		if ( $tokens[$stackPtr]['content'] != '$wpdb' )
			return;

		$is_object_call = $phpcsFile->findNext( array( T_OBJECT_OPERATOR ), $stackPtr + 1, null, null, null, true );
		if ( false == $is_object_call )
			return; // This is not a call to the wpdb object

		$methodPtr = $phpcsFile->findNext( array( T_WHITESPACE ), $is_object_call + 1, null, true, null, true );
		$method = $tokens[ $methodPtr ]['content'];

		if ( ! isset( self::$methods['all'][ $method ] ) ) {
			return;
		}

		$endOfStatement = $phpcsFile->findNext( array( T_SEMICOLON ), $stackPtr + 1, null, null, null, true );
		$endOfLineComment = '';
		for ( $i = $endOfStatement + 1; $i < count( $tokens ); $i++ ) {

			if ( $tokens[$i]['line'] !== $tokens[$endOfStatement]['line'] ) {
				break;
			}

			if ( $tokens[$i]['code'] === T_COMMENT ) {
				$endOfLineComment .= $tokens[$i]['content'];
			}

		}

		$whitelisted_db_call = false;
		if ( preg_match( '/db call\W*(ok|pass|clear|whitelist)/i', $endOfLineComment, $matches ) ) {
			$whitelisted_db_call = true;
		}

		// Check for Database Schema Changes
		$_pos = $stackPtr;
		while ( $_pos = $phpcsFile->findNext( array( T_CONSTANT_ENCAPSED_STRING, T_DOUBLE_QUOTED_STRING ), $_pos + 1, $endOfStatement, null, null, true ) ) {
			if ( preg_match( '#\b(ALTER|CREATE|DROP)\b#i', $tokens[$_pos]['content'], $matches ) > 0 ) {
				$phpcsFile->addError( 'Attempting a database schema change is highly discouraged.', $_pos, 'SchemaChange' );
			}
		}

		// Flag instance if not whitelisted
		if ( ! $whitelisted_db_call ) {
			$phpcsFile->addWarning( 'Usage of a direct database call is discouraged.', $stackPtr, 'DirectQuery' );
		}

		if ( ! isset( self::$methods['cachable'][ $method ] ) ) {
			return $endOfStatement;
		}

		$whitelisted_cache = false;
		$cached = $wp_cache_get = false;
		if ( preg_match( '/cache\s+(ok|pass|clear|whitelist)/i', $endOfLineComment, $matches ) ) {
			$whitelisted_cache = true;
		}
		if ( ! $whitelisted_cache && ! empty( $tokens[$stackPtr]['conditions'] ) ) {
			$scope_function = $phpcsFile->getCondition( $stackPtr, T_FUNCTION );

			if ( $scope_function ) {
				$scopeStart = $tokens[$scope_function]['scope_opener'];
				$scopeEnd = $tokens[$scope_function]['scope_closer'];

				for ( $i = $scopeStart + 1; $i < $scopeEnd; $i++ ) {
					if ( T_STRING === $tokens[ $i ]['code'] ) {

						if ( 'wp_cache_delete' === $tokens[ $i ]['content'] ) {

							if ( in_array( $method, array( 'query', 'update', 'replace', 'delete' ) ) ) {
								$cached = true;
								break;
							}

						} elseif ( 'wp_cache_get' === $tokens[ $i ]['content'] ) {

							$wp_cache_get = true;

						} elseif ( in_array( $tokens[ $i ]['content'], array( 'wp_cache_set', 'wp_cache_add' ) ) ) {

							if ( $wp_cache_get ) {
								$cached = true;
								break;
							}
						}
					}
				}
			}
		}

		if ( ! $cached && ! $whitelisted_cache ) {
			$message = 'Usage of a direct database call without caching is prohibited. Use wp_cache_get / wp_cache_set or wp_cache_delete.';
			$phpcsFile->addError( $message, $stackPtr, 'NoCaching' );
		}

		return $endOfStatement;

	}//end process()

}//end class
