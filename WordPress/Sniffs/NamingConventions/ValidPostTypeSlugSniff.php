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
 * Validates post type names.
 *
 * Checks post type slugs for the presence of invalid characters, excessive
 * length, and reserved names.
 *
 * @link https://developer.wordpress.org/reference/functions/register_post_type/
 *
 * @since 2.2.0
 */
final class ValidPostTypeSlugSniff extends AbstractValidSlugSniff {

	/**
	 * Retrieve function and parameter(s) pairs this sniff is looking for.
	 *
	 * @since 3.2.0
	 *
	 * @return array<string, string|array<string>> Function parameter(s) pairs.
	 */
	protected function get_target_functions() {
		return array(
			'register_post_type' => array( 'post_type' ),
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
		return 'post type';
	}

	/**
	 * Retrieve the plural slug type.
	 *
	 * @since 3.2.0
	 *
	 * @return string The plural slug type.
	 */
	protected function get_slug_type_plural() {
		return 'post types';
	}

	/**
	 * Retrieve regex to validate the characters that can be used as the
	 * post type slug.
	 *
	 * @since 3.2.0
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_post_type/
	 *
	 * @return string
	 */
	protected function get_valid_characters() {
		return '/^[a-z0-9_-]+$/';
	}

	/**
	 * Retrieve max length of a post type name.
	 *
	 * @since 3.2.0
	 *
	 * @return int
	 */
	protected function get_max_length() {
		return 20;
	}

	/**
	 * Retrieve the reserved post type names which can not be used
	 * by themes and plugins.
	 *
	 * @since 3.2.0
	 *
	 * @return array<string, true>
	 */
	protected function get_reserved_names() {
		return WPReservedNamesHelper::get_post_types();
	}
}
