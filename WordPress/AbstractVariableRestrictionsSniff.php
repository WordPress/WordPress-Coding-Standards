<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Restricts usage of some variables.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.10.0 Class became a proper abstract class. This was already the behaviour.
 *                 Moved the file and renamed the class from
 *                 `WordPress_Sniffs_Variables_VariableRestrictionsSniff` to
 *                 `WordPress_AbstractVariableRestrictionsSniff`.
 */
abstract class WordPress_AbstractVariableRestrictionsSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Exclude groups.
	 *
	 * Example: 'foo,bar'
	 *
	 * @var string Comma-delimited group list.
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
	public function register() {
		return array(
			T_VARIABLE,
			T_OBJECT_OPERATOR,
			T_DOUBLE_COLON,
			T_OPEN_SQUARE_BRACKET,
			T_DOUBLE_QUOTED_STRING,
		);

	}

	/**
	 * Groups of variables to restrict.
	 *
	 * This method should be overridden in extending classes.
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
	abstract public function getGroups();

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @return int|void If no groups are found, a stack pointer to indicate to skip the current one is passed.
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens  = $phpcsFile->getTokens();
		$token   = $tokens[ $stackPtr ];
		$exclude = explode( ',', $this->exclude );
		$groups  = $this->getGroups();

		if ( empty( $groups ) ) {
			return ( count( $tokens ) + 1 );
		}

		// Check if it is a function not a variable.
		if ( in_array( $token['code'], array( T_OBJECT_OPERATOR, T_DOUBLE_COLON ), true ) ) { // This only works for object vars and array members.
			$method               = $phpcsFile->findNext( T_WHITESPACE, ( $stackPtr + 1 ), null, true );
			$possible_parenthesis = $phpcsFile->findNext( T_WHITESPACE, ( $method + 1 ), null, true );
			if ( T_OPEN_PARENTHESIS === $tokens[ $possible_parenthesis ]['code'] ) {
				return; // So .. it is a function after all !
			}
		}

		foreach ( $groups as $groupName => $group ) {

			if ( in_array( $groupName, $exclude, true ) ) {
				continue;
			}

			$patterns = array();

			// Simple variable.
			if ( in_array( $token['code'], array( T_VARIABLE, T_DOUBLE_QUOTED_STRING ), true ) && ! empty( $group['variables'] ) ) {
				$patterns = array_merge( $patterns, $group['variables'] );
				$var      = $token['content'];

			} elseif ( in_array( $token['code'], array( T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_DOUBLE_QUOTED_STRING ), true ) && ! empty( $group['object_vars'] ) ) {
				// Object var, ex: $foo->bar / $foo::bar / Foo::bar / Foo::$bar .
				$patterns = array_merge( $patterns, $group['object_vars'] );

				$owner = $phpcsFile->findPrevious( array( T_VARIABLE, T_STRING ), $stackPtr );
				$child = $phpcsFile->findNext( array( T_STRING, T_VAR, T_VARIABLE ), $stackPtr );
				$var   = implode( '', array( $tokens[ $owner ]['content'], $token['content'], $tokens[ $child ]['content'] ) );

			} elseif ( in_array( $token['code'], array( T_OPEN_SQUARE_BRACKET, T_DOUBLE_QUOTED_STRING ), true ) && ! empty( $group['array_members'] ) ) {
				// Array members.
				$patterns = array_merge( $patterns, $group['array_members'] );

				$owner  = $phpcsFile->findPrevious( array( T_VARIABLE ), $stackPtr );
				$inside = $phpcsFile->getTokensAsString( $stackPtr, ( $token['bracket_closer'] - $stackPtr + 1 ) );
				$var    = implode( '', array( $tokens[ $owner ]['content'], $inside ) );
			} else {
				continue;
			}

			if ( empty( $patterns ) ) {
				continue;
			}

			$patterns = array_map( array( $this, 'test_patterns' ), $patterns );
			$pattern  = implode( '|', $patterns );
			$delim    = ( T_OPEN_SQUARE_BRACKET !== $token['code'] ) ? '\b' : '';

			if ( T_DOUBLE_QUOTED_STRING === $token['code'] ) {
				$var = $token['content'];
			}

			if ( preg_match( '#(' . $pattern . ')' . $delim . '#', $var, $match ) !== 1 ) {
				continue;
			}

			if ( 'warning' === $group['type'] ) {
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

			return; // Show one error only.

		}

	} // end process()

	/**
	 * Transform a wildcard pattern to a usable regex pattern.
	 *
	 * @param string $pattern Pattern.
	 * @return string
	 */
	private function test_patterns( $pattern ) {
		$pattern = preg_quote( $pattern, '#' );
		$pattern = preg_replace(
			array( '#\\\\\*#', '[\'"]' ),
			array( '.*', '\'' ),
			$pattern
		);
		return $pattern;
	}

} // End class.
