<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\NamingConventions;

use WordPress\AbstractFunctionParameterSniff;
use WordPress\PHPCSHelper;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Verify that everything defined in the global namespace is prefixed with a theme/plugin specific prefix.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.12.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 *
 * @uses    \WordPress\Sniff::$custom_test_class_whitelist
 */
class PrefixAllGlobalsSniff extends AbstractFunctionParameterSniff {

	/**
	 * Error message template.
	 *
	 * @var string
	 */
	const ERROR_MSG = '%s by a theme/plugin should start with the theme/plugin prefix. Found: "%s".';

	/**
	 * Target prefixes.
	 *
	 * @since 0.12.0
	 *
	 * @var string[]|string
	 */
	public $prefixes = '';

	/**
	 * Prefix blacklist.
	 *
	 * @since 0.12.0
	 *
	 * @var string[]
	 */
	protected $prefix_blacklist = array(
		'wp' => true,
		'_'  => true,
	);

	/**
	 * Target prefixes after validation.
	 *
	 * @since 0.12.0
	 *
	 * @var string[]
	 */
	private $validated_prefixes = array();

	/**
	 * Cache of previously set prefixes.
	 *
	 * Prevents having to do the same prefix validation over and over again.
	 *
	 * @since 0.12.0
	 *
	 * @var string[]
	 */
	private $previous_prefixes = array();

