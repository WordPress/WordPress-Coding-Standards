<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\GetTokensAsString;
use PHPCSUtils\Utils\Namespaces;
use WordPressCS\WordPress\AbstractFunctionRestrictionsSniff;
use WordPressCS\WordPress\Helpers\RulesetPropertyHelper;

/**
 * Restricts usage of some classes.
 *
 * @since 0.10.0
 */
abstract class AbstractClassRestrictionsSniff extends AbstractFunctionRestrictionsSniff {

	/**
	 * Regex pattern with placeholder for the class names.
	 *
	 * @var string
	 */
	protected $regex_pattern = '`^\\\\(?:%s)$`i';

	/**
	 * Temporary storage for retrieved class name.
	 *
	 * @var string
	 */
	protected $classname;

	/**
	 * Groups of classes to restrict.
	 *
	 * This method should be overridden in extending classes.
	 *
	 * Example: groups => array(
	 *  'lambda' => array(
	 *      'type'    => 'error' | 'warning',
	 *      'message' => 'Avoid direct calls to the database.',
	 *      'classes' => array( 'PDO', '\Namespace\Classname' ),
	 *  )
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
			\T_DOUBLE_COLON,
			\T_NEW,
			\T_EXTENDS,
			\T_IMPLEMENTS,
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * {@internal Unlike in the `AbstractFunctionRestrictionsSniff`,
	 *            we can't do a preliminary check on classes as at this point
	 *            we don't know the class name yet.}}
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {
		// Reset the temporary storage before processing the token.
		unset( $this->classname );

		$this->excluded_groups = RulesetPropertyHelper::merge_custom_array( $this->exclude );
		if ( array_diff_key( $this->groups, $this->excluded_groups ) === array() ) {
			// All groups have been excluded.
			// Don't remove the listener as the exclude property can be changed inline.
			return;
		}

		if ( true === $this->is_targetted_token( $stackPtr ) ) {
			return $this->check_for_matches( $stackPtr );
		}
	}

	/**
	 * Determine if we have a valid classname for the target token.
	 *
	 * @since 0.11.0 This logic was originally contained in the `process()` method.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return bool
	 */
	public function is_targetted_token( $stackPtr ) {

		$token     = $this->tokens[ $stackPtr ];
		$classname = '';

		if ( \in_array( $token['code'], array( \T_NEW, \T_EXTENDS, \T_IMPLEMENTS ), true ) ) {
			if ( \T_NEW === $token['code'] ) {
				$nameEnd = ( $this->phpcsFile->findNext( array( \T_OPEN_PARENTHESIS, \T_WHITESPACE, \T_SEMICOLON, \T_CLOSE_PARENTHESIS, \T_CLOSE_TAG ), ( $stackPtr + 2 ) ) - 1 );
			} else {
				$nameEnd = ( $this->phpcsFile->findNext( array( \T_CLOSE_CURLY_BRACKET, \T_WHITESPACE ), ( $stackPtr + 2 ) ) - 1 );
			}

			$classname = GetTokensAsString::noEmpties( $this->phpcsFile, ( $stackPtr + 2 ), $nameEnd );
			$classname = $this->get_namespaced_classname( $classname, ( $stackPtr - 1 ) );
		}

		if ( \T_DOUBLE_COLON === $token['code'] ) {
			$nameEnd = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $stackPtr - 1 ), null, true );
			if ( \T_STRING !== $this->tokens[ $nameEnd ]['code'] ) {
				// Hierarchy keyword or object stored in variable.
				return false;
			}

			$nameStart = ( $this->phpcsFile->findPrevious( Collections::namespacedNameTokens(), ( $nameEnd - 1 ), null, true ) + 1 );
			$classname = GetTokensAsString::noEmpties( $this->phpcsFile, $nameStart, $nameEnd );
			$classname = $this->get_namespaced_classname( $classname, ( $nameStart - 1 ) );
		}

		// Stop if we couldn't determine a classname.
		if ( empty( $classname ) ) {
			return false;
		}

		// Nothing to do if one of the hierarchy keywords - 'parent', 'self' or 'static' - is used.
		if ( \in_array( strtolower( $classname ), array( '\parent', '\self', '\static' ), true ) ) {
			return false;
		}

		$this->classname = $classname;
		return true;
	}

	/**
	 * Verify if the current token is one of the targetted classes.
	 *
	 * @since 0.11.0 Split out from the `process()` method.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function check_for_matches( $stackPtr ) {
		$skip_to = array();

		foreach ( $this->groups as $groupName => $group ) {

			if ( isset( $this->excluded_groups[ $groupName ] ) ) {
				continue;
			}

			if ( preg_match( $group['regex'], $this->classname ) === 1 ) {
				$skip_to[] = $this->process_matched_token( $stackPtr, $groupName, $this->classname );
			}
		}

		if ( empty( $skip_to ) || min( $skip_to ) === 0 ) {
			return;
		}

		return min( $skip_to );
	}

	/**
	 * Process a matched token.
	 *
	 * @since 0.11.0 Split out from the `process()` method.
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched
	 *                                in it original case.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 *
	 * @phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod.Found
	 */
	public function process_matched_token( $stackPtr, $group_name, $matched_content ) {
		parent::process_matched_token( $stackPtr, $group_name, $matched_content );
	}
	// phpcs:enable

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
		return parent::prepare_name_for_regex( $classname );
	}

	/**
	 * See if the classname was found in a namespaced file and if so, add the namespace to the classname.
	 *
	 * @param string $classname   The full classname as found.
	 * @param int    $search_from The token position to search up from.
	 * @return string Classname, potentially prefixed with the namespace.
	 */
	protected function get_namespaced_classname( $classname, $search_from ) {
		// Don't do anything if this is already a fully qualified classname.
		if ( empty( $classname ) || '\\' === $classname[0] ) {
			return $classname;
		}

		// Remove the namespace keyword if used.
		if ( 0 === stripos( $classname, 'namespace\\' ) ) {
			$classname = substr( $classname, 10 );
		}

		$namespace = Namespaces::determineNamespace( $this->phpcsFile, $search_from );
		if ( '' === $namespace ) {
			// No namespace keyword found at all, so global namespace.
			$classname = '\\' . $classname;
		} else {
			$classname = '\\' . $namespace . '\\' . $classname;
		}

		return $classname;
	}
}
