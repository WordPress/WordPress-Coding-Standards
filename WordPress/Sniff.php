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
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\Helpers\ArrayWalkingFunctionsHelper;
use WordPressCS\WordPress\Helpers\ContextHelper;
use WordPressCS\WordPress\Helpers\SanitizingFunctionsTrait;
use WordPressCS\WordPress\Helpers\VariableHelper;

/**
 * Represents a PHP_CodeSniffer sniff for sniffing WordPress coding standards.
 *
 * Provides a bootstrap for the sniffs, to reduce code duplication.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.4.0
 *
 * {@internal This class contains numerous properties where the array format looks
 *            like `'string' => true`, i.e. the array item is set as the array key.
 *            This allows for sniffs to verify whether something is in one of these
 *            lists using `isset()` rather than `in_array()` which is a much more
 *            efficient (faster) check to execute and therefore improves the
 *            performance of the sniffs.
 *            The `true` value in those cases is used as a placeholder and has no
 *            meaning in and of itself.
 *            In the rare few cases where the array values *do* have meaning, this
 *            is documented in the property documentation.}}
 */
abstract class Sniff implements PHPCS_Sniff {

	use SanitizingFunctionsTrait;

	/**
	 * Functions which unslash the data passed to them.
	 *
	 * @since 2.1.0
	 *
	 * @var array
	 */
	protected $unslashingFunctions = array(
		'stripslashes_deep'              => true,
		'stripslashes_from_strings_only' => true,
		'wp_unslash'                     => true,
	);

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
		$valid_functions += $this->unslashingFunctions;
		$valid_functions += ArrayWalkingFunctionsHelper::get_array_walking_functions();

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
		if ( isset( $this->unslashingFunctions[ $functionName ] ) ) {

			$is_unslashed = true;

			// Remove the unslashing functions.
			$valid_functions = array_diff_key( $valid_functions, $this->unslashingFunctions );

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

	/**
	 * Check if the existence of a variable is validated with isset(), empty(), array_key_exists()
	 * or key_exists().
	 *
	 * When $in_condition_only is false, (which is the default), this is considered
	 * valid:
	 *
	 * ```php
	 * if ( isset( $var ) ) {
	 *     // Do stuff, like maybe return or exit (but could be anything)
	 * }
	 *
	 * foo( $var );
	 * ```
	 *
	 * When it is true, that would be invalid, the use of the variable must be within
	 * the scope of the validating condition, like this:
	 *
	 * ```php
	 * if ( isset( $var ) ) {
	 *     foo( $var );
	 * }
	 * ```
	 *
	 * @since 0.5.0
	 * @since 2.1.0 Now recognizes array_key_exists() and key_exists() as validation functions.
	 * @since 2.1.0 Stricter check on whether the correct variable and the correct
	 *              array keys are being validated.
	 *
	 * @param int          $stackPtr          The index of this token in the stack.
	 * @param array|string $array_keys        An array key to check for ("bar" in $foo['bar'])
	 *                                        or an array of keys for multi-level array access.
	 * @param bool         $in_condition_only Whether to require that this use of the
	 *                                        variable occur within the scope of the
	 *                                        validating condition, or just in the same
	 *                                        scope as it (default).
	 *
	 * @return bool Whether the var is validated.
	 */
	protected function is_validated( $stackPtr, $array_keys = array(), $in_condition_only = false ) {

		if ( $in_condition_only ) {
			/*
			 * This is a stricter check, requiring the variable to be used only
			 * within the validation condition.
			 */

			// If there are no conditions, there's no validation.
			if ( empty( $this->tokens[ $stackPtr ]['conditions'] ) ) {
				return false;
			}

			$conditions = $this->tokens[ $stackPtr ]['conditions'];
			end( $conditions ); // Get closest condition.
			$conditionPtr = key( $conditions );
			$condition    = $this->tokens[ $conditionPtr ];

			if ( ! isset( $condition['parenthesis_opener'] ) ) {
				// Live coding or parse error.
				return false;
			}

			$scope_start = $condition['parenthesis_opener'];
			$scope_end   = $condition['parenthesis_closer'];

		} else {
			/*
			 * We are are more loose, requiring only that the variable be validated
			 * in the same function/file scope as it is used.
			 */

			$scope_start = 0;

			// Check if we are in a function.
			$function = $this->phpcsFile->getCondition( $stackPtr, \T_FUNCTION );

			// If so, we check only within the function, otherwise the whole file.
			if ( false !== $function ) {
				$scope_start = $this->tokens[ $function ]['scope_opener'];
			} else {
				// Check if we are in a closure.
				$closure = $this->phpcsFile->getCondition( $stackPtr, \T_CLOSURE );

				// If so, we check only within the closure.
				if ( false !== $closure ) {
					$scope_start = $this->tokens[ $closure ]['scope_opener'];
				}
			}

			$scope_end = $stackPtr;
		}

		if ( ! empty( $array_keys ) && ! is_array( $array_keys ) ) {
			$array_keys = (array) $array_keys;
		}

		$bare_array_keys = array_map( array( 'PHPCSUtils\Utils\TextStrings', 'stripQuotes' ), $array_keys );
		$targets         = array(
			\T_ISSET          => 'construct',
			\T_EMPTY          => 'construct',
			\T_UNSET          => 'construct',
			\T_STRING         => 'function_call',
			\T_COALESCE       => 'coalesce',
			\T_COALESCE_EQUAL => 'coalesce',
		);

		// phpcs:ignore Generic.CodeAnalysis.JumbledIncrementer.Found -- On purpose, see below.
		for ( $i = ( $scope_start + 1 ); $i < $scope_end; $i++ ) {

			if ( isset( $targets[ $this->tokens[ $i ]['code'] ] ) === false ) {
				continue;
			}

			switch ( $targets[ $this->tokens[ $i ]['code'] ] ) {
				case 'construct':
					$issetOpener = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $i + 1 ), null, true, null, true );
					if ( false === $issetOpener || \T_OPEN_PARENTHESIS !== $this->tokens[ $issetOpener ]['code'] ) {
						// Parse error or live coding.
						continue 2;
					}

					$issetCloser = $this->tokens[ $issetOpener ]['parenthesis_closer'];

					// Look for this variable. We purposely stomp $i from the parent loop.
					for ( $i = ( $issetOpener + 1 ); $i < $issetCloser; $i++ ) {

						if ( \T_VARIABLE !== $this->tokens[ $i ]['code'] ) {
							continue;
						}

						if ( $this->tokens[ $stackPtr ]['content'] !== $this->tokens[ $i ]['content'] ) {
							continue;
						}

						// If we're checking for specific array keys (ex: 'hello' in
						// $_POST['hello']), that must match too. Quote-style, however, doesn't matter.
						if ( ! empty( $bare_array_keys ) ) {
							$found_keys = VariableHelper::get_array_access_keys( $this->phpcsFile, $i );
							$found_keys = array_map( array( 'PHPCSUtils\Utils\TextStrings', 'stripQuotes' ), $found_keys );
							$diff       = array_diff_assoc( $bare_array_keys, $found_keys );
							if ( ! empty( $diff ) ) {
								continue;
							}
						}

						return true;
					}

					break;

				case 'function_call':
					// Only check calls to array_key_exists() and key_exists().
					if ( 'array_key_exists' !== $this->tokens[ $i ]['content']
						&& 'key_exists' !== $this->tokens[ $i ]['content']
					) {
						continue 2;
					}

					$next_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $i + 1 ), null, true, null, true );
					if ( false === $next_non_empty || \T_OPEN_PARENTHESIS !== $this->tokens[ $next_non_empty ]['code'] ) {
						// Not a function call.
						continue 2;
					}

