<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Restricts usage of some classes.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.10.0
 */
abstract class WordPress_AbstractClassRestrictionsSniff extends WordPress_AbstractFunctionRestrictionsSniff {

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
	protected $regex_pattern = '`^\\\\(?:%s)$`i';

	/**
	 * Groups of classes to restrict.
	 *
	 * This method should be overridden in extending classes.
	 *
	 * Example: groups => array(
	 * 	'lambda' => array(
	 * 		'type'      => 'error' | 'warning',
	 * 		'message'   => 'Avoid direct calls to the database.',
	 * 		'classes'   => array( 'PDO', '\Namespace\Classname' ),
	 * 	)
	 * )
	 *
	 * You can use * wildcards to target a group of (namespaced) classes.
	 * Aliased namespaces (use ..) are currently not supported.
	 *
	 * Documented here for clarity. Not (re)defined as it is already defined in the parent class.
	 *
	 * @return array
	 *
	abstract public function getGroups();
	 */

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		// Prepare the function group regular expressions only once.
		if ( false === $this->setup_groups( 'classes' ) ) {
			return array();
		}

		return array(
			T_DOUBLE_COLON,
			T_NEW,
			T_EXTENDS,
			T_IMPLEMENTS,
		);

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
		$tokens    = $phpcsFile->getTokens();
		$token     = $tokens[ $stackPtr ];
		$classname = '';

		if ( in_array( $token['code'], array( T_NEW, T_EXTENDS, T_IMPLEMENTS ), true ) ) {
			if ( T_NEW === $token['code'] ) {
				$nameEnd   = ( $phpcsFile->findNext( array( T_OPEN_PARENTHESIS, T_WHITESPACE, T_SEMICOLON, T_OBJECT_OPERATOR ), ( $stackPtr + 2 ) ) - 1 );
			} else {
				$nameEnd   = ( $phpcsFile->findNext( array( T_CLOSE_CURLY_BRACKET, T_WHITESPACE ), ( $stackPtr + 2 ) ) - 1 );
			}

			$length    = ( $nameEnd - ( $stackPtr + 1 ) );
			$classname = $phpcsFile->getTokensAsString( ( $stackPtr + 2 ), $length );

			if ( T_NS_SEPARATOR !== $tokens[ ( $stackPtr + 2 ) ]['code'] ) {
				$classname = $this->get_namespaced_classname( $classname, $phpcsFile, $tokens, ( $stackPtr - 1 ) );
			}
		}

		if ( T_DOUBLE_COLON === $token['code'] ) {
			$nameEnd   = $phpcsFile->findPrevious( array( T_STRING ), ( $stackPtr - 1 ) );
			$nameStart = ( $phpcsFile->findPrevious( array( T_STRING, T_NS_SEPARATOR, T_NAMESPACE ), ( $nameEnd - 1 ), null, true, null, true ) + 1 );
			$length    = ( $nameEnd - ( $nameStart - 1) );
			$classname = $phpcsFile->getTokensAsString( $nameStart, $length );

			if ( T_NS_SEPARATOR !== $tokens[ $nameStart ]['code'] ) {
				$classname = $this->get_namespaced_classname( $classname, $phpcsFile, $tokens, ( $nameStart - 1 ) );
			}
		}

		// Stop if we couldn't determine a classname.
		if ( empty( $classname ) ) {
			return;
		}

		// Nothing to do if 'parent', 'self' or 'static'.
		if ( in_array( $classname, array( 'parent', 'self', 'static' ), true ) ) {
			return;
		}

		$exclude   = explode( ',', $this->exclude );

