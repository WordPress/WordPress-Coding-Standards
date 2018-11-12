<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\VIP;

use WordPress\AbstractFunctionRestrictionsSniff;

/**
 * Restricts usage of some functions in VIP context.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.3.0
 * @since   0.10.0 The checks for `extract()` and the POSIX functions have been replaced by
 *                 the stand-alone sniffs WordPress_Sniffs_Functions_DontExtractSniff and
 *                 WordPress_Sniffs_PHP_POSIXFunctionsSniff respectively.
 * @since   0.11.0 The checks for `create_function()`, `serialize()`/`unserialize()` and
 *                 `urlencode` have been moved to the stand-alone sniff
 *                 WordPress_Sniffs_PHP_DiscouragedPHPFunctionsSniff.
 *                 The checks for PHP developer functions, `error_reporting` and `phpinfo`have been
 *                 moved to the stand-alone sniff WordPress_Sniffs_PHP_DevelopmentFunctionsSniff.
 *                 The check for `parse_url()` and `curl_*` have been moved to the stand-alone sniff
 *                 WordPress_Sniffs_WP_AlternativeFunctionsSniff.
 *                 The check for `eval()` now defers to the upstream Squiz.PHP.Eval sniff.
 * @since   0.13.0 Class name changed: this class is now namespaced.
 *
 * @deprecated 1.0.0  This sniff has been deprecated.
 *                    This file remains for now to prevent BC breaks.
 */
class RestrictedFunctionsSniff extends AbstractFunctionRestrictionsSniff {

