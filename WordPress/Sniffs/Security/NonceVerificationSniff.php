<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\Security;

use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Conditions;
use PHPCSUtils\Utils\Lists;
use PHPCSUtils\Utils\MessageHelper;
use PHPCSUtils\Utils\Scopes;
use WordPressCS\WordPress\Helpers\ContextHelper;
use WordPressCS\WordPress\Helpers\RulesetPropertyHelper;
use WordPressCS\WordPress\Helpers\UnslashingFunctionsHelper;
use WordPressCS\WordPress\Helpers\VariableHelper;
use WordPressCS\WordPress\Sniff;

/**
 * Checks that nonce verification accompanies form processing.
 *
 * @link    https://developer.wordpress.org/plugins/security/nonces/ Nonces on Plugin Developer Handbook
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.5.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   1.0.0  This sniff has been moved from the `CSRF` category to the `Security` category.
 *
 * @uses    \WordPressCS\WordPress\Helpers\SanitizingFunctionsTrait::$customSanitizingFunctions
 * @uses    \WordPressCS\WordPress\Helpers\SanitizingFunctionsTrait::$customUnslashingSanitizingFunctions
 */
class NonceVerificationSniff extends Sniff {

	/**
	 * Superglobals to notify about when not accompanied by an nonce check.
	 *
	 * A value of `true` results in an error. A value of `false` in a warning.
	 *
	 * @since 0.12.0
	 *
	 * @var array
	 */
	protected $superglobals = array(
		'$_POST'    => true,
		'$_FILES'   => true,
		'$_GET'     => false,
		'$_REQUEST' => false,
	);

	/**
	 * Custom list of functions which verify nonces.
	 *
	 * @since 0.5.0
	 *
	 * @var string|string[]
	 */
	public $customNonceVerificationFunctions = array();

	/**
	 * List of the functions which verify nonces.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 * @since 3.0.0  - Moved from the generic `Sniff` class to this class.
	 *               - Visibility changed from `protected` to `private.
	 *
	 * @var array
	 */
	private $nonceVerificationFunctions = array(
		'wp_verify_nonce'     => true,
		'check_admin_referer' => true,
		'check_ajax_referer'  => true,
	);

	/**
	 * Cache of previously added custom functions.
	 *
	 * Prevents having to do the same merges over and over again.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 - Changed from public static to protected non-static.
	 *               - Changed the format from simple bool to array.
	 * @since 3.0.0  - Property rename from `$addedCustomFunctions` to `$addedCustomNonceFunctions`.
	 *               - Visibility changed from `protected` to `private.
	 *               - Format changed from a multi-dimensional array to a single-dimensional array.
	 *
	 * @var array
	 */
	private $addedCustomNonceFunctions = array();

	/**
	 * Information on the all scopes that were checked to find a nonce verification in a particular file.
	 *
	 * The array will be in the following format:
	 * ```
	 * array(
	 *   'file'  => (string) The name of the file.
	 *   'cache' => (array) array(
	 *     # => array(             The key is the token pointer to the "start" position.
	 *       'end'   => (int)      The token pointer to the "end" position.
	 *       'nonce' => (int|bool) The token pointer where n nonce check
	 *                             was found, or false if none was found.
	 *     )
	 *   )
	 * )
	 * ```
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, mixed>
	 */
	private $cached_results;

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		$targets  = array( \T_VARIABLE => \T_VARIABLE );
		$targets += Collections::listOpenTokensBC(); // We need to skip over lists.

		return $targets;
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {
		// Skip over lists as whatever is in those will always be assignments.
		if ( isset( Collections::listOpenTokensBC()[ $this->tokens[ $stackPtr ]['code'] ] ) ) {
			$open_close = Lists::getOpenClose( $this->phpcsFile, $stackPtr );
			$skip_to    = $stackPtr;
			if ( false !== $open_close ) {
				$skip_to = $open_close['closer'];
			}

			return $skip_to;
		}

		if ( ! isset( $this->superglobals[ $this->tokens[ $stackPtr ]['content'] ] ) ) {
			return;
		}

		if ( Scopes::isOOProperty( $this->phpcsFile, $stackPtr ) ) {
			// Property with the same name as a superglobal. Not our target.
			return;
		}

		if ( VariableHelper::is_assignment( $this->phpcsFile, $stackPtr ) ) {
			return;
		}

		$this->mergeFunctionLists();

		if ( $this->has_nonce_check( $stackPtr ) ) {
			return;
		}

		$error_code = 'Missing';
		if ( false === $this->superglobals[ $this->tokens[ $stackPtr ]['content'] ] ) {
			$error_code = 'Recommended';
		}

		// If we're still here, no nonce-verification function was found.
		MessageHelper::addMessage(
			$this->phpcsFile,
			'Processing form data without nonce verification.',
			$stackPtr,
			$this->superglobals[ $this->tokens[ $stackPtr ]['content'] ],
			$error_code
		);
	}

