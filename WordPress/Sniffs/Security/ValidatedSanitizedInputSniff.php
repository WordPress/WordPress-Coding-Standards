<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\Security;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Utils\Context;
use PHPCSUtils\Utils\TextStrings;
use PHPCSUtils\Utils\Variables;
use WordPressCS\WordPress\Helpers\ContextHelper;
use WordPressCS\WordPress\Helpers\SanitizationHelperTrait;
use WordPressCS\WordPress\Helpers\ValidationHelper;
use WordPressCS\WordPress\Helpers\VariableHelper;
use WordPressCS\WordPress\Sniff;

/**
 * Flag any non-validated/sanitized input ( _GET / _POST / etc. ).
 *
 * @link https://github.com/WordPress/WordPress-Coding-Standards/issues/69
 *
 * @since 0.3.0
 * @since 0.4.0  This class now extends the WordPressCS native `Sniff` class.
 * @since 0.5.0  Method getArrayIndexKey() has been moved to the WordPressCS native `Sniff` class.
 * @since 0.13.0 Class name changed: this class is now namespaced.
 * @since 1.0.0  This sniff has been moved from the `VIP` category to the `Security` category.
 *
 * @uses \WordPressCS\WordPress\Helpers\SanitizationHelperTrait::$customSanitizingFunctions
 * @uses \WordPressCS\WordPress\Helpers\SanitizationHelperTrait::$customUnslashingSanitizingFunctions
 */
class ValidatedSanitizedInputSniff extends Sniff {

	use SanitizationHelperTrait;

	/**
	 * Check for validation functions for a variable within its own parenthesis only.
	 *
	 * @var boolean
	 */
	public $check_validation_in_scope_only = false;