	/**
	 * A list of all PHP superglobals with the exception of $GLOBALS which is handled separately.
	 *
	 * @since 0.12.0
	 *
	 * @var array
	 */
	protected $superglobals = array(
		'_COOKIE'  => true,
		'_ENV'     => true,
		'_GET'     => true,
		'_FILES'   => true,
		'_POST'    => true,
		'_REQUEST' => true,
		'_SERVER'  => true,
		'_SESSION' => true,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since 0.12.0
	 *
	 * @return array
	 */
	public function register() {
		$targets = array(
			T_FUNCTION  => T_FUNCTION,
			T_CLASS     => T_CLASS,
			T_INTERFACE => T_INTERFACE,
			T_TRAIT     => T_TRAIT,
			T_CONST     => T_CONST,
			T_VARIABLE  => T_VARIABLE,
			T_DOLLAR    => T_DOLLAR, // Variable variables.
		);

		// Add function call target for hook names and constants defined using define().
		$parent = parent::register();
		if ( ! empty( $parent ) ) {
			$targets[] = T_STRING;
		}

		return $targets;
	}

	/**
	 * Groups of functions to restrict.
	 *
	 * @since 0.12.0
	 *
	 * @return array
	 */
	public function getGroups() {
		$this->target_functions           = $this->hookInvokeFunctions;
		$this->target_functions['define'] = true;

		return parent::getGroups();
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @since 0.12.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {
		/*
		 * Allow for whitelisting.
		 *
		 * Generally speaking a theme/plugin should *only* execute their own hooks, but there may be a
		 * good reason to execute a core hook.
		 *
		 * Similarly, newer PHP or WP functions or constants may need to be emulated for continued support
		 * of older PHP and WP versions.
		 */
		if ( $this->has_whitelist_comment( 'prefix', $stackPtr ) ) {
			return;
		}

		// Allow overruling the prefixes set in a ruleset via the command line.
		$cl_prefixes = trim( PHPCSHelper::get_config_data( 'prefixes' ) );
		if ( ! empty( $cl_prefixes ) ) {
			$this->prefixes = $cl_prefixes;
		}

		$this->prefixes = $this->merge_custom_array( $this->prefixes, array(), false );
		if ( empty( $this->prefixes ) ) {
			// No prefixes passed, nothing to do.
			return;
		}

		$this->validate_prefixes();
		if ( empty( $this->validated_prefixes ) ) {
			// No _valid_ prefixes passed, nothing to do.
			return;
		}

		if ( T_STRING === $this->tokens[ $stackPtr ]['code'] ) {
			// Disallow excluding function groups for this sniff.
			$this->exclude = '';

			return parent::process_token( $stackPtr );

		} elseif ( T_DOLLAR === $this->tokens[ $stackPtr ]['code'] ) {

			return $this->process_variable_variable( $stackPtr );

		} elseif ( T_VARIABLE === $this->tokens[ $stackPtr ]['code'] ) {

			return $this->process_variable_assignment( $stackPtr );

		} else {

			// Namespaced methods, classes and constants do not need to be prefixed.
			$namespace = $this->determine_namespace( $stackPtr );
			if ( '' !== $namespace && '\\' !== $namespace ) {
				return;
			}

			$item_name  = '';
			$error_text = 'Unknown syntax used by';
			$error_code = 'NonPrefixedSyntaxFound';

			switch ( $this->tokens[ $stackPtr ]['type'] ) {
				case 'T_FUNCTION':
					// Methods in a class do not need to be prefixed.
					if ( $this->phpcsFile->hasCondition( $stackPtr, array( T_CLASS, T_ANON_CLASS, T_INTERFACE, T_TRAIT ) ) === true ) {
						return;
					}

					$item_name = $this->phpcsFile->getDeclarationName( $stackPtr );
					if ( function_exists( '\\' . $item_name ) ) {
						// Backfill for PHP native function.
						return;
					}

					$error_text = 'Functions declared';
					$error_code = 'NonPrefixedFunctionFound';
					break;

				case 'T_CLASS':
				case 'T_INTERFACE':
				case 'T_TRAIT':
					// Ignore test classes.
					if ( true === $this->is_test_class( $stackPtr ) ) {
						if ( $this->tokens[ $stackPtr ]['scope_condition'] === $stackPtr && isset( $this->tokens[ $stackPtr ]['scope_closer'] ) ) {
							// Skip forward to end of test class.
							return $this->tokens[ $stackPtr ]['scope_closer'];
						}
						return;
					}

					$item_name  = $this->phpcsFile->getDeclarationName( $stackPtr );
					$error_text = 'Classes declared';
					$error_code = 'NonPrefixedClassFound';

					switch ( $this->tokens[ $stackPtr ]['type'] ) {
						case 'T_CLASS':
							if ( class_exists( '\\' . $item_name ) ) {
								// Backfill for PHP native class.
								return;
							}
							break;

						case 'T_INTERFACE':
							if ( interface_exists( '\\' . $item_name ) ) {
								// Backfill for PHP native interface.
								return;
							}

							$error_text = 'Interfaces declared';
							$error_code = 'NonPrefixedInterfaceFound';
							break;

						case 'T_TRAIT':
							if ( function_exists( '\trait_exists' ) && trait_exists( '\\' . $item_name ) ) {
								// Backfill for PHP native trait.
								return;
							}

							$error_text = 'Traits declared';
							$error_code = 'NonPrefixedTraitFound';
							break;

						default:
							// Left empty on purpose.
							break;
					}

					break;

				case 'T_CONST':
					// Constants in a class do not need to be prefixed.
					if ( $this->phpcsFile->hasCondition( $stackPtr, array( T_CLASS, T_ANON_CLASS, T_INTERFACE, T_TRAIT ) ) === true ) {
						return;
					}

					$constant_name_ptr = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true, null, true );
					if ( false === $constant_name_ptr ) {
						// Live coding.
						return;
					}

					$item_name = $this->tokens[ $constant_name_ptr ]['content'];
					if ( defined( '\\' . $item_name ) ) {
						// Backfill for PHP native constant.
						return;
					}

					$error_text = 'Global constants defined';
					$error_code = 'NonPrefixedConstantFound';
					break;

				default:
					// Left empty on purpose.
					break;

			}

			if ( empty( $item_name ) || $this->is_prefixed( $item_name ) === true ) {
				return;
			}

			$this->phpcsFile->addError(
				self::ERROR_MSG,
				$stackPtr,
				$error_code,
				array(
					$error_text,
					$item_name,
				)
			);
		}

	} // End process_token().

	/**
	 * Handle variable variable defined in the global namespace.
	 *
	 * @since 0.12.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	protected function process_variable_variable( $stackPtr ) {
		static $indicators = array(
			T_OPEN_CURLY_BRACKET => true,
			T_VARIABLE           => true,
		);

		// Is this a variable variable ?
		// Not concerned with nested ones as those will be recognized on their own token.
		$next_non_empty = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true, null, true );
		if ( false === $next_non_empty || ! isset( $indicators[ $this->tokens[ $next_non_empty ]['code'] ] ) ) {
			return;
		}

		if ( T_OPEN_CURLY_BRACKET === $this->tokens[ $next_non_empty ]['code']
			&& isset( $this->tokens[ $next_non_empty ]['bracket_closer'] )
		) {
			// Skip over the variable part.
			$next_non_empty = $this->tokens[ $next_non_empty ]['bracket_closer'];
		}

		$maybe_assignment = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $next_non_empty + 1 ), null, true, null, true );

		while ( false !== $maybe_assignment
			&& T_OPEN_SQUARE_BRACKET === $this->tokens[ $maybe_assignment ]['code']
			&& isset( $this->tokens[ $maybe_assignment ]['bracket_closer'] )
		) {
			$maybe_assignment = $this->phpcsFile->findNext(
				Tokens::$emptyTokens,
				( $this->tokens[ $maybe_assignment ]['bracket_closer'] + 1 ),
				null,
				true,
				null,
				true
			);
		}

		if ( false === $maybe_assignment ) {
			return;
		}

		if ( ! isset( Tokens::$assignmentTokens[ $this->tokens[ $maybe_assignment ]['code'] ] ) ) {
			// Not an assignment.
			return;
		}

		$error = self::ERROR_MSG;

		/*
		 * Local variable variables in a function do not need to be prefixed.
		 * But a variable variable could evaluate to the name of an imported global
		 * variable.
		 * Not concerned with imported variable variables (global.. ) as that has been
		 * forbidden since PHP 7.0. Presuming cross-version code and if not, that
		 * is for the PHPCompatibility standard to detect.
		 */
		if ( $this->phpcsFile->hasCondition( $stackPtr, array( T_FUNCTION, T_CLOSURE ) ) === true ) {
			$condition = $this->phpcsFile->getCondition( $stackPtr, T_FUNCTION );
			if ( false === $condition ) {
				$condition = $this->phpcsFile->getCondition( $stackPtr, T_CLOSURE );
			}

			$has_global = $this->phpcsFile->findPrevious( T_GLOBAL, ( $stackPtr - 1 ), $this->tokens[ $condition ]['scope_opener'] );
			if ( false === $has_global ) {
				// No variable import happening.
				return;
			}

			$error = 'Variable variable which could potentially override an imported global variable detected. ' . $error;
		}

		$variable_name = $this->phpcsFile->getTokensAsString( $stackPtr, ( ( $next_non_empty - $stackPtr ) + 1 ) );

		// Still here ? In that case, the variable name should be prefixed.
		$this->phpcsFile->addWarning(
			$error,
			$stackPtr,
			'NonPrefixedVariableFound',
			array(
				'Variables defined',
				$variable_name,
			)
		);

		// Skip over the variable part of the variable.
		return ( $next_non_empty + 1 );
	}