	/**
	 * Check if this token has an associated nonce check.
	 *
	 * @since 0.5.0
	 * @since 3.0.0 - Moved from the generic `Sniff` class to this class.
	 *              - Visibility changed from `protected` to `private.
	 *
	 * @param int $stackPtr The position of the current token in the stack of tokens.
	 *
	 * @return bool
	 */
	private function has_nonce_check( $stackPtr ) {
		$start = 0;
		$end   = $stackPtr;

		// If we're in a function, only look inside of it.
		$functionPtr = Conditions::getLastCondition( $this->phpcsFile, $stackPtr, array( \T_FUNCTION, \T_CLOSURE ) );
		if ( false !== $functionPtr ) {
			$start = $this->tokens[ $functionPtr ]['scope_opener'];
		}

		$allow_nonce_after = false;
		if ( ContextHelper::is_in_isset_or_empty( $this->phpcsFile, $stackPtr )
			|| ContextHelper::is_in_type_test( $this->phpcsFile, $stackPtr )
			|| VariableHelper::is_comparison( $this->phpcsFile, $stackPtr )
			|| ContextHelper::is_in_array_comparison( $this->phpcsFile, $stackPtr )
			|| ContextHelper::is_in_function_call( $this->phpcsFile, $stackPtr, UnslashingFunctionsHelper::get_functions() ) !== false
			|| $this->is_only_sanitized( $stackPtr )
		) {
			$allow_nonce_after = true;
		}

		// We allow for certain actions, such as an isset() check to come before the nonce check.
		// If this superglobal is inside such a check, look for the nonce after it as well,
		// all the way to the end of the scope.
		if ( true === $allow_nonce_after ) {
			$end = ( 0 === $start ) ? $this->phpcsFile->numTokens : $this->tokens[ $start ]['scope_closer'];
		}

		// Check if we've looked here before.
		$filename = $this->phpcsFile->getFilename();

		$current_cache = $this->get_cache( $filename, $start );
		if ( false !== $current_cache['nonce'] ) {
			// If we have already found an nonce check in this scope, we just
			// need to check whether it comes before this token. It is OK if the
			// check is after the token though, if this was only a isset() check.
			return ( true === $allow_nonce_after || $current_cache['nonce'] < $stackPtr );
		} elseif ( $end <= $current_cache['end'] ) {
			// If not, we can still go ahead and return false if we've already
			// checked to the end of the search area.
			return false;
		}

		$search_start = $start;
		if ( $current_cache['end'] > $start ) {
			// We haven't checked this far yet, but we can still save work by
			// skipping over the part we've already checked.
			$search_start = $this->cached_results['cache'][ $start ]['end'];
		}

		// Loop through the tokens looking for nonce verification functions.
		for ( $i = $search_start; $i < $end; $i++ ) {
			// Skip over nested closed scope constructs.
			if ( isset( Collections::closedScopes()[ $this->tokens[ $i ]['code'] ] ) ) {
				if ( isset( $this->tokens[ $i ]['scope_closer'] ) ) {
					$i = $this->tokens[ $i ]['scope_closer'];
				}
				continue;
			}

			// If this isn't a function name, skip it.
			if ( \T_STRING !== $this->tokens[ $i ]['code'] ) {
				continue;
			}

			// If this is one of the nonce verification functions, we can bail out.
			if ( isset( $this->nonceVerificationFunctions[ $this->tokens[ $i ]['content'] ] ) ) {
				/*
				 * Now, make sure it is a call to a global function.
				 */
				if ( ContextHelper::has_object_operator_before( $this->phpcsFile, $i ) === true ) {
					continue;
				}

				if ( ContextHelper::is_token_namespaced( $this->phpcsFile, $i ) === true ) {
					continue;
				}

				$this->set_cache( $filename, $start, $end, $i );
				return true;
			}
		}

		// We're still here, so no luck.
		$this->set_cache( $filename, $start, $end, false );

		return false;
	}

	/**
	 * Helper function to retrieve results from the cache.
	 *
	 * @since 3.0.0
	 *
	 * @param string $filename The name of the current file.
	 * @param int    $start    The stack pointer searches started from.
	 *
	 * @return array<string, mixed>
	 */
	private function get_cache( $filename, $start ) {
		if ( is_array( $this->cached_results )
			&& $filename === $this->cached_results['file']
			&& isset( $this->cached_results['cache'][ $start ] )
		) {
			return $this->cached_results['cache'][ $start ];
		}

		return array(
			'end'   => 0,
			'nonce' => false,
		);
	}

	/**
	 * Helper function to store results to the cache.
	 *
	 * @since 3.0.0
	 *
	 * @param string   $filename The name of the current file.
	 * @param int      $start    The stack pointer searches started from.
	 * @param int      $end      The stack pointer searched stopped at.
	 * @param int|bool $nonce    Stack pointer to the nonce verification function call or false if none was found.
	 *
	 * @return void
	 */
	private function set_cache( $filename, $start, $end, $nonce ) {
		if ( is_array( $this->cached_results ) === false
			|| $filename !== $this->cached_results['file']
		) {
			$this->cached_results = array(
				'file'  => $filename,
				'cache' => array(
					$start => array(
						'end'   => $end,
						'nonce' => $nonce,
					),
				),
			);
			return;
		}

		// Okay, so we know the current cache is for the current file. Check if we've seen this start pointer before.
		if ( isset( $this->cached_results['cache'][ $start ] ) === false ) {
			$this->cached_results['cache'][ $start ] = array(
				'end'   => $end,
				'nonce' => $nonce,
			);
			return;
		}

		// Update existing entry.
		if ( $end > $this->cached_results['cache'][ $start ]['end'] ) {
			$this->cached_results['cache'][ $start ]['end'] = $end;
		}

		$this->cached_results['cache'][ $start ]['nonce'] = $nonce;
	}

	/**
	 * Merge custom functions provided via a custom ruleset with the defaults, if we haven't already.
	 *
	 * @since 0.11.0 Split out from the `process()` method.
	 *
	 * @return void
	 */
	protected function mergeFunctionLists() {
		if ( $this->customNonceVerificationFunctions !== $this->addedCustomNonceFunctions ) {
			$this->nonceVerificationFunctions = RulesetPropertyHelper::merge_custom_array(
				$this->customNonceVerificationFunctions,
				$this->nonceVerificationFunctions
			);

			$this->addedCustomNonceFunctions = $this->customNonceVerificationFunctions;
		}
	}
}
