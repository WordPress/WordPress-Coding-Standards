<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Helpers;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Utils\Context;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\Helpers\ArrayWalkingFunctionsHelper;
use WordPressCS\WordPress\Helpers\ContextHelper;
use WordPressCS\WordPress\Helpers\RulesetPropertyHelper;
use WordPressCS\WordPress\Helpers\UnslashingFunctionsHelper;

/**
 * Helper functions and function lists for checking whether a sanitizing function is being used.
 *
 * Any sniff class which incorporates this trait will automatically support the
 * following `public` properties which can be changed from within a custom ruleset:
 * - `customSanitizingFunctions`.
 * - `customUnslashingSanitizingFunctions`
 *
 * ---------------------------------------------------------------------------------------------
 * This trait is only intended for internal use by WordPressCS and is not part of the public API.
 * This also means that it has no promise of backward compatibility. Use at your own risk.
 * ---------------------------------------------------------------------------------------------
 *
 * @internal
 *
 * @since 3.0.0 The properties in this trait were previously contained partially in the
 *              `WordPressCS\WordPress\Sniff` class and partially in the `NonceVerificationSniff`
 *              and the `ValidatedSanitizedInputSniff` classes and have been moved here.
 *              The pre-existing methods in this trait were previously contained in the
 *              `WordPressCS\WordPress\Sniff` class and have been moved here.
 */
trait SanitizationHelperTrait {

	/**
	 * Custom list of functions that sanitize the values passed to them.
	 *
	 * @since 0.5.0
	 * @since 3.0.0 Moved from the NonceVerification and the ValidatedSanitizedInput sniff classes to this trait.
	 *
	 * @var string[]
	 */
	public $customSanitizingFunctions = array();

	/**
	 * Custom sanitizing functions that implicitly unslash the values passed to them.
	 *
	 * @since 0.5.0
	 * @since 3.0.0 Moved from the NonceVerification and the ValidatedSanitizedInput sniff classes to this trait.
	 *
	 * @var string[]
	 */
	public $customUnslashingSanitizingFunctions = array();

	/**
	 * Functions that sanitize values.
	 *
	 * This list is complementary to the `$unslashingSanitizingFunctions`
	 * list.
	 * Sanitizing functions should be added to this list if they do *not*
	 * implicitly unslash data and to the `$unslashingsanitizingFunctions`
	 * list if they do.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 * @since 3.0.0  - Moved from the Sniff class to this trait.
	 *               - Visibility changed from protected to private.
	 *
	 * @var array<string, true>
	 */
	private $sanitizingFunctions = array(
		'_wp_handle_upload'          => true,
		'esc_url_raw'                => true,
		'filter_input'               => true,
		'filter_var'                 => true,
		'hash_equals'                => true,
		'is_email'                   => true,
		'number_format'              => true,
		'sanitize_bookmark_field'    => true,
		'sanitize_bookmark'          => true,
		'sanitize_email'             => true,
		'sanitize_file_name'         => true,
		'sanitize_hex_color_no_hash' => true,
		'sanitize_hex_color'         => true,
		'sanitize_html_class'        => true,
		'sanitize_meta'              => true,
		'sanitize_mime_type'         => true,
		'sanitize_option'            => true,
		'sanitize_sql_orderby'       => true,
		'sanitize_term_field'        => true,
		'sanitize_term'              => true,
		'sanitize_text_field'        => true,
		'sanitize_textarea_field'    => true,
		'sanitize_title_for_query'   => true,
		'sanitize_title_with_dashes' => true,
		'sanitize_title'             => true,
		'sanitize_url'               => true,
		'sanitize_user_field'        => true,
		'sanitize_user'              => true,
		'validate_file'              => true,
		'wp_handle_sideload'         => true,
		'wp_handle_upload'           => true,
		'wp_kses_allowed_html'       => true,
		'wp_kses_data'               => true,
		'wp_kses_one_attr'           => true,
		'wp_kses_post'               => true,
		'wp_kses'                    => true,
		'wp_parse_id_list'           => true,
		'wp_redirect'                => true,
		'wp_safe_redirect'           => true,
		'wp_sanitize_redirect'       => true,
		'wp_strip_all_tags'          => true,
	);

	/**
	 * Sanitizing functions that implicitly unslash the data passed to them.
	 *
	 * This list is complementary to the `$sanitizingFunctions` list.
	 * Sanitizing functions should be added to this list if they also
	 * implicitely unslash data and to the `$sanitizingFunctions` list
	 * if they don't.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 * @since 3.0.0  - Moved from the Sniff class to this trait.
	 *               - Visibility changed from protected to private.
	 *
	 * @var array<string, bool>
	 */
	private $unslashingSanitizingFunctions = array(
		'absint'       => true,
		'boolval'      => true,
		'count'        => true,
		'doubleval'    => true,
		'floatval'     => true,
		'intval'       => true,
		'sanitize_key' => true,
		'sizeof'       => true,
	);

