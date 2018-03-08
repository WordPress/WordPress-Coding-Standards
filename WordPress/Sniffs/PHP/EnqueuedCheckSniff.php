<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\PHP;

use WordPress\AbstractFunctionParameterSniff;

/**
 * Based off the StrictInArraySniff.php sniff, this checks the enqueued 4th parameter to make sure a version is available.
 * The Enqueued functions are:
 * wp_register_script()
 * wp_enqueue_script()
 * wp_register_style()
 * wp_enqueue_style()
 * If a source ($src) value is passed, then version ($ver) needs to have a value.
 * Additionally, If a source ($src) value is passed a check for in footer ($in_footer)
 * to alert the user if the value isnt True
 *
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1146
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   1.0.0
 */
class EnqueuedCheckSniff extends AbstractFunctionParameterSniff {

	/**
	 * The group name for this group of functions.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $group_name = 'Enqueued';

	/**
	 * List of enqueued functions that need to be check to make sure
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_register_script/
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/
	 * @link https://developer.wordpress.org/reference/functions/wp_register_style/
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 *
	 * @since 1.0.0
	 *
	 * @var array <string function_name> => <bool always needed ?>
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
		 * Version Check
		 *
		 * Check to make sure a Version ($ver) is set
		 * Otherwise it will Show an error to add a src (url) to the enqueued function
		 */
		if ( false === isset( $parameters[4] ) || ! $parameters[4]['raw'] ) {
			$this->phpcsFile->addError( 'No Version found for %s; Please supply a value for the fourth argument', $stackPtr, 'MissingVersion', array( $matched_content ) );
			return;
		}

		/*
		 * In footer Check
		 *
		 * Check to make sure that $in_footer is set to true
		 * Otherwise it will warn the user to make sure if its correct
		 */
		if ( isset( $parameters[5] ) ) {
			/*
			 * Only wp_register_script and wp_enqueue_script need this check
			 * As it is not available to wp_register_style and wp_enqueue_style
			 */
			switch ( $matched_content ) {
				case 'wp_register_script':
				case 'wp_enqueue_script':
					if ( 'true' !== $parameters[5]['raw'] ) {
						$this->phpcsFile->addWarning( 'If the Footer is not set to True for %s; Double check if correct or set to True', $stackPtr, 'MissingInFooter', array( $matched_content ) );
						return;
					}
			}
		}
	}
} // End class.
