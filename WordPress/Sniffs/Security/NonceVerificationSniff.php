<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\Security;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Utils\MessageHelper;
use WordPressCS\WordPress\Helpers\RulesetPropertyHelper;
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
		'$_FILE'    => true,
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
	 * Custom list of functions that sanitize the values passed to them.
	 *
	 * @since 0.11.0
	 *
	 * @var string|string[]
	 */
	public $customSanitizingFunctions = array();

	/**
	 * Custom sanitizing functions that implicitly unslash the values passed to them.
	 *
	 * @since 0.11.0
	 *
	 * @var string|string[]
	 */
	public $customUnslashingSanitizingFunctions = array();

	/**
	 * Cache of previously added custom functions.
	 *
	 * Prevents having to do the same merges over and over again.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 - Changed from public static to protected non-static.
	 *               - Changed the format from simple bool to array.
	 *
	 * @var array
	 */
	protected $addedCustomFunctions = array(
		'nonce'           => array(),
		'sanitize'        => array(),
		'unslashsanitize' => array(),
	);

	/**
	 * List of the functions which verify nonces.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 * @since 3.0.0  Moved from the generic `Sniff` class to this class.
	 *
	 * @var array
	 */
	private $nonceVerificationFunctions = array(
		'wp_verify_nonce'     => true,
		'check_admin_referer' => true,
		'check_ajax_referer'  => true,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {

		return array(
			\T_VARIABLE,
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

		$instance = $this->tokens[ $stackPtr ];

		if ( ! isset( $this->superglobals[ $instance['content'] ] ) ) {
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
		if ( false === $this->superglobals[ $instance['content'] ] ) {
			$error_code = 'Recommended';
		}

		// If we're still here, no nonce-verification function was found.
		MessageHelper::addMessage(
			$this->phpcsFile,
			'Processing form data without nonce verification.',
			$stackPtr,
			$this->superglobals[ $instance['content'] ],
			$error_code
		);
	}

	/**
	 * Check if this token has an associated nonce check.
	 *
	 * @since 0.5.0
	 * @since 3.0.0  Moved from the generic `Sniff` class to this class.
	 *
	 * @param int $stackPtr The position of the current token in the stack of tokens.
	 *
	 * @return bool
	 */
	private function has_nonce_check( $stackPtr ) {

		/**
		 * A cache of the scope that we last checked for nonce verification in.
		 *
		 * @var array {
		 *      @var string   $file        The name of the file.
		 *      @var int      $start       The index of the token where the scope started.
		 *      @var int      $end         The index of the token where the scope ended.
		 *      @var bool|int $nonce_check The index of the token where an nonce check
		 *                                 was found, or false if none was found.
		 * }
		 */
		static $last;

		$start = 0;
		$end   = $stackPtr;

		$tokens = $this->phpcsFile->getTokens();

		// If we're in a function, only look inside of it.
		// Once PHPCS 3.5.0 comes out this should be changed to the new Conditions::GetLastCondition() method.
		if ( isset( $tokens[ $stackPtr ]['conditions'] ) === true ) {
			$conditions = $tokens[ $stackPtr ]['conditions'];
			$conditions = array_reverse( $conditions, true );
			foreach ( $conditions as $tokenPtr => $condition ) {
				if ( \T_FUNCTION === $condition || \T_CLOSURE === $condition ) {
					$start = $tokens[ $tokenPtr ]['scope_opener'];
					break;
				}
			}
		}

		$allow_nonce_after = false;
		if ( $this->is_in_isset_or_empty( $stackPtr )
			|| $this->is_in_type_test( $stackPtr )
			|| VariableHelper::is_comparison( $this->phpcsFile, $stackPtr )
			|| $this->is_in_array_comparison( $stackPtr )
			|| $this->is_in_function_call( $stackPtr, $this->unslashingFunctions ) !== false
			|| $this->is_only_sanitized( $stackPtr )
		) {
			$allow_nonce_after = true;
		}

		// We allow for certain actions, such as an isset() check to come before the nonce check.
		// If this superglobal is inside such a check, look for the nonce after it as well,
		// all the way to the end of the scope.
		if ( true === $allow_nonce_after ) {
			$end = ( 0 === $start ) ? $this->phpcsFile->numTokens : $tokens[ $start ]['scope_closer'];
		}

		// Check if we've looked here before.
		$filename = $this->phpcsFile->getFilename();

		if ( is_array( $last )
			&& $filename === $last['file']
			&& $start === $last['start']
		) {

			if ( false !== $last['nonce_check'] ) {
				// If we have already found an nonce check in this scope, we just
				// need to check whether it comes before this token. It is OK if the
				// check is after the token though, if this was only a isset() check.
				return ( true === $allow_nonce_after || $last['nonce_check'] < $stackPtr );
			} elseif ( $end <= $last['end'] ) {
				// If not, we can still go ahead and return false if we've already
				// checked to the end of the search area.
				return false;
			}

			// We haven't checked this far yet, but we can still save work by
			// skipping over the part we've already checked.
			$start = $last['end'];
		} else {
			$last = array(
				'file'  => $filename,
				'start' => $start,
				'end'   => $end,
			);
		}

		// Loop through the tokens looking for nonce verification functions.
		for ( $i = $start; $i < $end; $i++ ) {
			// Skip over nested closed scope constructs.
			if ( \T_FUNCTION === $tokens[ $i ]['code']
				|| \T_CLOSURE === $tokens[ $i ]['code']
				|| isset( Tokens::$ooScopeTokens[ $tokens[ $i ]['code'] ] )
			) {
				if ( isset( $tokens[ $i ]['scope_closer'] ) ) {
					$i = $tokens[ $i ]['scope_closer'];
				}
				continue;
			}

			// If this isn't a function name, skip it.
			if ( \T_STRING !== $tokens[ $i ]['code'] ) {
				continue;
			}

			// If this is one of the nonce verification functions, we can bail out.
			if ( isset( $this->nonceVerificationFunctions[ $tokens[ $i ]['content'] ] ) ) {
				/*
				 * Now, make sure it is a call to a global function.
				 */
				if ( $this->is_class_object_call( $i ) === true ) {
					continue;
				}

				if ( $this->is_token_namespaced( $i ) === true ) {
					continue;
				}

				$last['nonce_check'] = $i;
				return true;
			}
		}

		// We're still here, so no luck.
		$last['nonce_check'] = false;

		return false;
	}

	/**
	 * Merge custom functions provided via a custom ruleset with the defaults, if we haven't already.
	 *
	 * @since 0.11.0 Split out from the `process()` method.
	 *
	 * @return void
	 */
	protected function mergeFunctionLists() {
		if ( $this->customNonceVerificationFunctions !== $this->addedCustomFunctions['nonce'] ) {
			$this->nonceVerificationFunctions = RulesetPropertyHelper::merge_custom_array(
				$this->customNonceVerificationFunctions,
				$this->nonceVerificationFunctions
			);

			$this->addedCustomFunctions['nonce'] = $this->customNonceVerificationFunctions;
		}

		if ( $this->customSanitizingFunctions !== $this->addedCustomFunctions['sanitize'] ) {
			$this->sanitizingFunctions = RulesetPropertyHelper::merge_custom_array(
				$this->customSanitizingFunctions,
				$this->sanitizingFunctions
			);

			$this->addedCustomFunctions['sanitize'] = $this->customSanitizingFunctions;
		}

		if ( $this->customUnslashingSanitizingFunctions !== $this->addedCustomFunctions['unslashsanitize'] ) {
			$this->unslashingSanitizingFunctions = RulesetPropertyHelper::merge_custom_array(
				$this->customUnslashingSanitizingFunctions,
				$this->unslashingSanitizingFunctions
			);

			$this->addedCustomFunctions['unslashsanitize'] = $this->customUnslashingSanitizingFunctions;
		}
	}

}
