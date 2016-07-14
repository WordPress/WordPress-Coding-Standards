<?php
/**
 * WordPress Coding Standard.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Restricts usage of some functions.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Shady Sharaf <shady@x-team.com>
 */
class WordPress_Sniffs_Functions_FunctionRestrictionsSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Exclude groups.
	 *
	 * Example: 'switch_to_blog,user_meta'
	 *
	 * @var string Comma-delimited group list.
	 */
	public $exclude = '';

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_STRING,
			T_EVAL,
		);

	} // end register()

	/**
	 * Groups of functions to restrict.
	 *
	 * Example: groups => array(
	 * 	'lambda' => array(
	 * 		'type'      => 'error' | 'warning',
	 * 		'message'   => 'Use anonymous functions instead please!',
	 * 		'functions' => array( 'eval', 'create_function' ),
	 * 	)
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return array();
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
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$token  = $tokens[ $stackPtr ];

		// Exclude function definitions, class methods, and namespaced calls.
		if (
			T_STRING === $token['code']
			&&
			( $prev = $phpcsFile->findPrevious( T_WHITESPACE, ( $stackPtr - 1 ), null, true ) )
			&&
			(
				// Skip sniffing if calling a method, or on function definitions.
				in_array( $tokens[ $prev ]['code'], array( T_FUNCTION, T_DOUBLE_COLON, T_OBJECT_OPERATOR ), true )
				||
				(
					// Skip namespaced functions, ie: \foo\bar() not \bar().
					T_NS_SEPARATOR === $tokens[ $prev ]['code']
					&&
					( $pprev = $phpcsFile->findPrevious( T_WHITESPACE, ( $prev - 1 ), null, true ) )
					&&
					T_STRING === $tokens[ $pprev ]['code']
				)
			)
			) {
			return;
		}

		$exclude = explode( ',', $this->exclude );

		$groups = $this->getGroups();

		if ( empty( $groups ) ) {
			return ( count( $tokens ) + 1 );
		}

		foreach ( $groups as $groupName => $group ) {

			if ( in_array( $groupName, $exclude, true ) ) {
				continue;
			}

			$functions = implode( '|', $group['functions'] );
			$functions = preg_replace( '#[^\.]\*#', '.*', $functions ); // So you can use * instead of .*

			if ( preg_match( '#\b(' . $functions . ')\b#', $token['content'] ) < 1 ) {
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
				array( $token['content'] )
			);

		}

	} // end process()

} // end class
