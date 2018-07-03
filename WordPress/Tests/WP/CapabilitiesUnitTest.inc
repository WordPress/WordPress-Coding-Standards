<?php
add_posts_page( 'page_title', 'menu_title', 'administrator', 'menu_slug', 'function' ); // Error.
add_media_page( 'page_title', 'menu_title', 'editor', 'menu_slug', 'function' ); // Error.
add_pages_page( 'page_title', 'menu_title', 'author', 'menu_slug', 'function' ); // Error.
add_comments_page( 'page_title', 'menu_title', 'contributor', 'menu_slug', 'function' ); // Error.
add_theme_page( 'page_title', $menu_title, 'subscriber', 'menu_slug', 'function' ); // Error.
add_plugins_page( 'page_title', 'menu_title', 'super_admin', 'menu_slug', 'function' ); // Error.
add_users_page( 'page_title', 'menu_title', 'administrator', 'menu_slug', 'function' ); // Error.
add_management_page( 'page_title', 'menu_title', 'editor', 'menu_slug', 'function' ); // Error.
add_options_page( 'page_title', 'menu_title', 'level_10', 'menu_slug', 'function' ); // Error.
if ( current_user_can( 'super_admin' ) ) { } // Error.
if( current_user_can_for_blog( '1', 'editor' ) ) { }  // Error.
add_dashboard_page(
	'page_title',
	'menu_title',
	'super_admin' /* Error */, // Error.
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

// @codingStandardsChangeSetting WordPress.WP.Capabilities check_only_known_caps false
add_menu_page( $pagetitle, $menu_title, $subscriber, 'handle', 'function', 'icon_url' ); // Warning.
add_plugins_page( 'page_title', 'menu_title', $cap, 'menu_slug', 'function' ); // Warning.
add_users_page( 'page_title', 'menu_title', 'foo_bar', 'menu_slug', 'function' ); // Warning.
add_management_page( 'page_title', 'menu_title', 'foo_bar', 'menu_slug', 'function' ); // Warning.
add_options_page( $pagetitle, $menu_title, CONSTANT, 'menu_slug', 'function' ); // Warning.
add_menu_page( $pagetitle, 'menu_title', 'foo_bar', 'handle', 'function', 'icon_url' ); // Warning.
if ( author_can( $post, $capability ) ) { } // Warning.
add_submenu_page(
	'parent_slug',
	'page_title',
	'menu_title',
	$variable, // Warning.
	'menu_slug',
	'function'
);
if ( author_can( $post, 'custom_cap' ) ) { } // Warning.

// @codingStandardsChangeSetting WordPress.WP.Capabilities custom_capabilities custom_cap,foo_bar
if ( current_user_can( 'foo_bar' ) ) { } // OK.
if ( author_can( $post, 'custom_cap' ) ) { } // OK.

// @codingStandardsChangeSetting WordPress.WP.Capabilities custom_capabilities false

if ( current_user_can( 'foo_bar' ) ) { } // Warning.
if ( current_user_can_for_blog( '3', 'custom_cap' ) ) { } // Warning.

// @codingStandardsChangeSetting WordPress.WP.Capabilities check_only_known_caps true

if ( author_can( $post, 'read' ) ) { } // OK.
if ( author_can( $post, '' ) ) { } // OK.
if ( author_can( $post, 'custom_cap' ) ) { } // OK.
