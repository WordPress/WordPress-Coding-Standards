<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\WP;

use WordPress\AbstractFunctionParameterSniff;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * This checks the enqueued 4th and 5th parameters to make sure the version and in_footer are set.
 *
 * If a source ($src) value is passed, then version ($ver) needs to have non-falsy value.
 * If a source ($src) value is passed a check for in footer ($in_footer), warn the user if the value is falsy.
 *
 * @link https://developer.wordpress.org/reference/functions/wp_register_script/
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 * @link https://developer.wordpress.org/reference/functions/wp_register_style/
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since 1.0.0
 */
class EnqueuedResourceParametersSniff extends AbstractFunctionParameterSniff {

	/**
	 * The group name for this group of functions.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $group_name = 'Enqueued';

	/**
	 * List of enqueued functions that need to be checked for use of the in_footer and version arguments.
	 *
	 * @since 1.0.0
	 *
	 * @var array <string function_name> => <bool true>
	 */
	protected $target_functions = array(
		'wp_register_script' => true,
		'wp_enqueue_script'  => true,
		'wp_register_style'  => true,
		'wp_enqueue_style'   => true,
	);

	/**
	 * Process the parameters of a matched function.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param array  $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched.
	 * @param array  $parameters      Array with information about the parameters.
	 *
	 * @return void
	 */
	public function process_parameters( $stackPtr, $group_name, $matched_content, $parameters ) {

		// Check to see if a source ($src) is specified.
		if ( ! isset( $parameters[2] ) ) {
			return;
		}

		/*
		 * Version Check: Check to make sure the version is set explicitly.
		 */

		if ( ! isset( $parameters[4] ) || 'null' === $parameters[4]['raw'] ) {
			$type = 'script';
			if ( strpos( $matched_content, '_style' ) !== false ) {
				$type = 'style';
			}

			$this->phpcsFile->addError(
				'Resource version not set in call to %s(). This means new versions of the %s will not always be loaded due to browser caching.',
				$stackPtr,
				'MissingVersion',
				array( $matched_content, $type )
			);
		} else {
			// The version argument should have a non-false value.
			if ( $this->is_falsy( $parameters[4]['start'], $parameters[4]['end'] ) ) {
				$this->phpcsFile->addError(
					'Version parameter is not explicitly set or has been set to an equivalent of "false" for %s; ' .
					'This means that the WordPress core version will be used which is not recommended for plugin or theme development.',
					$stackPtr,
					'NoExplicitVersion',
					array( $matched_content )
				);
			}
		}

		/*
		 * In footer Check
		 *
		 * Check to make sure that $in_footer is set to true.
		 * It will warn the user to make sure it is intended.
		 *
		 * Only wp_register_script and wp_enqueue_script need this check,
		 * as this parameter is not available to wp_register_style and wp_enqueue_style.
		 */
		if ( 'wp_register_script' !== $matched_content && 'wp_enqueue_script' !== $matched_content ) {
			return;
		}

		if ( ! isset( $parameters[5] ) ) {
			// If in footer is not set, throw a warning about the default.
			$this->phpcsFile->addWarning(
				'In footer ($in_footer) is not set explicitly %s; ' .
				'It is recommended to load scripts in the footer. Please set this value to `true` to load it in the footer, or explicitly `false` if it should be loaded in the header.',
				$stackPtr,
				'NotInFooter',
				array( $matched_content )
			);
		}
	}

	/**
	 * Determine if a range has a falsy value.
	 *
	 * @param int $start The position to start looking from.
	 * @param int $end   The position to stop looking (inclusive).
	 *
	 * @return bool True if the parameter is falsy.
	 */
	protected function is_falsy( $start, $end ) {
		$falseTokens   = Tokens::$emptyTokens;
		$falseTokens[] = T_FALSE;

		// Find anything excluding the false tokens.
		$hasNonFalse = $this->phpcsFile->findNext( $falseTokens, $start, $end + 1, true );
		// If no non-false tokens are found, we are good.
		if ( false === $hasNonFalse ) {
			return true;
		}

		// If there is a number, it can be a falsy-value.
		$numberToken = $this->phpcsFile->findNext( array( T_DNUMBER, T_LNUMBER ), $start, $end + 1 );
		if ( $numberToken ) {
			$value = trim( $this->phpcsFile->getTokensAsString( $start, ( $end - $start ) + 1 ) );

			// Only parse values that have a format we can safely evaulate.
			if ( preg_match( '#^[\s(?:\d+(?:\.\d+|x\d+)?)*/-]+$#', $value ) > 0 ) {
				// Evaluate the argument to figure out the outcome is false or not.
				return false === eval( "return (bool) $value;" ); // @codingStandardsIgnoreLine - No harm here.
			}
		}

		return false;
	}
}