	/**
	 * Check that defined global variables are prefixed.
	 *
	 * @since 0.12.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	protected function process_variable_assignment( $stackPtr ) {

		// We're only concerned with variables which are being defined.
		// `is_assigment()` will not recognize property assignments, which is good in this case.
		if ( false === $this->is_assignment( $stackPtr ) ) {
			return;
		}

		$is_error      = true;
		$variable_name = substr( $this->tokens[ $stackPtr ]['content'], 1 ); // Strip the dollar sign.

		// Bow out early if we know for certain no prefix is needed.
		if ( $this->variable_prefixed_or_whitelisted( $variable_name ) === true ) {
			return;
		}

		if ( 'GLOBALS' === $variable_name ) {
			$array_open = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true, null, true );
			if ( false === $array_open || T_OPEN_SQUARE_BRACKET !== $this->tokens[ $array_open ]['code'] ) {
				// Live coding or something very silly.
				return;
			}

			$array_key = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $array_open + 1 ), null, true, null, true );
			if ( false === $array_key ) {
				// No key found, nothing to do.
				return;
			}

			$stackPtr      = $array_key;
			$variable_name = $this->strip_quotes( $this->tokens[ $array_key ]['content'] );

			// Check whether a prefix is needed.
			if ( isset( Tokens::$stringTokens[ $this->tokens[ $array_key ]['code'] ] )
				&& $this->variable_prefixed_or_whitelisted( $variable_name ) === true
			) {
				return;
			}

			if ( T_DOUBLE_QUOTED_STRING === $this->tokens[ $array_key ]['code'] ) {
				// If the array key is a double quoted string, try again with only
				// the part before the first variable (if any).
				$exploded = explode( '$', $variable_name );
				$first    = rtrim( $exploded[0], '{' );
				if ( '' !== $first ) {
					if ( $this->variable_prefixed_or_whitelisted( $first ) === true ) {
						return;
					}
				} else {
					// If the first part was dynamic, throw a warning.
					$is_error = false;
				}
			} elseif ( ! isset( Tokens::$stringTokens[ $this->tokens[ $array_key ]['code'] ] ) ) {
				// Dynamic array key, throw a warning.
				$is_error = false;
			}
		} else {
			// Function parameters do not need to be prefixed.
			if ( isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
				foreach ( $this->tokens[ $stackPtr ]['nested_parenthesis'] as $opener => $closer ) {
					if ( isset( $this->tokens[ $opener ]['parenthesis_owner'] ) && T_FUNCTION === $this->tokens[ $this->tokens[ $opener ]['parenthesis_owner'] ]['code'] ) {
						return;
					}
				}
				unset( $opener, $closer );
			}

			// Properties in a class do not need to be prefixed.
			$conditions = array_keys( $this->tokens[ $stackPtr ]['conditions'] );
			$ptr        = array_pop( $conditions );
			if ( isset( $this->tokens[ $ptr ] )
				&& in_array( $this->tokens[ $ptr ]['code'], array( T_CLASS, T_ANON_CLASS, T_TRAIT ), true )
			) {
				return;
			}

			// Local variables in a function do not need to be prefixed unless they are being imported.
			if ( $this->phpcsFile->hasCondition( $stackPtr, array( T_FUNCTION, T_CLOSURE ) ) === true ) {
				$condition = $this->phpcsFile->getCondition( $stackPtr, T_FUNCTION );
				if ( false === $condition ) {
					$condition = $this->phpcsFile->getCondition( $stackPtr, T_CLOSURE );
				}

				$has_global = $this->phpcsFile->findPrevious( T_GLOBAL, ( $stackPtr - 1 ), $this->tokens[ $condition ]['scope_opener'] );
				if ( false === $has_global ) {
					// No variable import happening.
					return;
				}

				// Ok, this may be an imported global variable.
				$end_of_statement = $this->phpcsFile->findNext( T_SEMICOLON, ( $has_global + 1 ) );
				if ( false === $end_of_statement ) {
					// No semi-colon - live coding.
					return;
				}

				for ( $ptr = ( $has_global + 1 ); $ptr <= $end_of_statement; $ptr++ ) {
					// Move the stack pointer to the next variable.
					$ptr = $this->phpcsFile->findNext( T_VARIABLE, $ptr, $end_of_statement, false, null, true );

					if ( false === $ptr ) {
						// Reached the end of the global statement without finding the variable,
						// so this must be a local variable.
						return;
					}

					if ( substr( $this->tokens[ $ptr ]['content'], 1 ) === $variable_name ) {
						break;
					}
				}

				unset( $condition, $has_global, $end_of_statement, $ptr, $imported );

			}
		}

		// Still here ? In that case, the variable name should be prefixed.
		$this->addMessage(
			self::ERROR_MSG,
			$stackPtr,
			$is_error,
			'NonPrefixedVariableFound',
			array(
				'Variables defined',
				'$' . $variable_name,
			)
		);

	} // End process_variable_assignment().

	/**
	 * Process the parameters of a matched function.
	 *
	 * @since 0.12.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param array  $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched.
	 * @param array  $parameters      Array with information about the parameters.
	 *
	 * @return void
	 */
	public function process_parameters( $stackPtr, $group_name, $matched_content, $parameters ) {

		// Ignore deprecated hook names.
		if ( strpos( $matched_content, '_deprecated' ) > 0 ) {
			return;
		}

		// No matter whether it is a constant definition or a hook call, both use the first parameter.
		if ( ! isset( $parameters[1] ) ) {
			return;
		}

		$is_error     = true;
		$raw_content = $this->strip_quotes( $parameters[1]['raw'] );

		if ( $this->is_prefixed( $raw_content ) === true ) {
			return;
		} else {
			// This may be a dynamic hook/constant name.
			$first_non_empty = $this->phpcsFile->findNext(
				Tokens::$emptyTokens,
				$parameters[1]['start'],
				( $parameters[1]['end'] + 1 ),
				true
			);

			if ( false === $first_non_empty ) {
				return;
			}

			$first_non_empty_content = $this->strip_quotes( $this->tokens[ $first_non_empty ]['content'] );

			// Try again with just the first token if it's a text string.
			if ( isset( Tokens::$stringTokens[ $this->tokens[ $first_non_empty ]['code'] ] )
				&& $this->is_prefixed( $first_non_empty_content ) === true
			) {
				return;
			}

			if ( T_DOUBLE_QUOTED_STRING === $this->tokens[ $first_non_empty ]['code'] ) {
				// If the first part of the parameter is a double quoted string, try again with only
				// the part before the first variable (if any).
				$exploded                = explode( '$', $first_non_empty_content );
				$first                   = rtrim( $exploded[0], '{' );
				if ( '' !== $first ) {
					if ( $this->is_prefixed( $first ) === true ) {
						return;
					}
				} else {
					// Start of hook/constant name is dynamic, throw a warning.
					$is_error = false;
				}
			} elseif ( ! isset( Tokens::$stringTokens[ $this->tokens[ $first_non_empty ]['code'] ] ) ) {
				// Dynamic hook/constant name, throw a warning.
				$is_error = false;
			}
		}

		if ( 'define' === $matched_content ) {
			if ( defined( '\\' . $raw_content ) ) {
				// Backfill for PHP native constant.
				return;
			}

			$data       = array( 'Global constants defined' );
			$error_code = 'NonPrefixedConstantFound';
		} else {
			$data       = array( 'Hook names invoked' );
			$error_code = 'NonPrefixedHooknameFound';
		}

		$data[] = $raw_content;

		$this->addMessage( self::ERROR_MSG, $parameters[1]['start'], $is_error, $error_code, $data );

	} // End process_parameters().

