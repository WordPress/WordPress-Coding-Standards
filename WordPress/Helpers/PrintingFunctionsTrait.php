<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Helpers;

use WordPressCS\WordPress\Helpers\RulesetPropertyHelper;

/**
 * Helper functions and function lists for checking whether a function prints output.
 *
 * Any sniff class which incorporates this trait will automatically support the
 * following `public` property which can be changed from within a custom ruleset:
 * - `customPrintingFunctions`.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   3.0.0 The properties in this trait were previously contained partially in the
 *                `WordPressCS\WordPress\Sniff` class and partially in the `EscapeOutputSniff`
 *                class and have been moved here.
 */
trait PrintingFunctionsTrait {

	/**
	 * Custom list of functions which print output incorporating the passed values.
	 *
	 * @since 0.4.0
	 * @since 3.0.0 Moved from the EscapeOutput Sniff class to this class.
	 *
	 * @var string|string[]
	 */
	public $customPrintingFunctions = array();

	/**
	 * Functions which print output incorporating the values passed to them.
	 *
	 * @since 0.5.0
	 * @since 0.11.0 Changed from public static to protected non-static.
	 * @since 3.0.0 - Moved from the Sniff class to this class.
	 *              - Visibility changed from protected to private.
	 *
	 * @var array
	 */
	private $printingFunctions = array(
		'_deprecated_argument'    => true,
		'_deprecated_constructor' => true,
		'_deprecated_file'        => true,
		'_deprecated_function'    => true,
		'_deprecated_hook'        => true,
		'_doing_it_wrong'         => true,
		'_e'                      => true,
		'_ex'                     => true,
		'printf'                  => true,
		'trigger_error'           => true,
		'user_error'              => true,
		'vprintf'                 => true,
		'wp_die'                  => true,
		'wp_dropdown_pages'       => true,
	);

	/**
	 * Cache of previously added custom functions.
	 *
	 * Prevents having to do the same merges over and over again.
	 *
	 * @since 0.4.0
	 * @since 0.11.0 - Changed from public static to protected non-static.
	 *               - Changed the format from simple bool to array.
	 * @since 3.0.0  - Moved from the EscapeOutput Sniff class to this class.
	 *               - Visibility changed from protected to private.
	 *
	 * @var array
	 */
	private $addedCustomPrintingFunctions = array();

	/**
	 * Combined list of WP/PHP native and custom printing functions.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	private $allPrintingFunctions = array();

	/**
	 * Check if a particular function is regarded as a printing function.
	 *
	 * @since 3.0.0
	 *
	 * @param string $functionName The name of the function to check.
	 *
	 * @return bool
	 */
	public function is_printing_function( $functionName ) {
		if ( array() === $this->allPrintingFunctions
			|| $this->customPrintingFunctions !== $this->addedCustomPrintingFunctions
		) {
			$this->allPrintingFunctions = RulesetPropertyHelper::merge_custom_array(
				$this->customPrintingFunctions,
				$this->printingFunctions
			);

			$this->addedCustomPrintingFunctions = $this->customPrintingFunctions;
		}

		return isset( $this->allPrintingFunctions[ $functionName ] );
	}
}
