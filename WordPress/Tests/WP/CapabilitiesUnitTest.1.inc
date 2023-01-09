<?php
/*
 * These shouldn't be flagged.
 */
if ( author_can( $post, 'read' ) ) { } // OK.
map_meta_cap( 'edit_posts', $user->ID ); // OK.

/*
 * Low severity warnings, usually these need to be manually checked.
 */
add_posts_page( 'page_title', 'menu_title', 'admin' . 'istrator', 'menu_slug', 'function' ); // Low severity warning.
if ( author_can( $post, $capability ) ) { } // Low severity warning.
add_submenu_page(
	'parent_slug',
	'page_title',
	'menu_title',
	$variable, // Low severity warning.
	'menu_slug',
	'function'
);
add_menu_page( $pagetitle, $menu_title, $subscriber, 'handle', 'function', 'icon_url' ); // Low severity warning.
add_plugins_page( 'page_title', 'menu_title', $cap, 'menu_slug', 'function' ); // Low severity warning.
add_options_page( $pagetitle, $menu_title, CONSTANT, 'menu_slug', 'function' ); // Low severity warning.
add_posts_page( 'page_title', 'menu_title', self /* comment */   ::   CAPABILITY, 'menu_slug', 'function' ); // Low severity warning.
add_posts_page( 'page_title', 'menu_title', 'admin' /* comment */   . 'istrator', 'menu_slug', 'function' ); // Low severity warning.
add_menu_page(
	$p,
	$t, // Comment.
	$capability, // Low severity warning.
);
add_menu_page( $p, $t, 'admin' . 'istrator' ); // Low severity warning.
add_menu_page($p, $t, $caps['level'] ); // Low severity warning.

// Parse error, but just making sure we account for all possibilities.
add_menu_page($p, $t, 'level_' 'level' ); // Low severity warning.

/*
 * Empty capability parameter.
 */
if ( author_can( $post, '' ) ) { } // Error.

/*
 * Deprecated capabilities.
 */
// phpcs:set WordPress.WP.Capabilities minimum_wp_version 2.9
if ( author_can( $post, 'level_3' ) ) { } // Warning.

// phpcs:set WordPress.WP.Capabilities minimum_wp_version
if ( author_can( $post, 'level_5' ) ) { } // Error.
add_options_page( 'page_title', 'menu_title', 'level_10', 'menu_slug', 'function' ); // Error.

/*
 * Unknown capabilities, could be that they need to be set in the property, but weren't.
 */
if ( author_can( $post, 'custom_cap' ) ) { } // Warning.
if ( current_user_can( 'foo_bar' ) ) { } // Warning.
if ( current_user_can_for_blog( '3', 'custom_cap' ) ) { } // Warning.
add_users_page( 'page_title', 'menu_title', 'foo_bar', 'menu_slug', 'function' ); // Warning.
add_management_page( 'page_title', 'menu_title', 'foo_bar', 'menu_slug', 'function' ); // Warning.
add_menu_page( $pagetitle, 'menu_title', 'foo_bar', 'handle', 'function', 'icon_url' ); // Warning.

/*
 * Roles found instead of capabilities.
 */
add_posts_page( 'page_title', 'menu_title', 'administrator', 'menu_slug', 'function' ); // Error.
add_media_page( 'page_title', 'menu_title', 'editor', 'menu_slug', 'function' ); // Error.
add_pages_page( 'page_title', 'menu_title', 'author', 'menu_slug', 'function' ); // Error.
add_comments_page( 'page_title', 'menu_title', 'contributor', 'menu_slug', 'function' ); // Error.
add_theme_page( 'page_title', $menu_title, 'subscriber', 'menu_slug', 'function' ); // Error.
add_plugins_page( 'page_title', 'menu_title', 'super_admin', 'menu_slug', 'function' ); // Error.
add_users_page( 'page_title', 'menu_title', 'administrator', 'menu_slug', 'function' ); // Error.
add_management_page( 'page_title', 'menu_title', 'editor', 'menu_slug', 'function' ); // Error.
if ( current_user_can( 'super_admin' ) ) { } // Error.
if( current_user_can_for_blog( '1', 'editor' ) ) { }  // Error.
add_dashboard_page(
	'page_title',
	'menu_title',
	'super_admin' /* Comment */, // Error.
	'menu_slug',
	'function'
);
add_utility_page(
	'page_title'
	,'menu_title'
	,'super_admin' // Error.
	,'menu_slug'
	,'function'
	,'icon_url'
);

// PHP 8.0 named parameters support.
add_menu_page( capability: 'foobar', page_title: $p, menu_title: $m ); // Warning.

/*
 * Testing handling of the custom capabilities properties.
 */
// phpcs:set WordPress.WP.Capabilities custom_capabilities[] custom_cap,foo_bar
if ( current_user_can( 'foo_bar' ) ) { } // OK.
if ( author_can( $post, 'custom_cap' ) ) { } // OK.
if ( author_can( $post, 'custom_capability' ) ) { } // Warning.

// phpcs:set WordPress.WP.Capabilities custom_capabilities[]

// Making sure that the warnings and errors are showing up in the case where we unset the custom capabilities.
if ( author_can( $post, 'custom_cap' ) ) { } // Warning.
map_meta_cap( 'editor', $user->ID ); // Error.

// Another parse error, but the sniff should still handle this correctly (by bowing out).
add_menu_page( $p, $t, /* deliberately empty */, $slug, );

add_menu_page( [] ); // Should bow out because the parameter is not found.

$obj->current_user_can( 'foo_bar' ); // Ok. We're not checking for method calls.
My\NamespaceS\add_posts_page( 'page_title', 'menu_title', 'administrator', 'menu_slug', 'function' ); // Ok. We're not checking namespaced functions.

// Parse error, should be handled correctly by bowing out.
add_posts_page( 'page_title',
