<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Flag Database direct queries.
 *
 * @link    https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#direct-database-queries
 * @link    https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#database-alteration
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.6.0 Removed the add_unique_message() function as it is no longer needed.
 */
class WordPress_Sniffs_VIP_DirectDatabaseQuerySniff implements PHP_CodeSniffer_Sniff {

	/**
	 * List of custom cache get functions.
	 *
	 * @since 0.6.0
	 *
	 * @var string[]
	 */
	public $customCacheGetFunctions = array();

	/**
	 * List of custom cache set functions.
	 *
	 * @since 0.6.0
	 *
	 * @var string[]
	 */
	public $customCacheSetFunctions = array();

	/**
	 * List of custom cache delete functions.
	 *
	 * @since 0.6.0
	 *
	 * @var string[]
	 */
	public $customCacheDeleteFunctions = array();

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
	 * @return int|void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		if ( ! isset( self::$methods['all'] ) ) {
			self::$methods['all'] = array_merge( self::$methods['cachable'], self::$methods['noncachable'] );

			WordPress_Sniff::$cacheGetFunctions = array_merge(
				WordPress_Sniff::$cacheGetFunctions,
				array_flip( $this->customCacheGetFunctions )
			);

			WordPress_Sniff::$cacheSetFunctions = array_merge(
				WordPress_Sniff::$cacheSetFunctions,
				array_flip( $this->customCacheSetFunctions )
			);

			WordPress_Sniff::$cacheDeleteFunctions = array_merge(
				WordPress_Sniff::$cacheDeleteFunctions,
				array_flip( $this->customCacheDeleteFunctions )
			);
		}

		$tokens = $phpcsFile->getTokens();

		// Check for $wpdb variable.
		if ( '$wpdb' !== $tokens[ $stackPtr ]['content'] ) {
			return;
		}

		$is_object_call = $phpcsFile->findNext( array( T_OBJECT_OPERATOR ), ( $stackPtr + 1 ), null, false, null, true );
		if ( false === $is_object_call ) {
			return; // This is not a call to the wpdb object.
		}

		$methodPtr = $phpcsFile->findNext( array( T_WHITESPACE ), ( $is_object_call + 1 ), null, true, null, true );
		$method    = $tokens[ $methodPtr ]['content'];

		if ( ! isset( self::$methods['all'][ $method ] ) ) {
			return;
		}

		$endOfStatement   = $phpcsFile->findNext( array( T_SEMICOLON ), ( $stackPtr + 1 ), null, false, null, true );
		$endOfLineComment = '';
		$tokenCount       = count( $tokens );
		for ( $i = ( $endOfStatement + 1 ); $i < $tokenCount; $i++ ) {

			if ( $tokens[ $i ]['line'] !== $tokens[ $endOfStatement ]['line'] ) {
				break;
			}

			if ( T_COMMENT === $tokens[ $i ]['code'] ) {
				$endOfLineComment .= $tokens[ $i ]['content'];
			}
		}

		$whitelisted_db_call = false;
		if ( preg_match( '/db call\W*(?:ok|pass|clear|whitelist)/i', $endOfLineComment ) ) {
			$whitelisted_db_call = true;
		}

		// Check for Database Schema Changes.
		$_pos = $stackPtr;
		while ( $_pos = $phpcsFile->findNext( array( T_CONSTANT_ENCAPSED_STRING, T_DOUBLE_QUOTED_STRING ), ( $_pos + 1 ), $endOfStatement, false, null, true ) ) {
			if ( preg_match( '#\b(?:ALTER|CREATE|DROP)\b#i', $tokens[ $_pos ]['content'] ) > 0 ) {
				$phpcsFile->addError( 'Attempting a database schema change is highly discouraged.', $_pos, 'SchemaChange' );
			}
		}

		// Flag instance if not whitelisted.
		if ( ! $whitelisted_db_call ) {
			$phpcsFile->addWarning( 'Usage of a direct database call is discouraged.', $stackPtr, 'DirectQuery' );
		}

		if ( ! isset( self::$methods['cachable'][ $method ] ) ) {
			return $endOfStatement;
		}

		$whitelisted_cache = false;
		$cached            = false;
		$wp_cache_get      = false;
		if ( preg_match( '/cache\s+(?:ok|pass|clear|whitelist)/i', $endOfLineComment ) ) {
			$whitelisted_cache = true;
		}
		if ( ! $whitelisted_cache && ! empty( $tokens[ $stackPtr ]['conditions'] ) ) {
			$scope_function = $phpcsFile->getCondition( $stackPtr, T_FUNCTION );

			if ( $scope_function ) {
				$scopeStart = $tokens[ $scope_function ]['scope_opener'];
				$scopeEnd   = $tokens[ $scope_function ]['scope_closer'];

				for ( $i = ( $scopeStart + 1 ); $i < $scopeEnd; $i++ ) {
					if ( T_STRING === $tokens[ $i ]['code'] ) {

						if ( isset( WordPress_Sniff::$cacheDeleteFunctions[ $tokens[ $i ]['content'] ] ) ) {

							if ( in_array( $method, array( 'query', 'update', 'replace', 'delete' ), true ) ) {
								$cached = true;
								break;
							}
						} elseif ( isset( WordPress_Sniff::$cacheGetFunctions[ $tokens[ $i ]['content'] ] ) ) {

							$wp_cache_get = true;

						} elseif ( isset( WordPress_Sniff::$cacheSetFunctions[ $tokens[ $i ]['content'] ] ) ) {

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

	} // End process().

} // End class.
