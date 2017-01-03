<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Restricts array assignment of certain keys.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.10.0 Class became a proper abstract class. This was already the behaviour.
 *                 Moved the file and renamed the class from
 *                 `WordPress_Sniffs_Arrays_ArrayAssignmentRestrictionsSniff` to
 *                 `WordPress_AbstractArrayAssignmentRestrictionsSniff`.
 */
abstract class WordPress_AbstractArrayAssignmentRestrictionsSniff extends WordPress_Sniff {

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
			T_DOUBLE_ARROW,
			T_CLOSE_SQUARE_BRACKET,
			T_CONSTANT_ENCAPSED_STRING,
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
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

		$groups = $this->getGroups();

		if ( empty( $groups ) ) {
			$phpcsFile->removeTokenListener( $this, $this->register() );
			return;
		}

		$tokens  = $phpcsFile->getTokens();
		$token   = $tokens[ $stackPtr ];
		$exclude = explode( ',', $this->exclude );

		if ( in_array( $token['code'], array( T_CLOSE_SQUARE_BRACKET ), true ) ) {
			$equal = $phpcsFile->findNext( T_WHITESPACE, ( $stackPtr + 1 ), null, true );
			if ( T_EQUAL !== $tokens[ $equal ]['code'] ) {
				return; // This is not an assignment!
			}
		}

		// Instances: Multi-dimensional array, keyed by line.
		$inst = array();

		/*
		   Covers:
		   $foo = array( 'bar' => 'taz' );
		   $foo['bar'] = $taz;
		 */
		if ( in_array( $token['code'], array( T_CLOSE_SQUARE_BRACKET, T_DOUBLE_ARROW ), true ) ) {
			$operator = $stackPtr; // T_DOUBLE_ARROW.
			if ( T_CLOSE_SQUARE_BRACKET === $token['code']  ) {
				$operator = $phpcsFile->findNext( array( T_EQUAL ), ( $stackPtr + 1 ) );
			}

			$keyIdx = $phpcsFile->findPrevious( array( T_WHITESPACE, T_CLOSE_SQUARE_BRACKET ), ( $operator - 1 ), null, true );
			if ( ! is_numeric( $tokens[ $keyIdx ]['content'] ) ) {
				$key            = trim( $tokens[ $keyIdx ]['content'], '\'"' );
				$valStart       = $phpcsFile->findNext( array( T_WHITESPACE ), ( $operator + 1 ), null, true );
				$valEnd         = $phpcsFile->findNext( array( T_COMMA, T_SEMICOLON ), ( $valStart + 1 ), null, false, null, true );
				$val            = $phpcsFile->getTokensAsString( $valStart, ( $valEnd - $valStart ) );
				$val            = trim( $val, '\'"' );
				$inst[ $key ][] = array( $val, $token['line'] );
			}
		} elseif ( in_array( $token['code'], array( T_CONSTANT_ENCAPSED_STRING, T_DOUBLE_QUOTED_STRING ), true ) ) {
			// $foo = 'bar=taz&other=thing';
			if ( preg_match_all( '#[\'"&]([a-z_]+)=([^\'"&]*)#i', $token['content'], $matches ) <= 0 ) {
				return; // No assignments here, nothing to check.
			}
			foreach ( $matches[1] as $i => $_k ) {
				$inst[ $_k ][] = array( $matches[2][ $i ], $token['line'] );
			}
		}

		if ( empty( $inst ) ) {
			return;
		}

		foreach ( $groups as $groupName => $group ) {

			if ( in_array( $groupName, $exclude, true ) ) {
				continue;
			}

			$callback = array( $this, 'callback' );
			if ( isset( $group['callback'] ) && is_callable( $group['callback'] ) ) {
				$callback = $group['callback'];
			}

			foreach ( $inst as $key => $assignments ) {
				foreach ( $assignments as $occurance ) {
					list( $val, $line ) = $occurance;

					if ( ! in_array( $key, $group['keys'], true ) ) {
						continue;
					}

					$output = call_user_func( $callback, $key, $val, $line, $group );

					if ( ! isset( $output ) || false === $output ) {
						continue;
					} elseif ( true === $output ) {
						$message = $group['message'];
					} else {
						$message = $output;
					}

					if ( 'warning' === $group['type'] ) {
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
		} // End foreach().

	} // End process().

	/**
	 * Callback to process each confirmed key, to check value.
	 *
	 * This method must be extended to add the logic to check assignment value.
	 *
	 * @param  string $key   Array index / key.
	 * @param  mixed  $val   Assigned value.
	 * @param  int    $line  Token line.
	 * @param  array  $group Group definition.
	 * @return mixed         FALSE if no match, TRUE if matches, STRING if matches
	 *                       with custom error message passed to ->process().
	 */
	abstract public function callback( $key, $val, $line, $group );

} // End class.
