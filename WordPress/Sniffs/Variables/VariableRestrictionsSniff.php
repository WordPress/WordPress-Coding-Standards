<?php
/**
 * Restricts usage of some variables
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 */
class WordPress_Sniffs_Variables_VariableRestrictionsSniff implements PHP_CodeSniffer_Sniff
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
				T_VARIABLE,
				T_OBJECT_OPERATOR,
				T_DOUBLE_COLON,
				T_OPEN_SQUARE_BRACKET,
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
		$tokens = $phpcsFile->getTokens();

		$token = $tokens[$stackPtr];

		$exclude = explode( ',', $this->exclude );

		$groups = $this->getGroups();

		if ( empty( $groups ) ) {
			return;
		}

		// Check if it is a function not a variable
		if ( in_array( $token['code'], array( T_OBJECT_OPERATOR, T_DOUBLE_COLON ) ) ) { // This only works for object vars and array members
			$method = $phpcsFile->findNext( T_WHITESPACE, $stackPtr + 1, null, true );
			$possible_parenthesis = $phpcsFile->findNext( T_WHITESPACE, $method + 1, null, true );
			if ( $tokens[$possible_parenthesis]['code'] == T_OPEN_PARENTHESIS ) {
				return; // So .. it is a function after all !
			}
		}

		foreach ( $groups as $groupName => $group ) {
			
			if ( in_array( $groupName, $exclude ) ) {
				continue;
			}

			$patterns = array();

			// Simple variable
			if ( in_array( $token['code'], array( T_VARIABLE, T_DOUBLE_QUOTED_STRING ) ) && ! empty( $group['variables'] ) ) {
				$patterns = array_merge( $patterns, $group['variables'] );
				$var = $token['content'];
			}
			// Object var, ex: $foo->bar / $foo::bar / Foo::bar / Foo::$bar
			elseif ( in_array( $token['code'], array( T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_DOUBLE_QUOTED_STRING ) ) && ! empty( $group['object_vars'] ) ) {
				$patterns = array_merge( $patterns, $group['object_vars'] );

				$owner = $phpcsFile->findPrevious( array( T_VARIABLE, T_STRING ), $stackPtr );
				$child = $phpcsFile->findNext( array( T_STRING, T_VAR, T_VARIABLE ), $stackPtr );
				$var = implode( '', array( $tokens[$owner]['content'], $token['content'], $tokens[$child]['content'] ) );
			}
			// Array members
			elseif ( in_array( $token['code'], array( T_OPEN_SQUARE_BRACKET, T_DOUBLE_QUOTED_STRING ) ) && ! empty( $group['array_members'] ) ) {
				$patterns = array_merge( $patterns, $group['array_members'] );

				$owner = $phpcsFile->findPrevious( array( T_VARIABLE ), $stackPtr );
				$inside = $phpcsFile->getTokensAsString( $stackPtr, $token['bracket_closer'] - $stackPtr + 1 );
				$var = implode( '', array( $tokens[$owner]['content'], $inside ) );
			}
			else {
				continue;
			}

			if ( empty( $patterns ) ) {
				continue;
			}

			$patterns = array_map( array( $this, 'test_patterns' ), $patterns );

			$pattern = implode( '|', $patterns );

			$delim = ( $token['code'] != T_OPEN_SQUARE_BRACKET ) ? '\b' : '';

			if ( $token['code'] == T_DOUBLE_QUOTED_STRING ) {
				$var = $token['content'];
			}

			if ( preg_match( '#(' . $pattern . ')' . $delim .'#', $var, $match ) < 1 ) {
				continue;
			}

			if ( $group['type'] == 'warning' ) {
				$addWhat = array( $phpcsFile, 'addWarning' );
			} else {
				$addWhat = array( $phpcsFile, 'addError' );
			}

			call_user_func(
				$addWhat,
				$group['message'], 
				$stackPtr, 
				$groupName, 
				array( $var )
				);

			return; // Show one error only

		}

	}//end process()
	
	private function test_patterns( $pattern ) {
		$pattern = preg_quote( $pattern, '#' );
		$pattern = preg_replace(
			array( '#\\\\\*#', '[\'"]' ),
			array( '.*', '\'' ), 
			$pattern
			);
		return $pattern;
	}


}//end class