		foreach ( $this->groups as $groupName => $group ) {

			if ( in_array( $groupName, $exclude, true ) ) {
				continue;
			}

			if ( preg_match( $group['regex'], $classname ) < 1 ) {
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
				array( $classname )
			);

		}

	} // end process()

	/**
	 * Prepare the class name for use in a regular expression.
	 *
	 * The getGroups() method allows for providing class names with a wildcard * to target
	 * a group of classes within a namespace. It also allows for providing class names as
	 * 'ordinary' names or prefixed with one or more namespaces.
	 * This prepare routine takes that into account while still safely escaping the
	 * class name for use in a regular expression.
	 *
	 * @param string $classname Class name, potentially prefixed with namespaces.
	 * @return string Regex escaped class name.
	 */
	protected function prepare_name_for_regex( $classname ) {
		$classname = trim( $classname, '\\' ); // Make sure all classnames have a \ prefix, but only one.
		$classname = str_replace( array( '.*', '*' ) , '#', $classname ); // Replace wildcards with placeholder.
		$classname = preg_quote( $classname, '`' );
		$classname = str_replace( '#', '.*', $classname ); // Replace placeholder with regex wildcard.

		return $classname;
	}

	/**
	 * See if the classname was found in a namespaced file and if so, add the namespace to the classname.
	 *
	 * @param string               $classname   The full classname as found.
	 * @param PHP_CodeSniffer_File $phpcsFile   The file being scanned.
	 * @param array                $tokens      The token stack for this file.
	 * @param int                  $search_from The token position to search up from.
	 * @return string Classname, potentially prefixed with the namespace.
	 */
	protected function get_namespaced_classname( $classname, PHP_CodeSniffer_File $phpcsFile, $tokens, $search_from ) {
		// Don't do anything if this is already a fully qualified classname.
		if ( empty( $classname ) || '\\' === $classname[0] ) {
			return $classname;
		}

		// Remove the namespace keyword if used.
		if ( 0 === strpos( $classname, 'namespace\\' ) ) {
			$classname = substr( $classname, 10 );
		}

		$namespace_keyword = $phpcsFile->findPrevious( T_NAMESPACE, $search_from );
		if ( false === $namespace_keyword ) {
			// No namespace keyword found at all, so global namespace.
			$classname = '\\' . $classname;
		} else {
			$namespace = $this->determine_namespace( $phpcsFile, $tokens, $search_from );

			if ( ! empty( $namespace ) ) {
				$classname = '\\' . $namespace . '\\' . $classname;
			} else {
				// No actual namespace found, so global namespace.
				$classname = '\\' . $classname;
			}
		}

		return $classname;
	}

	/**
	 * Determine the namespace name based on whether this is a scoped namespace or a file namespace.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile   The file being scanned.
	 * @param array                $tokens      The token stack for this file.
	 * @param int                  $search_from The token position to search up from.
	 * @return string Namespace name or empty string if it couldn't be determined or no namespace applied.
	 */
	protected function determine_namespace( PHP_CodeSniffer_File $phpcsFile, $tokens, $search_from ) {
		$namespace = '';

		if ( ! empty( $tokens[ $search_from ]['conditions'] ) ) {
			// Scoped namespace {}.
			foreach ( $tokens[ $search_from ]['conditions'] as $pointer => $type ) {
				if ( T_NAMESPACE === $type && $tokens[ $pointer ]['scope_closer'] > $search_from ) {
					$namespace = $this->get_namespace_name( $phpcsFile, $tokens, $pointer );
				}
				break; // We only need to check the highest level condition.
			}
		} else {
			// Let's see if we can find a file namespace instead.
			$first = $phpcsFile->findNext( array( T_NAMESPACE ), 0, $search_from );

			if ( empty( $tokens[ $first ]['scope_condition'] ) ) {
				$namespace = $this->get_namespace_name( $phpcsFile, $tokens, $first );
			}
		}

		return $namespace;
	}

	/**
	 * Get the namespace name based on the position of the namespace scope opener.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile         The file being scanned.
	 * @param array                $tokens            The token stack for this file.
	 * @param int                  $t_namespace_token The token position to search from.
	 * @return string Namespace name.
	 */
	protected function get_namespace_name( PHP_CodeSniffer_File $phpcsFile, $tokens, $t_namespace_token ) {
		$nameEnd = ( $phpcsFile->findNext( array( T_OPEN_CURLY_BRACKET, T_WHITESPACE, T_SEMICOLON ), ( $t_namespace_token + 2 ) ) - 1 );
		$length    = ( $nameEnd - ( $t_namespace_token + 1 ) );
		$namespace = $phpcsFile->getTokensAsString( ( $t_namespace_token + 2 ), $length );

		return $namespace;
	}

} // End class.
