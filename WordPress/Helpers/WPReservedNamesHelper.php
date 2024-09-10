<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Helpers;

/**
 * Helper utilities for recognizing WP reserved names.
 */
final class WPReservedNamesHelper {
	/**
	 * Array of reserved post type names which can not be used by themes and plugins.
	 *
	 * Source: {@link https://developer.wordpress.org/reference/functions/register_post_type/#reserved-post-types}
	 *
	 * {@internal To be updated after every major release. Last updated for WordPress 6.5-RC3.}
	 *
	 * @var array<string, true> Key is reserved post type name, value irrelevant.
	 */
	private static $post_types = array(
		'action'              => true, // Not a WP post type, but prevents other problems.
		'attachment'          => true,
		'author'              => true, // Not a WP post type, but prevents other problems.
		'custom_css'          => true,
		'customize_changeset' => true,
		'nav_menu_item'       => true,
		'oembed_cache'        => true,
		'order'               => true, // Not a WP post type, but prevents other problems.
		'page'                => true,
		'post'                => true,
		'revision'            => true,
		'theme'               => true, // Not a WP post type, but prevents other problems.
		'user_request'        => true,
		'wp_block'            => true,
		'wp_font_face'        => true,
		'wp_font_family'      => true,
		'wp_global_styles'    => true,
		'wp_navigation'       => true,
		'wp_template'         => true,
		'wp_template_part'    => true,
	);

	/**
	 * Array of reserved taxonomy names which can not be used by themes and plugins.
	 *
	 * Source: {@link https://developer.wordpress.org/reference/functions/register_taxonomy/#reserved-terms}
	 *
	 * {@internal To be updated after every major release. Last updated for WordPress 6.6.1.}
	 *
	 * @var array<string, true> Key is reserved taxonomy name, value irrelevant.
	 */
	private static $terms = array(
		'attachment'                  => true,
		'attachment_id'               => true,
		'author'                      => true,
		'author_name'                 => true,
		'calendar'                    => true,
		'cat'                         => true,
		'category'                    => true,
		'category__and'               => true,
		'category__in'                => true,
		'category__not_in'            => true,
		'category_name'               => true,
		'comments_per_page'           => true,
		'comments_popup'              => true,
		'custom'                      => true,
		'customize_messenger_channel' => true,
		'customized'                  => true,
		'cpage'                       => true,
		'day'                         => true,
		'debug'                       => true,
		'embed'                       => true,
		'error'                       => true,
		'exact'                       => true,
		'feed'                        => true,
		'fields'                      => true,
		'hour'                        => true,
		'link_category'               => true,
		'm'                           => true,
		'minute'                      => true,
		'monthnum'                    => true,
		'more'                        => true,
		'name'                        => true,
		'nav_menu'                    => true,
		'nonce'                       => true,
		'nopaging'                    => true,
		'offset'                      => true,
		'order'                       => true,
		'orderby'                     => true,
		'p'                           => true,
		'page'                        => true,
		'page_id'                     => true,
		'paged'                       => true,
		'pagename'                    => true,
		'pb'                          => true,
		'perm'                        => true,
		'post'                        => true,
		'post__in'                    => true,
		'post__not_in'                => true,
		'post_format'                 => true,
		'post_mime_type'              => true,
		'post_status'                 => true,
		'post_tag'                    => true,
		'post_type'                   => true,
		'posts'                       => true,
		'posts_per_archive_page'      => true,
		'posts_per_page'              => true,
		'preview'                     => true,
		'robots'                      => true,
		's'                           => true,
		'search'                      => true,
		'second'                      => true,
		'sentence'                    => true,
		'showposts'                   => true,
		'static'                      => true,
		'status'                      => true,
		'subpost'                     => true,
		'subpost_id'                  => true,
		'tag'                         => true,
		'tag__and'                    => true,
		'tag__in'                     => true,
		'tag__not_in'                 => true,
		'tag_id'                      => true,
		'tag_slug__and'               => true,
		'tag_slug__in'                => true,
		'taxonomy'                    => true,
		'tb'                          => true,
		'term'                        => true,
		'terms'                       => true,
		'theme'                       => true,
		'title'                       => true,
		'type'                        => true,
		'types'                       => true,
		'w'                           => true,
		'withcomments'                => true,
		'withoutcomments'             => true,
		'year'                        => true,
	);

	/**
	 * Verify if a given name is a reserved post type name.
	 *
	 * @param string $name The name to be checked.
	 *
	 * @return bool
	 */
	public static function is_reserved_post_type( $name ) {
		return isset( self::$post_types[ $name ] );
	}

	/**
	 * Verify if a given name is a reserved taxonomy name.
	 *
	 * @param string $name The name to be checked.
	 *
	 * @return bool
	 */
	public static function is_reserved_term( $name ) {
		return isset( self::$terms[ $name ] )
			|| isset( self::$post_types[ $name ] );
	}

	/**
	 * Retrieve an array with the reserved post type names.
	 *
	 * @return array<string, true> Array with the post type names as keys. The value is irrelevant.
	 */
	public static function get_post_types() {
		return self::$post_types;
	}

	/**
	 * Retrieve an array with the reserved taxonomy names.
	 *
	 * @return array<string, true> Array with the taxonomy names as keys. The value is irrelevant.
	 */
	public static function get_terms() {
		return array_merge( self::$post_types, self::$terms );
	}
}