	/**
	 * Cache of previously added custom functions.
	 *
	 * Prevents having to do the same merges over and over again.
	 *
	 * @since 0.4.0
	 * @since 0.11.0 - Changed from public static to protected non-static.
	 *               - Changed the format from simple bool to array.
	 * @since 3.0.0  - Moved from the NonceVerification and the ValidatedSanitizedInput sniff classes to this class.
	 *               - Visibility changed from protected to private.
	 *
	 * @var array<string, string[]>
	 */
	private $addedCustomSanitizingFunctions = array(
		'sanitize'        => array(),
		'unslashsanitize' => array(),
	);

	/**
	 * Combined list of WP/PHP native and custom sanitizing functions.
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, bool>
	 */
	private $allSanitizingFunctions = array();

	/**
	 * Combined list of WP/PHP native and custom sanitizing and unslashing functions.
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, bool>
	 */
	private $allUnslashingSanitizingFunctions = array();

	/**
	 * Retrieve a list of all known sanitizing functions.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, bool>
	 */
	final public function get_sanitizing_functions() {
		if ( array() === $this->allSanitizingFunctions
			|| $this->customSanitizingFunctions !== $this->addedCustomSanitizingFunctions['sanitize']
		) {
			$this->allSanitizingFunctions = RulesetPropertyHelper::merge_custom_array(
				$this->customSanitizingFunctions,
				$this->sanitizingFunctions
			);

			$this->addedCustomSanitizingFunctions['sanitize'] = $this->customSanitizingFunctions;
		}

		return $this->allSanitizingFunctions;
	}

	/**
	 * Retrieve a list of all known sanitizing and unslashing functions.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, bool>
	 */
	final public function get_sanitizing_and_unslashing_functions() {
		if ( array() === $this->allUnslashingSanitizingFunctions
			|| $this->customUnslashingSanitizingFunctions !== $this->addedCustomSanitizingFunctions['unslashsanitize']
		) {
			$this->allUnslashingSanitizingFunctions = RulesetPropertyHelper::merge_custom_array(
				$this->customUnslashingSanitizingFunctions,
				$this->unslashingSanitizingFunctions
			);

			$this->addedCustomSanitizingFunctions['unslashsanitize'] = $this->customUnslashingSanitizingFunctions;
		}

		return $this->allUnslashingSanitizingFunctions;
	}

	/**
	 * Check if a particular function is regarded as a sanitizing function.
	 *
	 * @since 3.0.0
	 *
	 * @param string $functionName The name of the function to check.
	 *
	 * @return bool
	 */
	final public function is_sanitizing_function( $functionName ) {
		return isset( $this->get_sanitizing_functions()[ strtolower( $functionName ) ] );
	}

	/**
	 * Check if a particular function is regarded as a sanitizing and unslashing function.
	 *
	 * @since 3.0.0
	 *
	 * @param string $functionName The name of the function to check.
	 *
	 * @return bool
	 */
	final public function is_sanitizing_and_unslashing_function( $functionName ) {
		return isset( $this->get_sanitizing_and_unslashing_functions()[ strtolower( $functionName ) ] );
	}

