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
use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\AbstractFunctionRestrictionsSniff;
use WordPressCS\WordPress\Helpers\ArrayWalkingFunctionsHelper;
use WordPressCS\WordPress\Helpers\ContextHelper;
use WordPressCS\WordPress\Helpers\ConstantsHelper;
use WordPressCS\WordPress\Helpers\EscapingFunctionsTrait;
use WordPressCS\WordPress\Helpers\FormattingFunctionsHelper;
use WordPressCS\WordPress\Helpers\PrintingFunctionsTrait;
use WordPressCS\WordPress\Helpers\VariableHelper;

/**
 * Verifies that all outputted strings are escaped.
 *
 * @link    https://developer.wordpress.org/apis/security/data-validation/ WordPress Developer Docs on Data Validation.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   2013-06-11
 * @since   0.4.0  This class now extends the WordPressCS native `Sniff` class.
 * @since   0.5.0  The various function list properties which used to be contained in this class
 *                 have been moved to the WordPressCS native `Sniff` parent class.
 * @since   0.12.0 This sniff will now also check for output escaping when using shorthand
 *                 echo tags `<?=`.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   1.0.0  This sniff has been moved from the `XSS` category to the `Security` category.
 * @since   3.0.0  This class now extends the WordPressCS native
 *                 `AbstractFunctionRestrictionsSniff` class.
 *                 The parent `exclude` property is disabled.
 *
 * @uses    \WordPressCS\WordPress\Helpers\EscapingFunctionsTrait::$customEscapingFunctions
 * @uses    \WordPressCS\WordPress\Helpers\EscapingFunctionsTrait::$customAutoEscapedFunctions
 * @uses    \WordPressCS\WordPress\Helpers\PrintingFunctionsTrait::$customPrintingFunctions
 */
class EscapeOutputSniff extends AbstractFunctionRestrictionsSniff {

	use EscapingFunctionsTrait;
	use PrintingFunctionsTrait;

	/**
	 * Printing functions that incorporate unsafe values.
	 *
	 * @since 0.4.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 *
	 * @var array
	 */
	protected $unsafePrintingFunctions = array(
		'_e'  => 'esc_html_e() or esc_attr_e()',
		'_ex' => 'echo esc_html_x() or echo esc_attr_x()',
	);

	/**
	 * List of names of the native PHP constants which can be considered safe.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $safe_php_constants = array(
		'PHP_EOL'             => true, // String.
		'PHP_VERSION'         => true, // Integer.
		'PHP_MAJOR_VERSION'   => true, // Integer.
		'PHP_MINOR_VERSION'   => true, // Integer.
		'PHP_RELEASE_VERSION' => true, // Integer.
		'PHP_VERSION_ID'      => true, // Integer.
		'PHP_EXTRA_VERSION'   => true, // String.
		'PHP_DEBUG'           => true, // Integer.
	);

	/**
	 * List of tokens which can be considered as safe when directly part of the output.
	 *
	 * This list is enhanced with additional tokens in the `register()` method.
	 *
	 * @since 0.12.0
	 *
	 * @var array
	 */
	private $safe_components = array(
		\T_LNUMBER                  => \T_LNUMBER,
		\T_DNUMBER                  => \T_DNUMBER,
		\T_TRUE                     => \T_TRUE,
		\T_FALSE                    => \T_FALSE,
		\T_NULL                     => \T_NULL,
		\T_CONSTANT_ENCAPSED_STRING => \T_CONSTANT_ENCAPSED_STRING,
		\T_START_NOWDOC             => \T_START_NOWDOC,
		\T_NOWDOC                   => \T_NOWDOC,
		\T_END_NOWDOC               => \T_END_NOWDOC,
		\T_BOOLEAN_NOT              => \T_BOOLEAN_NOT,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		// Enrich the list of "safe components" tokens.
		$this->safe_components += Tokens::$comparisonTokens;
		$this->safe_components += Tokens::$operators;
		$this->safe_components += Tokens::$booleanOperators;
		$this->safe_components += Collections::incrementDecrementOperators();

		// Set up the tokens the sniff should listen too.
		$targets   = parent::register();
		$targets[] = \T_ECHO;
		$targets[] = \T_PRINT;
		$targets[] = \T_EXIT;
		$targets[] = \T_OPEN_TAG_WITH_ECHO;

		return $targets;
	}