	/**
	 * Keep track of whether the warnings have been thrown to prevent
	 * the messages being thrown for every token triggering the sniff.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $thrown = array(
		'DeprecatedSniff'                 => false,
		'FoundPropertyForDeprecatedSniff' => false,
	);

	/**
	 * Groups of functions to restrict.
	 *
	 * Example: groups => array(
	 *  'lambda' => array(
	 *      'type'      => 'error' | 'warning',
	 *      'message'   => 'Use anonymous functions instead please!',
	 *      'functions' => array( 'file_get_contents', 'create_function' ),
	 *  )
	 * )
	 *
	 * @return array
	 */
	public function getGroups() {
		return array(
			// @link WordPress.com: https://vip.wordpress.com/documentation/vip/code-review-what-we-look-for/#switch_to_blog
			// @link VIP Go: https://vip.wordpress.com/documentation/vip-go/code-review-blockers-warnings-notices/#switch_to_blog
			'switch_to_blog' => array(
				'type'      => 'error',
				'message'   => '%s() is not something you should ever need to do in a VIP theme context. Instead use an API (XML-RPC, REST) to interact with other sites if needed.',
				'functions' => array( 'switch_to_blog' ),
			),

			'file_get_contents' => array(
				'type'      => 'warning',
				'message'   => '%s() is highly discouraged, please use wpcom_vip_file_get_contents() instead.',
				'functions' => array(
					'file_get_contents',
					'vip_wp_file_get_contents',
				),
			),

			'wpcom_vip_get_term_link' => array(
				'type'      => 'error',
				'message'   => '%s() is deprecated, please use get_term_link(), get_tag_link(), or get_category_link() instead.',
				'functions' => array(
					'wpcom_vip_get_term_link',
				),
			),

			'get_page_by_path' => array(
				'type'      => 'error',
				'message'   => '%s() is prohibited, please use wpcom_vip_get_page_by_path() instead.',
				'functions' => array(
					'get_page_by_path',
				),
			),

			'get_page_by_title' => array(
				'type'      => 'error',
				'message'   => '%s() is prohibited, please use wpcom_vip_get_page_by_title() instead.',
				'functions' => array(
					'get_page_by_title',
				),
			),

			'wpcom_vip_get_term_by' => array(
				'type'      => 'error',
				'message'   => '%s() is deprecated, please use get_term_by() or get_cat_ID() instead.',
				'functions' => array(
					'wpcom_vip_get_term_by',
				),
			),

			'wpcom_vip_get_category_by_slug' => array(
				'type'      => 'error',
				'message'   => '%s() is deprecated, please use get_category_by_slug() instead.',
				'functions' => array(
					'wpcom_vip_get_category_by_slug',
				),
			),

			'url_to_postid' => array(
				'type'      => 'error',
				'message'   => '%s() is prohibited, please use wpcom_vip_url_to_postid() instead.',
				'functions' => array(
					'url_to_postid',
					'url_to_post_id',
				),
			),

			'attachment_url_to_postid' => array(
				'type'      => 'error',
				'message'   => '%s() is prohibited, please use wpcom_vip_attachment_url_to_postid() instead.',
				'functions' => array(
					'attachment_url_to_postid',
				),
			),

			// @link WordPress.com: https://lobby.vip.wordpress.com/wordpress-com-documentation/code-review-what-we-look-for/#remote-calls
			// @link VIP Go: https://vip.wordpress.com/documentation/vip-go/code-review-blockers-warnings-notices/#remote-calls
			'wp_remote_get' => array(
				'type'      => 'warning',
				'message'   => '%s() is highly discouraged, please use vip_safe_wp_remote_get() instead.',
				'functions' => array(
					'wp_remote_get',
				),
			),

			// @link WordPress.com: https://lobby.vip.wordpress.com/wordpress-com-documentation/code-review-what-we-look-for/#custom-roles
			// @link VIP Go: https://vip.wordpress.com/documentation/vip-go/code-review-blockers-warnings-notices/#custom-roles
			'custom_role' => array(
				'type'      => 'error',
				'message'   => 'Use wpcom_vip_add_role() instead of %s()',
				'functions' => array(
					'add_role',
				),
			),

			// @link WordPress.com: https://lobby.vip.wordpress.com/wordpress-com-documentation/code-review-what-we-look-for/#custom-roles
			// @link VIP Go: https://vip.wordpress.com/documentation/vip-go/code-review-blockers-warnings-notices/#cache-constraints
			'cookies' => array(
				'type'      => 'warning',
				'message'   => 'Due to using Batcache, server side based client related logic will not work, use JS instead.',
				'functions' => array(
					'setcookie',
				),
			),

			// @link WordPress.com: https://lobby.vip.wordpress.com/wordpress-com-documentation/code-review-what-we-look-for/#wp_users-and-user_meta
			'user_meta' => array(
				'type'      => 'error',
				'message'   => '%s() usage is highly discouraged, check VIP documentation on "Working with wp_users"',
				'functions' => array(
					'get_user_meta',
					'update_user_meta',
					'delete_user_meta',
					'add_user_meta',
				),
			),

			// @todo Introduce a sniff specific to get_posts() that checks for suppress_filters=>false being supplied.
			'get_posts' => array(
				'type'      => 'warning',
				'message'   => '%s() is discouraged in favor of creating a new WP_Query() so that Advanced Post Cache will cache the query, unless you explicitly supply suppress_filters => false.',
				'functions' => array(
					'get_posts',
					'wp_get_recent_posts',
					'get_children',
				),
			),

			'term_exists' => array(
				'type'      => 'error',
				'message'   => '%s() is highly discouraged due to not being cached; please use wpcom_vip_term_exists() instead.',
				'functions' => array(
					'term_exists',
				),
			),

			'count_user_posts' => array(
				'type'      => 'error',
				'message'   => '%s() is highly discouraged due to not being cached; please use wpcom_vip_count_user_posts() instead.',
				'functions' => array(
					'count_user_posts',
				),
			),

			'wp_old_slug_redirect' => array(
				'type'      => 'error',
				'message'   => '%s() is highly discouraged due to not being cached; please use wpcom_vip_old_slug_redirect() instead.',
				'functions' => array(
					'wp_old_slug_redirect',
				),
			),

			'get_adjacent_post' => array(
				'type'      => 'error',
				'message'   => '%s() is highly discouraged due to not being cached; please use wpcom_vip_get_adjacent_post() instead.',
				'functions' => array(
					'get_adjacent_post',
					'get_previous_post',
					'get_previous_post_link',
					'get_next_post',
					'get_next_post_link',
				),
			),

			'get_intermediate_image_sizes' => array(
				'type'      => 'error',
				'message'   => 'Intermediate images do not exist on the VIP platform, and thus get_intermediate_image_sizes() returns an empty array() on the platform. This behavior is intentional to prevent WordPress from generating multiple thumbnails when images are uploaded.',
				'functions' => array(
					'get_intermediate_image_sizes',
				),
			),

			// @link WordPress.com: https://lobby.vip.wordpress.com/wordpress-com-documentation/code-review-what-we-look-for/#mobile-detection
			// @link VIP Go: https://vip.wordpress.com/documentation/vip-go/code-review-blockers-warnings-notices/#mobile-detection
			'wp_is_mobile' => array(
				'type'      => 'error',
				'message'   => '%s() found. When targeting mobile visitors, jetpack_is_mobile() should be used instead of wp_is_mobile. It is more robust and works better with full page caching.',
				'functions' => array(
					'wp_is_mobile',
				),
			),
		);
	}

	/**
	 * Process the token and handle the deprecation notices.
	 *
	 * @since 1.0.0 Added to allow for throwing the deprecation notices.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return void|int
	 */
	public function process_token( $stackPtr ) {
		if ( false === $this->thrown['DeprecatedSniff'] ) {
			$this->thrown['DeprecatedSniff'] = $this->phpcsFile->addWarning(
				'The "WordPress.VIP.RestrictedFunctions" sniff has been deprecated. Please update your custom ruleset.',
				0,
				'DeprecatedSniff'
			);
		}

		if ( ! empty( $this->exclude )
			&& false === $this->thrown['FoundPropertyForDeprecatedSniff']
		) {
			$this->thrown['FoundPropertyForDeprecatedSniff'] = $this->phpcsFile->addWarning(
				'The "WordPress.VIP.RestrictedFunctions" sniff has been deprecated. Please update your custom ruleset.',
				0,
				'FoundPropertyForDeprecatedSniff'
			);
		}

		return parent::process_token( $stackPtr );
	}

}
