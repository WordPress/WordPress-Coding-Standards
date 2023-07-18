<?php
/**
 * Represents a PHP_CodeSniffer sniff for sniffing WordPress coding standards.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress;

use PHP_CodeSniffer\Sniffs\Sniff as PHPCS_Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\Helpers\ArrayWalkingFunctionsHelper;
use WordPressCS\WordPress\Helpers\ContextHelper;
use WordPressCS\WordPress\Helpers\SanitizingFunctionsTrait;
use WordPressCS\WordPress\Helpers\UnslashingFunctionsHelper;

/**
 * Represents a PHP_CodeSniffer sniff for sniffing WordPress coding standards.
 *
 * Provides a bootstrap for the sniffs, to reduce code duplication.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.4.0
 */
abstract class Sniff implements PHPCS_Sniff {

	use SanitizingFunctionsTrait;

	/**
	 * A list of superglobals that incorporate user input.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from static to non-static.
	 *
	 * @var string[]
	 */
	protected $input_superglobals = array(
		'$_COOKIE',
		'$_GET',
		'$_FILES',
		'$_POST',
		'$_REQUEST',
		'$_SERVER',
	);

	/**
	 * The current file being sniffed.
	 *
	 * @since 0.4.0
	 *
	 * @var \PHP_CodeSniffer\Files\File
	 */
	protected $phpcsFile;

	/**
	 * The list of tokens in the current file being sniffed.
	 *
	 * @since 0.4.0
	 *
	 * @var array
	 */
	protected $tokens;

	/**
	 * Set sniff properties and hand off to child class for processing of the token.
	 *
	 * @since 0.11.0
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The position of the current token
	 *                                               in the stack passed in $tokens.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process( File $phpcsFile, $stackPtr ) {
		$this->init( $phpcsFile );
		return $this->process_token( $stackPtr );
	}

	/**
	 * Processes a sniff when one of its tokens is encountered.
	 *
	 * @since 0.11.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	abstract public function process_token( $stackPtr );

	/**
	 * Initialize the class for the current process.
	 *
	 * This method must be called by child classes before using many of the methods
	 * below.
	 *
	 * @since 0.4.0
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file currently being processed.
	 */
	protected function init( File $phpcsFile ) {
		$this->phpcsFile = $phpcsFile;
		$this->tokens    = $phpcsFile->getTokens();
	}

	/**
	 * Check if something is only being sanitized.
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return bool Whether the token is only within a sanitization.
	 */
	protected function is_only_sanitized( $stackPtr ) {

		// If it isn't being sanitized at all.
		if ( ! $this->is_sanitized( $stackPtr ) ) {
			return false;
		}

		// If this isn't set, we know the value must have only been casted, because
		// is_sanitized() would have returned false otherwise.
		if ( ! isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			return true;
		}

		// At this point we're expecting the value to have not been casted. If it
		// was, it wasn't *only* casted, because it's also in a function.
		if ( ContextHelper::is_safe_casted( $this->phpcsFile, $stackPtr ) ) {
			return false;
		}

		// The only parentheses should belong to the sanitizing function. If there's
		// more than one set, this isn't *only* sanitization.
		return ( \count( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) === 1 );
	}

	/**
	 * Check if something is being sanitized.
	 *
	 * @since 0.5.0
	 *
	 * @param int  $stackPtr        The index of the token in the stack.
	 * @param bool $require_unslash Whether to give an error if no unslashing function
	 *                              is used on the variable before sanitization.
	 *
	 * @return bool Whether the token being sanitized.
	 */
	protected function is_sanitized( $stackPtr, $require_unslash = false ) {

		// First we check if it is being casted to a safe value.
		if ( ContextHelper::is_safe_casted( $this->phpcsFile, $stackPtr ) ) {
			return true;
		}

		// If this isn't within a function call, we know already that it's not safe.
		if ( ! isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			if ( $require_unslash ) {
				$this->add_unslash_error( $stackPtr );
			}

			return false;
		}

		// Get the function that it's in.
		$nested_parenthesis = $this->tokens[ $stackPtr ]['nested_parenthesis'];
		$nested_openers     = array_keys( $nested_parenthesis );
		$function_opener    = array_pop( $nested_openers );
		$functionPtr        = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $function_opener - 1 ), null, true, null, true );

		// If it is just being unset, the value isn't used at all, so it's safe.
		if ( \T_UNSET === $this->tokens[ $functionPtr ]['code'] ) {
			return true;
		}

		$valid_functions  = $this->get_sanitizing_functions();
		$valid_functions += $this->get_sanitizing_and_unslashing_functions();
		$valid_functions += UnslashingFunctionsHelper::get_functions();
		$valid_functions += ArrayWalkingFunctionsHelper::get_functions();

		$functionPtr = ContextHelper::is_in_function_call( $this->phpcsFile, $stackPtr, $valid_functions );

		// If this isn't a call to one of the valid functions, it sure isn't a sanitizing function.
		if ( false === $functionPtr ) {
			if ( true === $require_unslash ) {
				$this->add_unslash_error( $stackPtr );
			}

			return false;
		}

		$functionName = $this->tokens[ $functionPtr ]['content'];

		// Check if an unslashing function is being used.
		if ( UnslashingFunctionsHelper::is_unslashing_function( $functionName ) ) {

			$is_unslashed = true;

			// Remove the unslashing functions.
			$valid_functions = array_diff_key( $valid_functions, UnslashingFunctionsHelper::get_functions() );

			// Check is any of the remaining (sanitizing) functions is used.
			$higherFunctionPtr = ContextHelper::is_in_function_call( $this->phpcsFile, $functionPtr, $valid_functions );

			// If there is no other valid function being used, this value is unsanitized.
			if ( false === $higherFunctionPtr ) {
				return false;
			}

			$functionPtr  = $higherFunctionPtr;
			$functionName = $this->tokens[ $functionPtr ]['content'];

		} else {
			$is_unslashed = false;
		}

		// Arrays might be sanitized via an array walking function using a callback.
		if ( ArrayWalkingFunctionsHelper::is_array_walking_function( $functionName ) ) {

			// Get the callback parameter.
			$callback = ArrayWalkingFunctionsHelper::get_callback_parameter( $this->phpcsFile, $functionPtr );

			if ( ! empty( $callback ) ) {
				/*
				 * If this is a function callback (not a method callback array) and we're able
				 * to resolve the function name, do so.
				 */
				$first_non_empty = $this->phpcsFile->findNext(
					Tokens::$emptyTokens,
					$callback['start'],
					( $callback['end'] + 1 ),
					true
				);

				if ( false !== $first_non_empty && \T_CONSTANT_ENCAPSED_STRING === $this->tokens[ $first_non_empty ]['code'] ) {
					$functionName = TextStrings::stripQuotes( $this->tokens[ $first_non_empty ]['content'] );
				}
			}
		}

		// If slashing is required, give an error.
		if ( ! $is_unslashed && $require_unslash && ! $this->is_sanitizing_and_unslashing_function( $functionName ) ) {
			$this->add_unslash_error( $stackPtr );
		}

		// Check if this is a sanitizing function.
		if ( $this->is_sanitizing_function( $functionName ) || $this->is_sanitizing_and_unslashing_function( $functionName ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add an error for missing use of unslashing.
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 */
	public function add_unslash_error( $stackPtr ) {

		$this->phpcsFile->addError(
			'%s data not unslashed before sanitization. Use wp_unslash() or similar',
			$stackPtr,
			'MissingUnslash',
			array( $this->tokens[ $stackPtr ]['content'] )
		);
	}
}
