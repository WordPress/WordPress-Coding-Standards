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
use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\Utils\GetTokensAsString;
use PHPCSUtils\Utils\MessageHelper;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\Helpers\RulesetPropertyHelper;
use WordPressCS\WordPress\Sniff;

/**
 * Restricts array assignment of certain keys.
 *
 * @since 0.3.0
 * @since 0.10.0 Class became a proper abstract class. This was already the behaviour.
 *               Moved the file and renamed the class from
 *               `\WordPressCS\WordPress\Sniffs\Arrays\ArrayAssignmentRestrictionsSniff` to
 *               `\WordPressCS\WordPress\AbstractArrayAssignmentRestrictionsSniff`.
 */
abstract class AbstractArrayAssignmentRestrictionsSniff extends Sniff {

	/**
	 * Exclude groups.
	 *
	 * Example: 'foo,bar'
	 *
	 * @since 0.3.0
	 * @since 1.0.0 This property now expects to be passed an array.
	 *              Previously a comma-delimited string was expected.
	 *
	 * @var string[]
	 */
	public $exclude = array();

	/**
	 * Groups of variable data to check against.
	 * Don't use this in extended classes, override getGroups() instead.
	 * This is only used for Unit tests.
	 *
	 * @var array<string, array>
	 */
	public static $groups = array();

	/**
	 * Cache for the excluded groups information.
	 *
	 * @since 0.11.0
	 *
	 * @var array<string, bool>
	 */
	protected $excluded_groups = array();

	/**
	 * Cache for the group information.
	 *
	 * @since 0.13.0
	 *
	 * @var array<string, array>
	 */
	protected $groups_cache = array();

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		// Retrieve the groups only once and don't set up a listener if there are no groups.
		if ( false === $this->setup_groups() ) {
			return array();
		}

		return array(
			\T_DOUBLE_ARROW,
			\T_CLOSE_SQUARE_BRACKET,
			\T_CONSTANT_ENCAPSED_STRING,
			\T_DOUBLE_QUOTED_STRING,
		);
	}

	/**
	 * Groups of variables to restrict.
	 *
	 * This method should be overridden in extending classes.
	 *
	 * Example: groups => array(
	 *  'groupname' => array(
	 *      'type'    => 'error' | 'warning',
	 *      'message' => 'Descriptive error message. The error message will be passed the $key and $val of the current array assignment.',
	 *      'keys'    => array( 'key1', 'another_key' ),
	 *  )
	 * )
	 *
	 * @return array<string, array>
	 */
	abstract public function getGroups();

	/**
	 * Cache the groups.
	 *
	 * @since 0.13.0
	 *
	 * @return bool True if the groups were setup. False if not.
	 */
	protected function setup_groups() {
		$this->groups_cache = $this->getGroups();

		if ( empty( $this->groups_cache ) && empty( self::$groups ) ) {
			return false;
		}

		// Allow for adding extra unit tests.
		if ( ! empty( self::$groups ) ) {
			$this->groups_cache = array_merge( $this->groups_cache, self::$groups );
		}

		return true;
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {

		$this->excluded_groups = RulesetPropertyHelper::merge_custom_array( $this->exclude );
		if ( array_diff_key( $this->groups_cache, $this->excluded_groups ) === array() ) {
			// All groups have been excluded.
			// Don't remove the listener as the exclude property can be changed inline.
			return;
		}

		$token = $this->tokens[ $stackPtr ];

		if ( \T_CLOSE_SQUARE_BRACKET === $token['code'] ) {
			$equalPtr = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );
			if ( \T_EQUAL !== $this->tokens[ $equalPtr ]['code']
				&& \T_COALESCE_EQUAL !== $this->tokens[ $equalPtr ]['code']
			) {
				// This is not an assignment. Bow out.
				return;
			}
		}

		// Instances: Multi-dimensional array.
		$inst = array();

		/*
		 * Covers array assignments:
		 * `$foo = array( 'bar' => 'taz' );`
		 * `$foo['bar'] = $taz;`
		 */
		if ( \T_CLOSE_SQUARE_BRACKET === $token['code'] || \T_DOUBLE_ARROW === $token['code'] ) {
			$operator = $stackPtr; // T_DOUBLE_ARROW.
			if ( \T_CLOSE_SQUARE_BRACKET === $token['code'] ) {
				$operator = $equalPtr;
			}

			$keyIdx = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $stackPtr - 1 ), null, true );
			if ( isset( Tokens::$stringTokens[ $this->tokens[ $keyIdx ]['code'] ] )
				&& ! is_numeric( $this->tokens[ $keyIdx ]['content'] )
			) {
				$key      = TextStrings::stripQuotes( $this->tokens[ $keyIdx ]['content'] );
				$valStart = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $operator + 1 ), null, true );
				$valEnd   = BCFile::findEndOfStatement( $this->phpcsFile, $valStart, \T_COLON );
				if ( \T_COMMA === $this->tokens[ $valEnd ]['code']
					|| \T_SEMICOLON === $this->tokens[ $valEnd ]['code']
				) {
					// FindEndOfStatement includes the comma/semi-colon if that's the end of the statement.
					// That's not what we want (and inconsistent), so remove it.
					--$valEnd;
				}

				$val          = trim( GetTokensAsString::compact( $this->phpcsFile, $valStart, $valEnd, true ) );
				$inst[ $key ] = array(
					'value'  => $val,
					'line'   => $token['line'],
					'keyptr' => $keyIdx,
				);
			}
		} elseif ( isset( Tokens::$stringTokens[ $token['code'] ] ) ) {
			/*
			 * Covers assignments via query parameters: `$foo = 'bar=taz&other=thing';`.
			 */
			if ( preg_match_all( '#(?:^|&)([a-z_]+)=([^&]*)#i', TextStrings::stripQuotes( $token['content'] ), $matches ) <= 0 ) {
				return; // No assignments here, nothing to check.
			}

			foreach ( $matches[1] as $match_nr => $key ) {
				$inst[ $key ] = array(
					'value'  => $matches[2][ $match_nr ],
					'line'   => $token['line'],
					'keyptr' => $stackPtr,
				);
			}
		}

		if ( empty( $inst ) ) {
			return;
		}

		foreach ( $this->groups_cache as $groupName => $group ) {

			if ( isset( $this->excluded_groups[ $groupName ] ) ) {
				continue;
			}

			foreach ( $inst as $key => $assignment ) {
				if ( ! \in_array( $key, $group['keys'], true ) ) {
					continue;
				}

				$output = \call_user_func( array( $this, 'callback' ), $key, $assignment['value'], $assignment['line'], $group );

				if ( ! isset( $output ) || false === $output ) {
					continue;
				} elseif ( true === $output ) {
					$message = $group['message'];
				} else {
					$message = $output;
				}

				MessageHelper::addMessage(
					$this->phpcsFile,
					$message,
					$assignment['keyptr'],
					( 'error' === $group['type'] ),
					MessageHelper::stringToErrorcode( $groupName . '_' . $key ),
					array( $key, $assignment['value'] )
				);
			}
		}
	}

	/**
	 * Callback to process each confirmed key, to check value.
	 *
	 * This method must be extended to add the logic to check assignment value.
	 *
	 * @param string $key   Array index / key.
	 * @param mixed  $val   Assigned value.
	 * @param int    $line  Token line.
	 * @param array  $group Group definition.
	 *
	 * @return mixed FALSE if no match, TRUE if matches, STRING if matches
	 *               with custom error message passed to ->process().
	 */
	abstract public function callback( $key, $val, $line, $group );
}