	/**
	 * Superglobals for which the values will be slashed by WP.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_magic_quotes/
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, true>
	 */
	private $slashed_superglobals = array(
		'$_COOKIE'  => true,
		'$_GET'     => true,
		'$_POST'    => true,
		'$_REQUEST' => true,
		'$_SERVER'  => true,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			\T_VARIABLE,
			\T_DOUBLE_QUOTED_STRING,
			\T_HEREDOC,
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void
	 */
	public function process_token( $stackPtr ) {

		// Handling string interpolation.
		if ( \T_DOUBLE_QUOTED_STRING === $this->tokens[ $stackPtr ]['code']
			|| \T_HEREDOC === $this->tokens[ $stackPtr ]['code']
		) {
			// Retrieve all embeds, but use only the initial variable name part.
			$interpolated_variables = array_map(
				static function ( $embed ) {
					return preg_replace( '`^(\{?\$\{?\(?)([a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)(.*)$`', '$2', $embed );
				},
				TextStrings::getEmbeds( $this->tokens[ $stackPtr ]['content'] )
			);

			// Filter the embeds down to superglobals only.
			$interpolated_superglobals = array_filter(
				$interpolated_variables,
				static function ( $var_name ) {
					return ( 'GLOBALS' !== $var_name && Variables::isSuperglobalName( $var_name ) );
				}
			);

			foreach ( $interpolated_superglobals as $bad_variable ) {
				$this->phpcsFile->addError( 'Detected usage of a non-sanitized, non-validated input variable %s: %s', $stackPtr, 'InputNotValidatedNotSanitized', array( $bad_variable, $this->tokens[ $stackPtr ]['content'] ) );
			}

			return;
		}

		/* Handle variables */

		// Check if this is a superglobal we want to examine.
		if ( '$GLOBALS' === $this->tokens[ $stackPtr ]['content']
			|| Variables::isSuperglobalName( $this->tokens[ $stackPtr ]['content'] ) === false
		) {
			return;
		}

		// If the variable is being unset, we don't care about it.
		if ( Context::inUnset( $this->phpcsFile, $stackPtr ) ) {
			return;
		}

		// If we're overriding a superglobal with an assignment, no need to test.
		if ( VariableHelper::is_assignment( $this->phpcsFile, $stackPtr ) ) {
			return;
		}

		// This superglobal is being validated.
		if ( ContextHelper::is_in_isset_or_empty( $this->phpcsFile, $stackPtr ) ) {
			return;
		}

		$array_keys = VariableHelper::get_array_access_keys( $this->phpcsFile, $stackPtr );

		if ( empty( $array_keys ) ) {
			return;
		}

		$error_data = array( $this->tokens[ $stackPtr ]['content'] . '[' . implode( '][', $array_keys ) . ']' );

		/*
		 * Check for validation first.
		 */
		$validated = false;

		for ( $i = ( $stackPtr + 1 ); $i < $this->phpcsFile->numTokens; $i++ ) {
			if ( isset( Tokens::$emptyTokens[ $this->tokens[ $i ]['code'] ] ) ) {
				continue;
			}

			if ( \T_OPEN_SQUARE_BRACKET === $this->tokens[ $i ]['code']
				&& isset( $this->tokens[ $i ]['bracket_closer'] )
			) {
				// Skip over array keys.
				$i = $this->tokens[ $i ]['bracket_closer'];
				continue;
			}

			if ( \T_COALESCE === $this->tokens[ $i ]['code'] ) {
				$validated = true;
			}

			// Anything else means this is not a validation coalesce.
			break;
		}

		if ( false === $validated ) {
			$validated = ValidationHelper::is_validated( $this->phpcsFile, $stackPtr, $array_keys, $this->check_validation_in_scope_only );
		}

		if ( false === $validated ) {
			$this->phpcsFile->addError(
				'Detected usage of a possibly undefined superglobal array index: %s. Use isset() or empty() to check the index exists before using it',
				$stackPtr,
				'InputNotValidated',
				$error_data
			);
		}

		// If this variable is being tested with one of the `is_..()` functions, sanitization isn't needed.
		if ( ContextHelper::is_in_type_test( $this->phpcsFile, $stackPtr ) ) {
			return;
		}

		// If this is a comparison ('a' == $_POST['foo']), sanitization isn't needed.
		if ( VariableHelper::is_comparison( $this->phpcsFile, $stackPtr, false ) ) {
			return;
		}

		// If this is a comparison using the array comparison functions, sanitization isn't needed.
		if ( ContextHelper::is_in_array_comparison( $this->phpcsFile, $stackPtr ) ) {
			return;
		}

		// Now look for sanitizing functions.
		if ( ! $this->is_sanitized( $this->phpcsFile, $stackPtr, array( $this, 'add_unslash_error' ) ) ) {
			$this->phpcsFile->addError(
				'Detected usage of a non-sanitized input variable: %s',
				$stackPtr,
				'InputNotSanitized',
				$error_data
			);
		}
	}

	/**
	 * Add an error for missing use of unslashing.
	 *
	 * @since 0.5.0
	 * @since 3.0.0 - Moved from the `Sniff` class to this class.
	 *              - The `$phpcsFile` parameter was added.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The index of the token in the stack
	 *                                               which is missing unslashing.
	 *
	 * @return void
	 */
	public function add_unslash_error( File $phpcsFile, $stackPtr ) {
		$tokens   = $phpcsFile->getTokens();
		$var_name = $tokens[ $stackPtr ]['content'];

		if ( isset( $this->slashed_superglobals[ $var_name ] ) === false ) {
			// WP doesn't slash these, so they don't need unslashing.
			return;
		}

		// We know there will be array keys as that's checked in the process_token() method.
		$array_keys = VariableHelper::get_array_access_keys( $phpcsFile, $stackPtr );
		$error_data = array( $var_name . '[' . implode( '][', $array_keys ) . ']' );

		$phpcsFile->addError(
			'%s not unslashed before sanitization. Use wp_unslash() or similar',
			$stackPtr,
			'MissingUnslash',
			$error_data
		);
	}
}