					if ( ContextHelper::has_object_operator_before( $this->phpcsFile, $i ) === true ) {
						// Method call.
						continue 2;
					}

					if ( ContextHelper::is_token_namespaced( $this->phpcsFile, $i ) === true ) {
						// Namespaced function call.
						continue 2;
					}

					$params = PassedParameters::getParameters( $this->phpcsFile, $i );
					if ( count( $params ) < 2 ) {
						continue 2;
					}

					$param2_first_token = $this->phpcsFile->findNext( Tokens::$emptyTokens, $params[2]['start'], ( $params[2]['end'] + 1 ), true );
					if ( false === $param2_first_token
						|| \T_VARIABLE !== $this->tokens[ $param2_first_token ]['code']
						|| $this->tokens[ $param2_first_token ]['content'] !== $this->tokens[ $stackPtr ]['content']
					) {
						continue 2;
					}

					if ( ! empty( $bare_array_keys ) ) {
						// Prevent the original array from being altered.
						$bare_keys = $bare_array_keys;
						$last_key  = array_pop( $bare_keys );

						/*
						 * For multi-level array access, the complete set of keys could be split between
						 * the first and the second parameter, but could also be completely in the second
						 * parameter, so we need to check both options.
						 */

						$found_keys = VariableHelper::get_array_access_keys( $this->phpcsFile, $param2_first_token );
						$found_keys = array_map( array( 'PHPCSUtils\Utils\TextStrings', 'stripQuotes' ), $found_keys );

						// First try matching the complete set against the second parameter.
						$diff = array_diff_assoc( $bare_array_keys, $found_keys );
						if ( empty( $diff ) ) {
							return true;
						}

						// If that failed, try getting an exact match for the subset against the
						// second parameter and the last key against the first.
						if ( $bare_keys === $found_keys && TextStrings::stripQuotes( $params[1]['raw'] ) === $last_key ) {
							return true;
						}

						// Didn't find the correct array keys.
						continue 2;
					}

					return true;

				case 'coalesce':
					$prev = $i;
					do {
						$prev = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $prev - 1 ), null, true, null, true );
						// Skip over array keys, like `$_GET['key']['subkey']`.
						if ( \T_CLOSE_SQUARE_BRACKET === $this->tokens[ $prev ]['code'] ) {
							$prev = $this->tokens[ $prev ]['bracket_opener'];
							continue;
						}

						break;
					} while ( $prev >= ( $scope_start + 1 ) );

					// We should now have reached the variable.
					if ( \T_VARIABLE !== $this->tokens[ $prev ]['code'] ) {
						continue 2;
					}

					if ( $this->tokens[ $prev ]['content'] !== $this->tokens[ $stackPtr ]['content'] ) {
						continue 2;
					}

					if ( ! empty( $bare_array_keys ) ) {
						$found_keys = VariableHelper::get_array_access_keys( $this->phpcsFile, $prev );
						$found_keys = array_map( array( 'PHPCSUtils\Utils\TextStrings', 'stripQuotes' ), $found_keys );
						$diff       = array_diff_assoc( $bare_array_keys, $found_keys );
						if ( ! empty( $diff ) ) {
							continue 2;
						}
					}

					// Right variable, correct key.
					return true;
			}
		}

		return false;
	}
}
