<?php
/**
 * Restricts array assignment of certain keys
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 */
class WordPress_Sniffs_Arrays_ArrayAssignmentRestrictionsSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * Exclude groups
	 *
	 * Example: 'foo,bar'
	 * 
	 * @var string Comma-delimited group list 
	 */
	public $exclude = '';

	/**
	 * Groups of variable data to check against.
	 * Don't use this in extended classes, override getGroups() instead.
	 * This is only used for Unit tests.
	 * 
	 * @var array
	 */
	public static $groups = array();


	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
				T_DOUBLE_ARROW,
				T_CLOSE_SQUARE_BRACKET,
				T_CONSTANT_ENCAPSED_STRING,
				T_DOUBLE_QUOTED_STRING,
			   );

	}//end register()

	/**
	 * Groups of variables to restrict
	 * This should be overridden in extending classes.
	 *
	 * Example: groups => array(
	 * 	'wpdb' => array(
	 * 		'type' => 'error' | 'warning',
	 * 		'message' => 'Dont use this one please!',
	 * 		'variables' => array( '$val', '$var' ),
	 * 		'object_vars' => array( '$foo->bar', .. ),
	 * 		'array_members' => array( '$foo['bar']', .. ),
	 * 	)
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return self::$groups;
	}


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

		$groups = $this->getGroups();

		if ( empty( $groups ) ) {
			$phpcsFile->removeTokenListener( $this, $this->register() );
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$token = $tokens[ $stackPtr ];
		$exclude = explode( ',', $this->exclude );

		if ( in_array( $token['code'], array( T_CLOSE_SQUARE_BRACKET ) ) ) {
			$equal = $phpcsFile->findNext( T_WHITESPACE, $stackPtr + 1, null, true );
			if ( $tokens[$equal]['code'] !== T_EQUAL ) {
				return; // This is not an assignment!
			}
		}

		// Instances: Multi-dimensional array, keyed by line
		$inst = array();

		// $foo = array( 'bar' => 'taz' );
		// $foo['bar'] = $taz;
		if ( in_array( $token['code'], array( T_CLOSE_SQUARE_BRACKET, T_DOUBLE_ARROW ) ) ) {
			if ( $token['code'] == T_CLOSE_SQUARE_BRACKET ) {
				$operator = $phpcsFile->findNext( array( T_EQUAL ), $stackPtr + 1 );
			} elseif ( $token['code'] == T_DOUBLE_ARROW ) {
				$operator = $stackPtr;
			}
			$keyIdx = $phpcsFile->findPrevious( array( T_WHITESPACE, T_CLOSE_SQUARE_BRACKET ), $operator - 1, null, true );
			if ( ! is_numeric( $tokens[$keyIdx]['content'] ) ) {
				$key = trim( $tokens[$keyIdx]['content'], '\'"' );
				$valStart = $phpcsFile->findNext( array( T_WHITESPACE ), $operator + 1, null, true );
				$valEnd = $phpcsFile->findNext( array( T_COMMA, T_SEMICOLON ), $valStart + 1, null, false, null, true );
				$val = $phpcsFile->getTokensAsString( $valStart, $valEnd - $valStart );
				$val = trim( $val, '\'"' );
				$inst[ $key ][] = array( $val, $token['line'] );
			}
		}
		// $foo = 'bar=taz&other=thing';
		elseif ( in_array( $token['code'], array( T_CONSTANT_ENCAPSED_STRING, T_DOUBLE_QUOTED_STRING ) ) ) {
			if ( preg_match_all( '#[\'"&]([a-z_]+)=([^\'"&]*)#i', $token['content'], $matches ) <= 0 ) {
				return; // No assignments here, nothing to check
			}
			foreach ( $matches[1] as $i => $_k ) {
				$inst[ $_k ][] = array( $matches[2][$i], $token['line'] );
			}
		}

		if ( empty( $inst ) ) {
			return;
		}


		foreach ( $groups as $groupName => $group ) {
			
			if ( in_array( $groupName, $exclude ) ) {
				continue;
			}

			$callback = ( isset( $group['callback'] ) && is_callable( $group['callback'] ) ) ? $group['callback'] : array( $this, 'callback' );

			foreach ( $inst as $key => $assignments ) {
				foreach ( $assignments as $occurance ) {
					list( $val, $line ) = $occurance;

					if ( ! in_array( $key, $group['keys'] ) ) {
						continue;
					}

					$output = call_user_func( $callback, $key, $val, $line, $group );

					if ( $output === false || $output === null ) {
						continue;
					} elseif ( $output === true ) {
						$message = $group['message'];
					} else {
						$message = $output;
					}

					if ( $group['type'] == 'warning' ) {
						$addWhat = array( $phpcsFile, 'addWarning' );
					} else {
						$addWhat = array( $phpcsFile, 'addError' );
					}

					call_user_func(
						$addWhat,
						$message, 
						$stackPtr, 
						$groupName, 
						array( $key, $val )
						);
				}
			}


			// return; // Show one error only

		}

	}//end process()

	/**
	 * Callback to process each confirmed key, to check value
	 * This must be extended to add the logic to check assignment value
	 * 
	 * @param  string   $key   Array index / key
	 * @param  mixed    $val   Assigned value
	 * @param  int      $line  Token line
	 * @param  array    $group Group definition
	 * @return mixed           FALSE if no match, TRUE if matches, STRING if matches with custom error message passed to ->process()
	 */
	public function callback( $key, $val, $line, $group ) {
		return true;
	}


}//end class