	/**
	 * Groups of functions this sniff is looking for.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function getGroups() {
		// Make sure all array keys are lowercase (could contain user provided function names).
		$printing_functions = array_change_key_case( $this->get_printing_functions(), \CASE_LOWER );

		// Remove the unsafe printing functions to prevent duplicate notices.
		$printing_functions = array_diff_key( $printing_functions, $this->unsafePrintingFunctions );

		return array(
			'unsafe_printing_functions' => array(
				'functions' => array_keys( $this->unsafePrintingFunctions ),
			),
			'printing_functions' => array(
				'functions' => array_keys( $printing_functions ),
			),
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @since 3.0.0 This method has been split up.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {
		$start   = ( $stackPtr + 1 );
		$end     = $start;
		$ternary = false;

		switch ( $this->tokens[ $stackPtr ]['code'] ) {
			case \T_STRING:
				// Prevent exclusion of any of the function groups.
				$this->exclude = array();

				// In the tests, custom printing functions may be added/removed on the fly.
				if ( defined( 'PHP_CODESNIFFER_IN_TESTS' ) ) {
					$this->setup_groups( 'functions' );
				}

				// Let the abstract parent class handle the initial function call check.
				return parent::process_token( $stackPtr );

			case \T_EXIT:
				$next_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );
				if ( false === $next_non_empty
					|| \T_OPEN_PARENTHESIS !== $this->tokens[ $next_non_empty ]['code']
					|| isset( $this->tokens[ $next_non_empty ]['parenthesis_closer'] ) === false
				) {
					// Live coding/parse error or an exit/die which doesn't pass a status code. Ignore.
					return;
				}

				// $end is not examined, so make sure the parentheses are balanced.
				$start = $next_non_empty;
				$end   = ( $this->tokens[ $next_non_empty ]['parenthesis_closer'] + 1 );
				break;

			case \T_PRINT:
				$end = BCFile::findEndOfStatement( $this->phpcsFile, $stackPtr );
				if ( \T_COMMA !== $this->tokens[ $end ]['code']
					&& \T_SEMICOLON !== $this->tokens[ $end ]['code']
					&& \T_COLON !== $this->tokens[ $end ]['code']
					&& \T_DOUBLE_ARROW !== $this->tokens[ $end ]['code']
					&& isset( $this->tokens[ ( $end + 1 ) ] )
				) {
					/*
					 * FindEndOfStatement includes a comma/(semi-)colon/double arrow if that's the end of
					 * the statement, but for everything else, it returns the last non-empty token _before_
					 * the end, which would mean the last non-empty token in the statement would not
					 * be examined. Let's fix that.
					 */
					++$end;
				}

				// Note: no need to check for close tag as close tag will have the token before the tag as the $end.
				if ( $end >= ( $this->phpcsFile->numTokens - 1 ) ) {
					$last_non_empty = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, $end, null, true );
					if ( \T_SEMICOLON !== $this->tokens[ $last_non_empty ]['code'] ) {
						// Live coding/parse error at end of file. Ignore.
						return;
					}
				}

				// Special case for a print statement *within* a ternary, where we need to find the "inline else" as the end token.
				$prev_non_empty = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $stackPtr - 1 ), null, true );
				if ( \T_INLINE_THEN === $this->tokens[ $prev_non_empty ]['code'] ) {
					$target_nesting_level = 0;
					if ( empty( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) === false ) {
						$target_nesting_level = \count( $this->tokens[ $stackPtr ]['nested_parenthesis'] );
					}

					$inline_else = false;
					for ( $i = ( $stackPtr + 1 ); $i < $end; $i++ ) {
						if ( \T_INLINE_ELSE !== $this->tokens[ $i ]['code'] ) {
							continue;
						}

						if ( empty( $this->tokens[ $i ]['nested_parenthesis'] )
							&& 0 === $target_nesting_level
						) {
							$inline_else = $i;
							break;
						}

						if ( empty( $this->tokens[ $i ]['nested_parenthesis'] ) === false
							&& \count( $this->tokens[ $i ]['nested_parenthesis'] ) === $target_nesting_level
						) {
							$inline_else = $i;
							break;
						}
					}

					if ( false === $inline_else ) {
						// Live coding/parse error. Bow out.
						return;
					}

					$end = $inline_else;
				}

				break;

			// Echo, open tag with echo.
			default:
				$end = $this->phpcsFile->findNext( array( \T_SEMICOLON, \T_CLOSE_TAG ), $stackPtr );
				if ( false === $end ) {
					// Live coding/parse error. Bow out.
					return;
				}

				break;
		}

		if ( \T_EXIT !== $this->tokens[ $stackPtr ]['code'] ) {
			/*
			 * Check for a ternary operator.
			 * We only need to do this here if this echo/print statement is lacking parenthesis.
			 * Otherwise it will be handled in the `check_code_is_escaped()` method.
			 */
			$next_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );
			$last_non_empty = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $end - 1 ), null, true );

			if ( \T_OPEN_PARENTHESIS !== $this->tokens[ $next_non_empty ]['code']
				|| \T_CLOSE_PARENTHESIS !== $this->tokens[ $last_non_empty ]['code']
			) {
				// If there is a ternary skip over the part before the ?.
				$ternary = $this->find_ternary( $start, $end );
				if ( false !== $ternary ) {
					$start = ( $ternary + 1 );
				}
			}
		}

		return $this->check_code_is_escaped( $start, $end, $ternary );
	}

	/**
	 * Process a matched function call token.
	 *
	 * @since 3.0.0 Split off from the process_token() method.
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched
	 *                                in lowercase.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_matched_token( $stackPtr, $group_name, $matched_content ) {
		// Make sure we only deal with actual function calls, not function import use statements.
		$open_paren = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );
		if ( false === $open_paren
			|| \T_OPEN_PARENTHESIS !== $this->tokens[ $open_paren ]['code']
			|| isset( $this->tokens[ $open_paren ]['parenthesis_closer'] ) === false
		) {
			// Live coding, parse error or not a function _call_.
			return;
		}

		$end_of_statement = $this->tokens[ $open_paren ]['parenthesis_closer'];

		// These functions only need to have the first argument escaped.
		if ( \in_array( $matched_content, array( 'trigger_error', 'user_error' ), true ) ) {
			$first_param = PassedParameters::getParameter( $this->phpcsFile, $stackPtr, 1 );
			if ( false === $first_param ) {
				// First parameter doesn't exist. Nothing to do.
				return;
			}

			$end_of_statement = ( $first_param['end'] + 1 );
			unset( $first_param );
		}

		/*
		 * If the first param to `_deprecated_file()` follows the typical `basename( __FILE__ )`
		 * pattern, it doesn't need to be escaped.
		 */
		if ( '_deprecated_file' === $matched_content ) {
			$first_param = PassedParameters::getParameter( $this->phpcsFile, $stackPtr, 1 );
			if ( false === $first_param ) {
				// First parameter doesn't exist. Nothing to do.
				return;
			}

			// Check for a particular code pattern which can safely be ignored.
			if ( preg_match( '`^[\\\\]?basename\s*\(\s*__FILE__\s*\)$`', $first_param['clean'] ) === 1 ) {
				$stackPtr = ( $first_param['end'] + 2 );
			}
			unset( $first_param );
		}

		if ( 'unsafe_printing_functions' === $group_name ) {
			$error = $this->phpcsFile->addError(
				"All output should be run through an escaping function (like %s), found '%s'.",
				$stackPtr,
				'UnsafePrintingFunction',
				array( $this->unsafePrintingFunctions[ $matched_content ], $matched_content )
			);

			// If the error was reported, don't bother checking the function's arguments.
			if ( $error ) {
				return $end_of_statement;
			}
		}

		// Ignore the function itself.
		++$stackPtr;

		return $this->check_code_is_escaped( $stackPtr, $end_of_statement );
	}

	/**
	 * Check whether each relevant part of an arbitrary group of token is output escaped.
	 *
	 * @since 3.0.0 Split off from the process_token() method.
	 *
	 * @param int       $start   The position to start checking from.
	 * @param int       $end     The position to stop the check at.
	 * @param int|false $ternary Stack pointer to the `?` inline then token or
	 *                           FALSE when no non-parenthesized ternary was found.
	 *
	 * @return int Integer stack pointer to skip forward.
	 */
	protected function check_code_is_escaped( $start, $end, $ternary = false ) {

		$in_cast = false;

		// Looping through echo'd components.
		$watch = true;
		for ( $i = $start; $i < $end; $i++ ) {

			// Ignore whitespaces and comments.
			if ( isset( Tokens::$emptyTokens[ $this->tokens[ $i ]['code'] ] ) ) {
				continue;
			}

			// Ignore namespace separators.
			if ( \T_NS_SEPARATOR === $this->tokens[ $i ]['code'] ) {
				continue;
			}

			if ( \T_OPEN_PARENTHESIS === $this->tokens[ $i ]['code'] ) {

				if ( ! isset( $this->tokens[ $i ]['parenthesis_closer'] ) ) {
					// Live coding or parse error.
					break;
				}

				if ( $in_cast ) {

					// Skip to the end of a function call if it has been casted to a safe value.
					$i       = $this->tokens[ $i ]['parenthesis_closer'];
					$in_cast = false;

				} else {

					// Skip over the condition part of a ternary (i.e., to after the ?).
					$ternary = $this->phpcsFile->findNext( \T_INLINE_THEN, $i, $this->tokens[ $i ]['parenthesis_closer'] );

					if ( false !== $ternary ) {

						$next_paren = $this->phpcsFile->findNext( \T_OPEN_PARENTHESIS, ( $i + 1 ), $this->tokens[ $i ]['parenthesis_closer'] );

						// We only do it if the ternary isn't within a subset of parentheses.
						if ( false === $next_paren || ( isset( $this->tokens[ $next_paren ]['parenthesis_closer'] ) && $ternary > $this->tokens[ $next_paren ]['parenthesis_closer'] ) ) {
							$i = $ternary;
						}
					}
				}

				continue;
			}

			// Handle arrays for those functions that accept them.
			if ( \T_ARRAY === $this->tokens[ $i ]['code'] ) {
				++$i; // Skip the opening parenthesis.
				continue;
			}

			if ( isset( Collections::shortArrayTokens()[ $this->tokens[ $i ]['code'] ] ) ) {
				continue;
			}

			if ( \in_array( $this->tokens[ $i ]['code'], array( \T_DOUBLE_ARROW, \T_CLOSE_PARENTHESIS ), true ) ) {
				continue;
			}

			// Handle magic constants for debug functions.
			if ( isset( Tokens::$magicConstants[ $this->tokens[ $i ]['code'] ] ) ) {
				continue;
			}

			// Handle safe PHP native constants.
			if ( \T_STRING === $this->tokens[ $i ]['code']
				&& isset( $this->safe_php_constants[ $this->tokens[ $i ]['content'] ] )
				&& ConstantsHelper::is_use_of_global_constant( $this->phpcsFile, $i )
			) {
				continue;
			}

			// Wake up on concatenation characters, another part to check.
			if ( \T_STRING_CONCAT === $this->tokens[ $i ]['code'] ) {
				$watch = true;
				continue;
			}

			// Wake up after a ternary else (:).
			if ( false !== $ternary && \T_INLINE_ELSE === $this->tokens[ $i ]['code'] ) {
				$watch = true;
				continue;
			}

			// Wake up for commas.
			if ( \T_COMMA === $this->tokens[ $i ]['code'] ) {
				$in_cast = false;
				$watch   = true;
				continue;
			}

			if ( false === $watch ) {
				continue;
			}

			// Allow T_CONSTANT_ENCAPSED_STRING eg: echo 'Some String';
			// Also T_LNUMBER, e.g.: echo 45; exit -1; and booleans.
			if ( isset( $this->safe_components[ $this->tokens[ $i ]['code'] ] ) ) {
				continue;
			}

			$watch = false;

			// Allow int/double/bool casted variables.
			if ( isset( ContextHelper::get_safe_cast_tokens()[ $this->tokens[ $i ]['code'] ] ) ) {
				$in_cast = true;
				continue;
			}

			// Now check that next token is a function call.
			if ( \T_STRING === $this->tokens[ $i ]['code'] ) {

				$ptr                    = $i;
				$functionName           = $this->tokens[ $i ]['content'];
				$function_opener        = $this->phpcsFile->findNext( \T_OPEN_PARENTHESIS, ( $i + 1 ), null, false, null, true );
				$is_formatting_function = FormattingFunctionsHelper::is_formatting_function( $functionName );

				if ( false !== $function_opener ) {

					if ( ArrayWalkingFunctionsHelper::is_array_walking_function( $functionName ) ) {

						// Get the callback parameter.
						$callback = ArrayWalkingFunctionsHelper::get_callback_parameter( $this->phpcsFile, $ptr );

						if ( ! empty( $callback ) ) {
							/*
							 * If this is a function callback (not a method callback array) and we're able
							 * to resolve the function name, do so.
							 */
							$mapped_function = $this->phpcsFile->findNext(
								Tokens::$emptyTokens,
								$callback['start'],
								( $callback['end'] + 1 ),
								true
							);

							if ( false !== $mapped_function
								&& \T_CONSTANT_ENCAPSED_STRING === $this->tokens[ $mapped_function ]['code']
							) {
								$functionName = TextStrings::stripQuotes( $this->tokens[ $mapped_function ]['content'] );
								$ptr          = $mapped_function;
							}
						}
					}

					// Skip pointer to after the function.
					// If this is a formatting function we just skip over the opening
					// parenthesis. Otherwise we skip all the way to the closing.
					if ( $is_formatting_function ) {
						$i     = ( $function_opener + 1 );
						$watch = true;
					} elseif ( isset( $this->tokens[ $function_opener ]['parenthesis_closer'] ) ) {
						$i = $this->tokens[ $function_opener ]['parenthesis_closer'];
					} else {
						// Live coding or parse error.
						break;
					}
				}

				// If this is a safe function, we don't flag it.
				if (
					$is_formatting_function
					|| $this->is_escaping_function( $functionName )
					|| $this->is_auto_escaped_function( $functionName )
				) {
					continue;
				}

				$content = $functionName;

			} else {
				$content = $this->tokens[ $i ]['content'];
				$ptr     = $i;
			}

			// Make the error message a little more informative for array access variables.
			if ( \T_VARIABLE === $this->tokens[ $ptr ]['code'] ) {
				$array_keys = VariableHelper::get_array_access_keys( $this->phpcsFile, $ptr );

				if ( ! empty( $array_keys ) ) {
					$content .= '[' . implode( '][', $array_keys ) . ']';
				}
			}

			$this->phpcsFile->addError(
				"All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '%s'.",
				$ptr,
				'OutputNotEscaped',
				array( $content )
			);
		}

		return $end;
	}

	/**
	 * Check whether there is a ternary token at the right nesting level in an arbitrary set of tokens.
	 *
	 * @since 3.0.0 Split off from the process_token() method.
	 *
	 * @param int $start The position to start checking from.
	 * @param int $end   The position to stop the check at.
	 *
	 * @return int|false Stack pointer to the ternary or FALSE if no ternary was found.
	 */
	private function find_ternary( $start, $end ) {
		$ternary = $this->phpcsFile->findNext( \T_INLINE_THEN, $start, $end );
		if ( false === $ternary ) {
			return false;
		}

		$target_nesting_level = 0;
		if ( empty( $this->tokens[ $start ]['nested_parenthesis'] ) === false ) {
			$target_nesting_level = \count( $this->tokens[ $start ]['nested_parenthesis'] );
		}

		if ( empty( $this->tokens[ $ternary ]['nested_parenthesis'] )
			&& 0 === $target_nesting_level
		) {
			return $ternary;
		}

		if ( empty( $this->tokens[ $ternary ]['nested_parenthesis'] ) === false
			&& \count( $this->tokens[ $ternary ]['nested_parenthesis'] ) === $target_nesting_level
		) {
			return $ternary;
		}

		return false;
	}
}