	/**
	 * Check if something is only being sanitized.
	 *
	 * @since 0.5.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The method visibility was changed from `protected` to `public`.
	 *              - The `$phpcsFile` parameter was added.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The index of the token in the stack.
	 *
	 * @return bool Whether the token is only within a sanitization.
	 */
	final public function is_only_sanitized( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		// If it isn't being sanitized at all.
		if ( ! $this->is_sanitized( $phpcsFile, $stackPtr ) ) {
			return false;
		}

		// If the token isn't in parentheses, we know the value must have only been casted, because
		// is_sanitized() would have returned `false` otherwise.
		if ( ! isset( $tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			return true;
		}

		// At this point we're expecting the value to have not been casted. If it
		// was, it wasn't *only* casted, because it's also in a function.
		if ( ContextHelper::is_safe_casted( $phpcsFile, $stackPtr ) ) {
			return false;
		}

		// The only parentheses should belong to the sanitizing function. If there's
		// more than one set, this isn't *only* sanitization.
		return ( \count( $tokens[ $stackPtr ]['nested_parenthesis'] ) === 1 );
	}

	/**
	 * Check if something is being sanitized.
	 *
	 * @since 0.5.0
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - The method visibility was changed from `protected` to `public`.
	 *              - The `$phpcsFile` parameter was added.
	 *              - The $require_unslash parameter has been changed from
	 *                a boolean toggle to a ?callable $unslash_callback parameter to
	 *                allow a sniff calling this method to handle their "unslashing"
	 *                related messaging itself.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile        The file being scanned.
	 * @param int                         $stackPtr         The index of the token in the stack.
	 * @param callable|null               $unslash_callback Optional. When passed, this method will check if
	 *                                                      an unslashing function is used on the variable before
	 *                                                      sanitization and if not, the callback will be called
	 *                                                      to handle the missing unslashing.
	 *                                                      The callback will receive the $phpcsFile object and
	 *                                                      the $stackPtr.
	 *                                                      When not passed or `null`, this method will **not**
	 *                                                      check for unslashing issues.
	 *                                                      Defaults to `null` (skip unslashing checks).
	 *
	 * @return bool Whether the token is being sanitized.
	 */
	final public function is_sanitized( File $phpcsFile, $stackPtr, $unslash_callback = null ) {
		$tokens          = $phpcsFile->getTokens();
		$require_unslash = is_callable( $unslash_callback );

		if ( isset( $tokens[ $stackPtr ] ) === false ) {
			return false;
		}

		// If the variable is just being unset, the value isn't used at all, so it's safe.
		if ( Context::inUnset( $phpcsFile, $stackPtr ) ) {
			return true;
		}

		// First we check if it is being casted to a safe value.
		if ( ContextHelper::is_safe_casted( $phpcsFile, $stackPtr ) ) {
			return true;
		}

		// If this isn't within a function call, we know already that it's not safe.
		if ( ! isset( $tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			if ( $require_unslash ) {
				call_user_func( $unslash_callback, $phpcsFile, $stackPtr );
			}

			return false;
		}

		$sanitizing_functions  = $this->get_sanitizing_functions();
		$sanitizing_functions += $this->get_sanitizing_and_unslashing_functions();
		$sanitizing_functions += ArrayWalkingFunctionsHelper::get_functions();
		$valid_functions       = $sanitizing_functions + UnslashingFunctionsHelper::get_functions();

		// Get the function that it's in.
		$functionPtr = ContextHelper::is_in_function_call( $phpcsFile, $stackPtr, $valid_functions );

		// If this isn't a call to one of the valid functions, it sure isn't a sanitizing function.
		if ( false === $functionPtr ) {
			if ( true === $require_unslash ) {
				call_user_func( $unslash_callback, $phpcsFile, $stackPtr );
			}

			return false;
		}

		$functionName = $tokens[ $functionPtr ]['content'];

		// Check if an unslashing function is being used.
		$is_unslashed = false;
		if ( UnslashingFunctionsHelper::is_unslashing_function( $functionName ) ) {
			$is_unslashed = true;

			// Check whether this function call is wrapped within a sanitizing function.
			$higherFunctionPtr = ContextHelper::is_in_function_call( $phpcsFile, $functionPtr, $sanitizing_functions );

			// If there is no other valid function being used, this value is unsanitized.
			if ( false === $higherFunctionPtr ) {
				return false;
			}

			$functionPtr  = $higherFunctionPtr;
			$functionName = $tokens[ $functionPtr ]['content'];
		}

		// Arrays might be sanitized via an array walking function using a callback.
		if ( ArrayWalkingFunctionsHelper::is_array_walking_function( $functionName ) ) {
			// Get the callback parameter.
			$callback = ArrayWalkingFunctionsHelper::get_callback_parameter( $phpcsFile, $functionPtr );

			if ( ! empty( $callback ) ) {
				/*
				 * If this is a function callback (not a method callback array) and we're able
				 * to resolve the function name, do so.
				 */
				$first_non_empty = $phpcsFile->findNext(
					Tokens::$emptyTokens,
					$callback['start'],
					( $callback['end'] + 1 ),
					true
				);

				if ( false !== $first_non_empty && \T_CONSTANT_ENCAPSED_STRING === $tokens[ $first_non_empty ]['code'] ) {
					$functionName = TextStrings::stripQuotes( $tokens[ $first_non_empty ]['content'] );
				}
			}
		}

		// If slashing is required, give an error.
		if ( false === $is_unslashed
			&& true === $require_unslash
			&& ! $this->is_sanitizing_and_unslashing_function( $functionName )
		) {
			call_user_func( $unslash_callback, $phpcsFile, $stackPtr );
		}

		// Check if this is a sanitizing function.
		return ( $this->is_sanitizing_function( $functionName ) || $this->is_sanitizing_and_unslashing_function( $functionName ) );
	}
}
