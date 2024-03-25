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
use PHPCSUtils\Utils\Arrays;
use PHPCSUtils\Utils\Conditions;
use PHPCSUtils\Utils\Operators;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\AbstractFunctionRestrictionsSniff;
use WordPressCS\WordPress\Helpers\ArrayWalkingFunctionsHelper;
use WordPressCS\WordPress\Helpers\ConstantsHelper;
use WordPressCS\WordPress\Helpers\ContextHelper;
use WordPressCS\WordPress\Helpers\EscapingFunctionsTrait;
use WordPressCS\WordPress\Helpers\FormattingFunctionsHelper;
use WordPressCS\WordPress\Helpers\PrintingFunctionsTrait;
use WordPressCS\WordPress\Helpers\VariableHelper;

/**
 * Verifies that all outputted strings are escaped.
 *
 * @link https://developer.wordpress.org/apis/security/data-validation/ WordPress Developer Docs on Data Validation.
 *
 * @since 2013-06-11
 * @since 0.4.0  This class now extends the WordPressCS native `Sniff` class.
 * @since 0.5.0  The various function list properties which used to be contained in this class
 *               have been moved to the WordPressCS native `Sniff` parent class.
 * @since 0.12.0 This sniff will now also check for output escaping when using shorthand
 *               echo tags `<?=`.
 * @since 0.13.0 Class name changed: this class is now namespaced.
 * @since 1.0.0  This sniff has been moved from the `XSS` category to the `Security` category.
 * @since 3.0.0  This class now extends the WordPressCS native
 *               `AbstractFunctionRestrictionsSniff` class.
 *               The parent `exclude` property is disabled.
 *
 * @uses \WordPressCS\WordPress\Helpers\EscapingFunctionsTrait::$customEscapingFunctions
 * @uses \WordPressCS\WordPress\Helpers\EscapingFunctionsTrait::$customAutoEscapedFunctions
 * @uses \WordPressCS\WordPress\Helpers\PrintingFunctionsTrait::$customPrintingFunctions
 */
class EscapeOutputSniff extends AbstractFunctionRestrictionsSniff {

	use EscapingFunctionsTrait;
	use PrintingFunctionsTrait;

	/**
	 * Printing functions that incorporate unsafe values.
	 *
	 * @since 0.4.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 * @since 3.0.0  The format of the array values has changed from plain string to array.
	 *
	 * @var array<string, array>
	 */
	protected $unsafePrintingFunctions = array(
		'_e'  => array(
			'alternative' => 'esc_html_e() or esc_attr_e()',
			'params'      => array(
				1 => 'text',
			),
		),
		'_ex' => array(
			'alternative' => 'echo esc_html_x() or echo esc_attr_x()',
			'params'      => array(
				1 => 'text',
			),
		),
	);

	/**
	 * List of names of the native PHP constants which can be considered safe.
	 *
	 * @since 1.0.0
	 *
	 * @var array<string, bool>
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
	 * @var array<string|int, string|int>
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
	 * List of keyword tokens this sniff listens for, which can also be used as an inline expression.
	 *
	 * @since 3.0.0
	 *
	 * @var array<string|int, string|int>
	 */
	private $target_keywords = array(
		\T_EXIT  => \T_EXIT,
		\T_PRINT => \T_PRINT,
		\T_THROW => \T_THROW,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return string|int[]
	 */
	public function register() {
		// Enrich the list of "safe components" tokens.
		$this->safe_components += Tokens::$comparisonTokens;
		$this->safe_components += Tokens::$operators;
		$this->safe_components += Tokens::$booleanOperators;
		$this->safe_components += Collections::incrementDecrementOperators();

		// Set up the tokens the sniff should listen to.
		$targets   = array_merge( parent::register(), $this->target_keywords );
		$targets[] = \T_ECHO;
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
		// Make sure all array keys are lowercase (could contain user-provided function names).
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
		$start = ( $stackPtr + 1 );
		$end   = $start;

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

			case \T_THROW:
				// Find the open parentheses, while stepping over the exception creation tokens.
				$ignore  = Tokens::$emptyTokens;
				$ignore += Collections::namespacedNameTokens();
				$ignore += Collections::functionCallTokens();
				$ignore += Collections::objectOperators();

				$next_relevant = $this->phpcsFile->findNext( $ignore, ( $stackPtr + 1 ), null, true );
				if ( false === $next_relevant ) {
					return;
				}

				if ( \T_NEW === $this->tokens[ $next_relevant ]['code'] ) {
					$next_relevant = $this->phpcsFile->findNext( $ignore, ( $next_relevant + 1 ), null, true );
					if ( false === $next_relevant ) {
						return;
					}
				}

				if ( \T_OPEN_PARENTHESIS !== $this->tokens[ $next_relevant ]['code']
					|| isset( $this->tokens[ $next_relevant ]['parenthesis_closer'] ) === false
				) {
					// Live coding/parse error or a pre-created exception. Nothing to do for us.
					return;
				}

				$end = $this->tokens[ $next_relevant ]['parenthesis_closer'];

				// Check if the throw is within a `try-catch`.
				// Doing this here (instead of earlier) to allow skipping to the end of the statement.
				$search_for           = Collections::closedScopes();
				$search_for[ \T_TRY ] = \T_TRY;

				$last_condition = Conditions::getLastCondition( $this->phpcsFile, $stackPtr, $search_for );
				if ( false !== $last_condition && \T_TRY === $this->tokens[ $last_condition ]['code'] ) {
					// This exception will (probably) be caught, so ignore it.
					return $end;
				}

				$call_token = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $next_relevant - 1 ), null, true );
				$params     = PassedParameters::getParameters( $this->phpcsFile, $call_token );
				if ( empty( $params ) ) {
					// No parameters passed, nothing to do.
					return $end;
				}

