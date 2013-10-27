<?php
/**
 * Flag cron schedules less than 15 minutes
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 */
class WordPress_Sniffs_VIP_CronIntervalSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
				T_CONSTANT_ENCAPSED_STRING,
				T_DOUBLE_QUOTED_STRING,
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
		$token  = $tokens[$stackPtr];

		if ( 'cron_schedules' != trim( $token['content'], '"\'' ) ) {
			return;
		}

		// If within add_filter
		$functionPtr = $phpcsFile->findPrevious( T_STRING, key( $token['nested_parenthesis'] ) );
		if ( $tokens[$functionPtr]['content'] != 'add_filter' ) {
			return;
		}

		// Detect callback function name
		$callbackPtr = $phpcsFile->findNext( array( T_COMMA, T_WHITESPACE ), $stackPtr + 1, null, true, null, true );

		// If callback is array, get second element
		if ( T_ARRAY === $tokens[$callbackPtr]['code'] ) {
			$comma = $phpcsFile->findNext( T_COMMA, $callbackPtr + 1 );
			if ( false === $comma ) {
				return $this->confused( $phpcsFile, $stackPtr );
			}

			$callbackPtr = $phpcsFile->findNext( array( T_WHITESPACE ), $comma + 1, null, true, null, true );
			if ( false === $callbackPtr ) {
				return $this->confused( $phpcsFile, $stackPtr );
			}
			if ( ! in_array( $tokens[$callbackPtr]['code'], array( T_CONSTANT_ENCAPSED_STRING, T_DOUBLE_QUOTED_STRING ) ) ) {
				return $this->confused( $phpcsFile, $stackPtr );
			}
		} elseif ( ! in_array( $tokens[$callbackPtr]['code'], array( T_CONSTANT_ENCAPSED_STRING, T_DOUBLE_QUOTED_STRING ) ) ) {
			return $this->confused( $phpcsFile, $stackPtr );
		}

		$functionName = trim( $tokens[$callbackPtr]['content'], '"\'' );
		
		// Search for the function in tokens
		$functionPtr = null;
		foreach ( $tokens as $ptr => $_token ) {
			if ( $_token['code'] == T_STRING && $_token['content'] == $functionName ) {
				$functionPtr = $ptr;
			}
		}

		if ( is_null( $functionPtr ) ) {
			return $this->confused( $phpcsFile, $stackPtr );
		}

		$opening = $phpcsFile->findNext( T_OPEN_CURLY_BRACKET, $functionPtr );
		$closing = $tokens[ $opening ]['bracket_closer'];
		for ( $i = $opening; $i <= $closing; $i++ ) {

			if ( in_array( $tokens[$i]['code'], array( T_CONSTANT_ENCAPSED_STRING, T_DOUBLE_QUOTED_STRING ) ) ) {
				if ( trim( $tokens[$i]['content'], '\'"' ) == 'interval' ) {
					$operator = $phpcsFile->findNext( T_DOUBLE_ARROW, $i, null, null, null, true );
					if ( empty( $operator ) ) {
						$this->confused( $phpcsFile, $stackPtr );
					}

					$valueStart = $phpcsFile->findNext( T_WHITESPACE, $operator + 1, null, true, null, true );
					$valueEnd   = $phpcsFile->findNext( array( T_COMMA, T_CLOSE_PARENTHESIS ), $valueStart + 1 );
					$value = $phpcsFile->getTokensAsString( $valueStart, $valueEnd - $valueStart );
					
					if ( is_numeric( $value ) ) {
						$interval = $value;
						break;
					}

					$valueExp = explode( '*', $value );
					$valueExp = array_map( 'trim', $valueExp );
					if ( array_filter( $valueExp, 'is_numeric' ) ) {
						$interval = array_product( $valueExp );
						break;
					}

					// If all digits and operators, eval!
					if ( preg_match( '#^[\s\d\+\*\-\/]+$#', $value ) > 0 ) {
						$interval = eval( $value ); // No harm here
						break;
					}

					return $this->confused( $phpcsFile, $stackPtr );
					break;
				}
			}
		}
		
		if ( $interval < ( 15 * 60 * 60 ) ) {
			$phpcsFile->addError( 'Scheduling crons at %s sec ( less than 15 min ) is prohibited.', $stackPtr, 'cron_schedules_interval', array( $interval ) );
			return;
		}

		
	}//end process()


	public function confused( $phpcsFile, $stackPtr ) {
		$phpcsFile->addWarning( 'Detected changing of cron_schedules, but could not detect the interval value.', $stackPtr );
	}


}//end class
