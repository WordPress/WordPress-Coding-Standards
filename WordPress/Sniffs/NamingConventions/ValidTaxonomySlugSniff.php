<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\NamingConventions;

use WordPressCS\WordPress\AbstractValidSlugSniff;
use WordPressCS\WordPress\Helpers\WPReservedNamesHelper;

/**
 * Validates taxonomy names.
 *
 * Checks taxonomy slugs for the presence of invalid characters, excessive
 * length, and reserved names.
 *
 * @since 3.2.0
 *
 * @link https://developer.wordpress.org/reference/functions/register_taxonomy/
 */
final class ValidTaxonomySlugSniff extends AbstractValidSlugSniff {

	/**
	 * Retrieve function and parameter(s) pairs this sniff is looking for.
	 *
	 * @since 3.2.0
	 *
	 * @return array<string, string|array<string>> Function parameter(s) pairs.
	 */
	protected function get_target_functions() {
		return array(
			'register_taxonomy' => array( 'taxonomy' ),
		);
	}

	/**
	 * Retrieve the slug type.
	 *
	 * @since 3.2.0
	 *
	 * @return string The slug type.
	 */
	protected function get_slug_type() {
		return 'taxonomy';
	}

	/**
	 * Retrieve the plural slug type.
	 *
	 * @since 3.2.0
	 *
	 * @return string The plural slug type.
	 */
	protected function get_slug_type_plural() {
		return 'taxonomies';
	}

	/**
	 * Retrieve regex to validate the characters that can be used as the
	 * taxonomy slug.
	 *
	 * @since 3.2.0
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_taxonomy/
	 *
	 * @return string
	 */
	protected function get_valid_characters() {
		return '/^[a-z0-9_-]+$/';
	}

	/**
	 * Retrieve max length of a taxonomy name.
	 *
	 * The length is limited by the SQL field.
	 *
	 * @since 3.2.0
	 *
	 * @return int
	 */
	protected function get_max_length() {
		return 32;
	}

	/**
	 * Retrieve the reserved taxonomy names which can not be used
	 * by themes and plugins.
	 *
	 * @since 3.2.0
	 *
	 * @return array<string, true>
	 */
	protected function get_reserved_names() {
		return WPReservedNamesHelper::get_terms();
	}
}