	/**
	 * Check if a function/class/constant/variable name is prefixed with one of the expected prefixes.
	 *
	 * @since 0.12.0
	 *
	 * @param string $name Name to check for a prefix.
	 *
	 * @return bool True when the name is the prefix or starts with the prefix + an underscore.
	 *              False otherwise.
	 */
	private function is_prefixed( $name ) {

		foreach ( $this->validated_prefixes as $prefix ) {
			if ( strtolower( $name ) === $prefix ) {
				// Ok, prefix *is* the hook/constant name.
				return true;

			} else {
				$prefix_found = stripos( $name, $prefix . '_' );

				if ( 0 === $prefix_found ) {
					// Ok, prefix found as start of hook/constant name.
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Check if a variable name might need a prefix.
	 *
	 * Prefix is not needed for:
	 * - superglobals,
	 * - WP native globals,
	 * - variables which are already prefixed.
	 *
	 * @param string $name Variable name without the dollar sign.
	 * @return bool True if the variable name is whitelisted or already prefixed.
	 *              False otherwise.
	 */
	private function variable_prefixed_or_whitelisted( $name ) {
		// Ignore superglobals and WP global variables.
		if ( isset( $this->superglobals[ $name ] ) || isset( $this->wp_globals[ $name ] ) ) {
			return true;
		}

		return $this->is_prefixed( $name );
	}

	/**
	 * Validate an array of prefixes as passed through a custom property or via the command line.
	 *
	 * Checks that the prefix:
	 * - is not one of the blacklisted ones.
	 * - complies with the PHP rules for valid function, class, variable, constant names.
	 *
	 * @since 0.12.0
	 */
	private function validate_prefixes() {
		if ( $this->previous_prefixes === $this->prefixes ) {
			return;
		}

		// Set the cache *before* validation so as to not break the above compare.
		$this->previous_prefixes = $this->prefixes;

		// Validate the passed prefix(es).
		foreach ( $this->prefixes as $key => $prefix ) {
			$prefixLC = strtolower( $prefix );

			if ( isset( $this->prefix_blacklist[ $prefixLC ] ) ) {
				$this->phpcsFile->addError(
					'The "%s" prefix is not allowed.',
					0,
					'ForbiddenPrefixPassed',
					array( $prefix )
				);
				unset( $this->prefixes[ $key ] );
				continue;
			}

			// Validate the prefix against characters allowed for function, class, constant names etc.
			if ( preg_match( '`^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$`', $prefix ) !== 1 ) {
				$this->phpcsFile->addError(
					'The "%s" prefix is not a valid function/class/variable/constant prefix in PHP.',
					0,
					'InvalidPrefixPassed',
					array( $prefix )
				);
				unset( $this->prefixes[ $key ] );
			}

			// Lowercase the prefix to allow for direct compare.
			$this->prefixes[ $key ] = $prefixLC;
		}

		// Set the validated prefixes cache.
		$this->validated_prefixes = $this->prefixes;

	} // End validate_prefixes().

} // End class.