				// Examine each parameter individually.
				foreach ( $params as $param ) {
					$this->check_code_is_escaped( $param['start'], ( $param['end'] + 1 ), 'ExceptionNotEscaped' );
				}

				return $end;

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

		return $this->check_code_is_escaped( $start, $end );
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
		$next_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );
		if ( false === $next_non_empty
			|| \T_OPEN_PARENTHESIS !== $this->tokens[ $next_non_empty ]['code']
			|| isset( $this->tokens[ $next_non_empty ]['parenthesis_closer'] ) === false
		) {
			// Live coding, parse error or not a function _call_.
			return;
		}

		$end = $this->tokens[ $next_non_empty ]['parenthesis_closer'];

		if ( 'unsafe_printing_functions' === $group_name ) {
			$error = $this->phpcsFile->addError(
				"All output should be run through an escaping function (like %s), found '%s'.",
				$stackPtr,
				'UnsafePrintingFunction',
				array( $this->unsafePrintingFunctions[ $matched_content ]['alternative'], $matched_content )
			);

			// If the error was reported, don't bother checking the function's arguments.
			if ( $error || empty( $this->unsafePrintingFunctions[ $matched_content ]['params'] ) ) {
				return $end;
			}

			// If the function was not reported for being unsafe, examine the relevant parameters.
			$params = PassedParameters::getParameters( $this->phpcsFile, $stackPtr );
			foreach ( $this->unsafePrintingFunctions[ $matched_content ]['params'] as $position => $name ) {
				$param = PassedParameters::getParameterFromStack( $params, $position, $name );
				if ( false === $param ) {
					// Parameter doesn't exist. Nothing to do.
					continue;
				}

				$this->check_code_is_escaped( $param['start'], ( $param['end'] + 1 ) );
			}

			return $end;
		}

		$params = PassedParameters::getParameters( $this->phpcsFile, $stackPtr );

		/*
		 * These functions only need to have their first argument - `$message` - escaped.
		 * Note: user_error() is an alias for trigger_error(), so the param names are the same.
		 */
		if ( 'trigger_error' === $matched_content || 'user_error' === $matched_content ) {
			$message_param = PassedParameters::getParameterFromStack( $params, 1, 'message' );
			if ( false === $message_param ) {
				// Message parameter doesn't exist. Nothing to do.
				return $end;
			}

			return $this->check_code_is_escaped( $message_param['start'], ( $message_param['end'] + 1 ) );
		}

		/*
		 * If the first param to `_deprecated_file()` - `$file` - follows the typical `basename( __FILE__ )`
		 * pattern, it doesn't need to be escaped.
		 */
		if ( '_deprecated_file' === $matched_content ) {
			$file_param = PassedParameters::getParameterFromStack( $params, 1, 'file' );

			if ( false !== $file_param ) {
				// Check for a particular code pattern which can safely be ignored.
				if ( preg_match( '`^[\\\\]?basename\s*\(\s*__FILE__\s*\)$`', $file_param['clean'] ) === 1 ) {
					unset( $params[1], $params['file'] ); // Remove the param, whether passed positionally or named.
				}
			}
			unset( $file_param );
		}

		// Examine each parameter individually.
		foreach ( $params as $param ) {
			$this->check_code_is_escaped( $param['start'], ( $param['end'] + 1 ) );
		}

		return $end;
	}

	/**
	 * Check whether each relevant part of an arbitrary group of token is output escaped.
	 *
	 * @since 3.0.0 Split off from the process_token() method.
	 *
	 * @param int    $start The position to start checking from.
	 * @param int    $end   The position to stop the check at.
	 * @param string $code  Code to use for the PHPCS error.
	 *
	 * @return int Integer stack pointer to skip forward.
	 */
	protected function check_code_is_escaped( $start, $end, $code = 'OutputNotEscaped' ) {
		/*
		 * Check for a ternary operator.
		 * We only need to do this here if this statement is lacking parenthesis.
		 * Otherwise it will be handled in the below loop.
		 */
		$ternary        = false;
		$next_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $start + 1 ), null, true );
		$last_non_empty = $this->phpcsFile->findPrevious( Tokens::$emptyTokens, ( $end - 1 ), null, true );

		if ( \T_OPEN_PARENTHESIS !== $this->tokens[ $next_non_empty ]['code']
			|| \T_CLOSE_PARENTHESIS !== $this->tokens[ $last_non_empty ]['code']
			|| ( \T_OPEN_PARENTHESIS === $this->tokens[ $next_non_empty ]['code']
				&& \T_CLOSE_PARENTHESIS === $this->tokens[ $last_non_empty ]['code']
				&& isset( $this->tokens[ $next_non_empty ]['parenthesis_closer'] )
				&& $this->tokens[ $next_non_empty ]['parenthesis_closer'] !== $last_non_empty
			)
		) {
			// If there is a (long) ternary, skip over the part before the ?.
			$ternary = $this->find_long_ternary( $start, $end );
			if ( false !== $ternary ) {
				$start = ( $ternary + 1 );
			}
		}

		$in_cast = false;
		$watch   = true;

		// Looping through echo'd components.
		for ( $i = $start; $i < $end; $i++ ) {
			// Ignore whitespaces and comments.
			if ( isset( Tokens::$emptyTokens[ $this->tokens[ $i ]['code'] ] ) ) {
				continue;
			}

			// Skip over irrelevant tokens.
			if ( isset( Tokens::$magicConstants[ $this->tokens[ $i ]['code'] ] ) // Magic constants for debug functions.
				|| \T_NS_SEPARATOR === $this->tokens[ $i ]['code']
				|| \T_DOUBLE_ARROW === $this->tokens[ $i ]['code']
				|| \T_CLOSE_PARENTHESIS === $this->tokens[ $i ]['code']
			) {
				continue;
			}

			if ( \T_OPEN_PARENTHESIS === $this->tokens[ $i ]['code'] ) {
				if ( ! isset( $this->tokens[ $i ]['parenthesis_closer'] ) ) {
					// Live coding or parse error.
					break;
				}

				if ( $in_cast ) {
					// Skip to the end of a function call if it has been cast to a safe value.
					$i       = $this->tokens[ $i ]['parenthesis_closer'];
					$in_cast = false;

				} else {
					// Skip over the condition part of a (long) ternary (i.e., to after the ?).
					$ternary = $this->find_long_ternary( ( $i + 1 ), $this->tokens[ $i ]['parenthesis_closer'] );
					if ( false !== $ternary ) {
						$i = $ternary;
					}
				}

				continue;
			}

			/*
			 * If a keyword is encountered in an inline expression and the keyword is one
			 * this sniff listens to, recurse into the sniff, handle the expression
			 * based on the keyword and skip over the code examined.
			 */
			if ( isset( $this->target_keywords[ $this->tokens[ $i ]['code'] ] ) ) {
				$return_value = $this->process_token( $i );
				if ( isset( $return_value ) ) {
					$i = $return_value;
				}
				continue;
			}

			// Handle PHP 8.0+ match expressions.
			if ( \T_MATCH === $this->tokens[ $i ]['code'] ) {
				$match_valid = $this->walk_match_expression( $i, $code );
				if ( false === $match_valid ) {
					// Live coding or parse error. Shouldn't be possible as PHP[CS] will tokenize the keyword as `T_STRING` in that case.
					break; // @codeCoverageIgnore
				}

				$i = $match_valid;
				continue;
			}

			// Examine the items in an array individually for array parameters.
			if ( isset( Collections::arrayOpenTokensBC()[ $this->tokens[ $i ]['code'] ] ) ) {
				$array_open_close = Arrays::getOpenClose( $this->phpcsFile, $i );
				if ( false === $array_open_close ) {
					// Short list or misidentified short array token.
					continue;
				}

				$array_items = PassedParameters::getParameters( $this->phpcsFile, $i, 0, true );
				if ( ! empty( $array_items ) ) {
					foreach ( $array_items as $array_item ) {
						$this->check_code_is_escaped( $array_item['start'], ( $array_item['end'] + 1 ), $code );
					}
				}

				$i = $array_open_close['closer'];
				continue;
			}

			// Ignore safe PHP native constants.
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

			// Check for use of *::class.
			if ( \T_STRING === $this->tokens[ $i ]['code']
				|| \T_VARIABLE === $this->tokens[ $i ]['code']
				|| isset( Collections::ooHierarchyKeywords()[ $this->tokens[ $i ]['code'] ] )
			) {
				$double_colon = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $i + 1 ), $end, true );
				if ( false !== $double_colon
					&& \T_DOUBLE_COLON === $this->tokens[ $double_colon ]['code']
				) {
					$class_keyword = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $double_colon + 1 ), $end, true );
					if ( false !== $class_keyword
						&& \T_STRING === $this->tokens[ $class_keyword ]['code']
						&& 'class' === strtolower( $this->tokens[ $class_keyword ]['content'] )
					) {
						$i = $class_keyword;
						continue;
					}
				}
			}

			$watch = false;

			// Allow int/double/bool casted variables.
			if ( isset( ContextHelper::get_safe_cast_tokens()[ $this->tokens[ $i ]['code'] ] ) ) {
				/*
				 * If the next thing is a match expression, skip over it as whatever is
				 * being returned will be safely cast.
				 * Do not set `$in_cast` to `true`.
				 */
				$next_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $i + 1 ), $end, true );
				if ( false !== $next_non_empty
					&& \T_MATCH === $this->tokens[ $next_non_empty ]['code']
					&& isset( $this->tokens[ $next_non_empty ]['scope_closer'] )
				) {
					$i = $this->tokens[ $next_non_empty ]['scope_closer'];
					continue;
				}

				$in_cast = true;
				continue;
			}

			// Handle heredocs separately as they only need escaping when interpolation is used.
			if ( \T_START_HEREDOC === $this->tokens[ $i ]['code'] ) {
				$current = ( $i + 1 );
				while ( isset( $this->tokens[ $current ] ) && \T_HEREDOC === $this->tokens[ $current ]['code'] ) {
					$embeds = TextStrings::getEmbeds( $this->tokens[ $current ]['content'] );
					if ( ! empty( $embeds ) ) {
						$this->phpcsFile->addError(
							'All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found interpolation in unescaped heredoc.',
							$current,
							'HeredocOutputNotEscaped'
						);
					}
					++$current;
				}

				$i = $current;
				continue;
			}

			// Now check that the next token is a function call.
			if ( \T_STRING === $this->tokens[ $i ]['code'] ) {
				$ptr                    = $i;
				$functionName           = $this->tokens[ $i ]['content'];
				$function_opener        = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $i + 1 ), null, true );
				$is_formatting_function = FormattingFunctionsHelper::is_formatting_function( $functionName );

				if ( false !== $function_opener
					&& \T_OPEN_PARENTHESIS === $this->tokens[ $function_opener ]['code']
				) {
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

					// If this is a formatting function, we examine the parameters individually.
					if ( $is_formatting_function ) {
						$formatting_params = PassedParameters::getParameters( $this->phpcsFile, $i );
						if ( ! empty( $formatting_params ) ) {
							foreach ( $formatting_params as $format_param ) {
								$this->check_code_is_escaped( $format_param['start'], ( $format_param['end'] + 1 ), $code );
							}
						}

						$watch = true;
					}

					// Skip pointer to after the function.
					if ( isset( $this->tokens[ $function_opener ]['parenthesis_closer'] ) ) {
						$i = $this->tokens[ $function_opener ]['parenthesis_closer'];
					} else {
						// Live coding or parse error.
						break;
					}
				}

				// If this is a safe function, we don't flag it.
				if ( $is_formatting_function
					|| $this->is_escaping_function( $functionName )
					|| $this->is_auto_escaped_function( $functionName )
				) {
					// Special case get_search_query() which is unsafe if $escaped = false.
					if ( 'get_search_query' === strtolower( $functionName ) ) {
						$escaped_param = PassedParameters::getParameter( $this->phpcsFile, $ptr, 1, 'escaped' );
						if ( false !== $escaped_param && 'true' !== $escaped_param['clean'] ) {
							$this->phpcsFile->addError(
								'Output from get_search_query() is unsafe due to $escaped parameter being set to "false".',
								$ptr,
								'UnsafeSearchQuery'
							);
						}
					}

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
				$code,
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
	 * @return int|false Stack pointer to the ternary or FALSE if no ternary was found or
	 *                   if this is a short ternary.
	 */
	private function find_long_ternary( $start, $end ) {
		for ( $i = $start; $i < $end; $i++ ) {
			// Ignore anything within square brackets.
			if ( isset( $this->tokens[ $i ]['bracket_opener'], $this->tokens[ $i ]['bracket_closer'] )
				&& $i === $this->tokens[ $i ]['bracket_opener']
			) {
				$i = $this->tokens[ $i ]['bracket_closer'];
				continue;
			}

			// Skip past nested arrays, function calls and arbitrary groupings.
			if ( \T_OPEN_PARENTHESIS === $this->tokens[ $i ]['code']
				&& isset( $this->tokens[ $i ]['parenthesis_closer'] )
			) {
				$i = $this->tokens[ $i ]['parenthesis_closer'];
				continue;
			}

			// Skip past closures, anonymous classes and anything else scope related.
			if ( isset( $this->tokens[ $i ]['scope_condition'], $this->tokens[ $i ]['scope_closer'] )
				&& $this->tokens[ $i ]['scope_condition'] === $i
			) {
				$i = $this->tokens[ $i ]['scope_closer'];
				continue;
			}

			if ( \T_INLINE_THEN !== $this->tokens[ $i ]['code'] ) {
				continue;
			}

			/*
			 * Okay, we found a ternary and it should be at the correct nesting level.
			 * If this is a short ternary, it shouldn't be ignored though.
			 */
			if ( Operators::isShortTernary( $this->phpcsFile, $i ) === true ) {
				return false;
			}

			return $i;
		}

		return false;
	}

	/**
	 * Examine a match expression and only check for escaping in the "returned" parts of the match expression.
	 *
	 * {@internal PHPCSUtils will likely contain a utility for parsing match expressions in the future.
	 *            Ref: https://github.com/PHPCSStandards/PHPCSUtils/issues/497}
	 *
	 * @since 3.0.0
	 *
	 * @param int    $stackPtr Pointer to a T_MATCH token.
	 * @param string $code     Code to use for the PHPCS error.
	 *
	 * @return int|false Stack pointer to skip to or FALSE if the match expression contained a parse error.
	 */
	private function walk_match_expression( $stackPtr, $code ) {
		if ( ! isset( $this->tokens[ $stackPtr ]['scope_opener'], $this->tokens[ $stackPtr ]['scope_closer'] ) ) {
			// Parse error/live coding. Shouldn't be possible as PHP[CS] will tokenize the keyword as `T_STRING` in that case.
			return false; // @codeCoverageIgnore
		}

		$current = $this->tokens[ $stackPtr ]['scope_opener'];
		$end     = $this->tokens[ $stackPtr ]['scope_closer'];
		do {
			$current = $this->phpcsFile->findNext( \T_MATCH_ARROW, ( $current + 1 ), $end );
			if ( false === $current ) {
				// We must have reached the last match item (or there is a parse error).
				break;
			}

			$item_start = ( $current + 1 );
			$item_end   = false;

			// Find the first comma at the same level.
			for ( $i = $item_start; $i <= $end; $i++ ) {
				// Ignore anything within square brackets.
				if ( isset( $this->tokens[ $i ]['bracket_opener'], $this->tokens[ $i ]['bracket_closer'] )
					&& $i === $this->tokens[ $i ]['bracket_opener']
				) {
					$i = $this->tokens[ $i ]['bracket_closer'];
					continue;
				}

				// Skip past nested arrays, function calls and arbitrary groupings.
				if ( \T_OPEN_PARENTHESIS === $this->tokens[ $i ]['code']
					&& isset( $this->tokens[ $i ]['parenthesis_closer'] )
				) {
					$i = $this->tokens[ $i ]['parenthesis_closer'];
					continue;
				}

				// Skip past closures, anonymous classes and anything else scope related.
				if ( isset( $this->tokens[ $i ]['scope_condition'], $this->tokens[ $i ]['scope_closer'] )
					&& $this->tokens[ $i ]['scope_condition'] === $i
				) {
					$i = $this->tokens[ $i ]['scope_closer'];
					continue;
				}

				if ( \T_COMMA !== $this->tokens[ $i ]['code']
					&& $i !== $end
				) {
					continue;
				}

				$item_end = $i;
				break;
			}

			if ( false === $item_end ) {
				// Parse error/live coding. Shouldn't be possible.
				return false; // @codeCoverageIgnore
			}

			// Now check that the value returned by this match "leaf" is correctly escaped.
			$this->check_code_is_escaped( $item_start, $item_end, $code );

			// Independently of whether or not the check was successful or ran into (parse error) problems,
			// always skip to the identified end of the item.
			$current = $item_end;
		} while ( $current < $end );

		return $end;
	}
}
