<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Restricts usage of some functions.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.10.0 Class became a proper abstract class. This was already the behaviour.
 *                 Moved the file and renamed the class from
 *                 `WordPress_Sniffs_Functions_FunctionRestrictionsSniff` to
 *                 `WordPress_AbstractFunctionRestrictionsSniff`.
 */
abstract class WordPress_AbstractFunctionRestrictionsSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * Exclude groups.
	 *
	 * Example: 'switch_to_blog,user_meta'
	 *
	 * @var string Comma-delimited group list.
	 */
	public $exclude = '';

	/**
	 * Groups of function data to check against.
	 * Don't use this in extended classes, override getGroups() instead.
	 * This is only used for Unit tests.
	 *
	 * @var array
	 */
	public static $unittest_groups = array();

	/**
	 * Regex pattern with placeholder for the function names.
	 *
	 * @var string
	 */
	protected $regex_pattern = '`\b(?:%s)\b`i';

	/**
	 * Cache for the group information.
	 *
	 * @var array
	 */
	protected $groups = array();

	/**
	 * Groups of functions to restrict.
	 *
	 * This method should be overridden in extending classes.
	 *
	 * Example: groups => array(
	 * 	'lambda' => array(
	 * 		'type'      => 'error' | 'warning',
	 * 		'message'   => 'Use anonymous functions instead please!',
	 * 		'functions' => array( 'eval', 'create_function' ),
	 * 	)
	 * )
	 *
	 * You can use * wildcards to target a group of functions.
	 *
	 * @return array
	 */
	abstract public function getGroups();

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		// Prepare the function group regular expressions only once.
		if ( false === $this->setup_groups( 'functions' ) ) {
			return array();
		}

		return array(
			T_STRING,
			T_EVAL,
		);
	}

	/**
	 * Set up the regular expressions for each group.
	 *
	 * @param string $key The group array index key where the input for the regular expression can be found.
	 * @return bool True is the groups were setup. False if not.
	 */
	protected function setup_groups( $key ) {
		// Prepare the function group regular expressions only once.
		$this->groups = $this->getGroups();

		if ( empty( $this->groups ) && empty( self::$unittest_groups ) ) {
			return false;
		}

		// Allow for adding extra unit tests.
		if ( ! empty( self::$unittest_groups ) ) {
			$this->groups = array_merge( $this->groups, self::$unittest_groups );
		}

		foreach ( $this->groups as $groupName => $group ) {
			if ( empty( $group[ $key ] ) ) {
				unset( $this->groups[ $groupName ] );
			} else {
				$items = array_map( array( $this, 'prepare_name_for_regex' ), $group[ $key ] );
				$items = implode( '|', $items );

				$this->groups[ $groupName ]['regex'] = sprintf( $this->regex_pattern, $items );
			}
		}

		if ( empty( $this->groups ) ) {
			return false;
		}

		return true;
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

		foreach ( $this->groups as $groupName => $group ) {

			if ( in_array( $groupName, $exclude, true ) ) {
				continue;
			}

			if ( isset( $group['whitelist'][ $token['content'] ] ) ) {
				continue;
			}

			if ( preg_match( $group['regex'], $token['content'] ) < 1 ) {
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

	/**
	 * Prepare the function name for use in a regular expression.
	 *
	 * The getGroups() method allows for providing function names with a wildcard * to target
	 * a group of functions. This prepare routine takes that into account while still safely
	 * escaping the function name for use in a regular expression.
	 *
	 * @param string $function Function name.
	 * @return string Regex escaped function name.
	 */
	protected function prepare_name_for_regex( $function ) {
		$function = str_replace( array( '.*', '*' ) , '#', $function ); // Replace wildcards with placeholder.
		$function = preg_quote( $function, '`' );
		$function = str_replace( '#', '.*', $function ); // Replace placeholder with regex wildcard.

		return $function;
	}

} // End class.
