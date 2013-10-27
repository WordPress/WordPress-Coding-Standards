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
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr )
	{
		$tokens = $phpcsFile->getTokens();

		// Check for $wpdb variable
		if ( $tokens[$stackPtr]['content'] != '$wpdb' )
			return;

		if ( false == $phpcsFile->findNext( array( T_OBJECT_OPERATOR ), $stackPtr + 1, null, null, null, true ) )
			return; // This is not a call to the wpdb object

		// Check for whitelisting comment
		$whitelisted = false;
		$whitelist_pattern = '/db call\W*(ok|pass|clear|whitelist)/i';
		$endOfStatement = $phpcsFile->findNext( array( T_SEMICOLON ), $stackPtr + 1, null, null, null, true );
		for ( $i = $endOfStatement + 1; $i < count( $tokens ); $i++ ) {

			if ( in_array( $tokens[$i]['code'], array( T_WHITESPACE ) ) ) {
				continue;
			}

			if ( $tokens[$i]['code'] != T_COMMENT ) {
				break;
			}

			if ( preg_match( $whitelist_pattern, $tokens[$i]['content'], $matches ) > 0 ) {
				$whitelisted = true;
			}
		}

		// Check for Database Schema Changes
		$_pos = $stackPtr;
		while ( $_pos = $phpcsFile->findNext( array( T_CONSTANT_ENCAPSED_STRING, T_DOUBLE_QUOTED_STRING ), $_pos + 1, $endOfStatement, null, null, true ) ) {
			if ( preg_match( '#\b(ALTER|CREATE|DROP)\b#i', $tokens[$_pos]['content'], $matches ) > 0 ) {
				$message = 'Attempting a database schema change is highly discouraged.';
				$this->add_unique_message( $phpcsFile, 'error', $_pos, $tokens[$_pos]['line'], $message );
			}
		}

		// Flag instance if not whitelisted
		if ( ! $whitelisted ) {
			$message = 'Usage of a direct database call is discouraged.';
			$this->add_unique_message( $phpcsFile, 'warning', $stackPtr, $tokens[$stackPtr]['line'], $message );
		}

		$cached = false;
		if ( ! empty( $tokens[$stackPtr]['conditions'] ) ) {
			$conditions = $tokens[$stackPtr]['conditions'];
			$scope_function = null;
			foreach ( $conditions  as $condPtr => $condType ) {
				if ( $condType == T_FUNCTION ) {
					$scope_function = $condPtr;
				}
			}

			$scopeStart = $tokens[$scope_function]['scope_opener'];
			$scopeEnd = $tokens[$scope_function]['scope_closer'];

			if ( $scope_function ) {
				$wpcacheget = $phpcsFile->findNext( array( T_STRING ), $scopeStart + 1, $stackPtr - 1, null, 'wp_cache_get' );
				$wpcacheset = $phpcsFile->findNext( array( T_STRING ), $stackPtr + 1, $scopeEnd - 1, null, 'wp_cache_set' );

				if ( $wpcacheget && $wpcacheset ) {
					$cached = true;
				}
			}

		}

		if ( ! $cached ) {
			$message = 'Usage of a direct database call without caching is prohibited. Use wp_cache_get / wp_cache_set.';
			$this->add_unique_message( $phpcsFile, 'error', $stackPtr, $tokens[$stackPtr]['line'], $message );
		}

	}//end process()

	/**
	 * Add unique message per line
	 * @param PHP_CodeSniffer_File $phpcsFile
	 * @param string $type      (error|warning)
	 * @param int    $pointer
	 * @param int    $line
	 * @param string $message
     * @return void
	 */
	function add_unique_message( PHP_CodeSniffer_File $phpcsFile, $type, $pointer, $line, $message ) {
		$messages = call_user_func( array( $phpcsFile, 'get' . ucfirst( $type . 's' ) ) );
		if ( isset( $messages[$line] ) ) {
			foreach ( $messages[$line] as $idx => $events ) {
				foreach ( $events as $arr ) {
					if ( $arr['message'] == $message ) {
						return false;
					}
				}
			}
		}

		call_user_func( array( $phpcsFile, 'add' . ucfirst( $type ) ), $message, $pointer );
	}


}//end class
