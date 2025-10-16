<?php

add_menu_page( $page_title, $menu_title, $capability, __FILE__, $function, $icon_url, $position ); // Bad.

\ADD_DASHBOARD_PAGE( $page_title, $menu_title, $capability, __file__, $function); // Bad.

add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, 'awesome-submenu-page', $function ); // Ok.

Add_Submenu_Page( __FILE__ . 'parent', $page_title, $menu_title, $capability, __FILE__, $function ); // Bad x 2.

// These are all ok: not calling the WP core function.
$my_class->add_dashboard_page( $page_title, $menu_title, $capability, __FILE__, $function); // Ok.
Some_Class::add_dashboard_page( $page_title, $menu_title, $capability, __FILE__, $function); // Ok.
\My_Namespace\add_dashboard_page( $page_title, $menu_title, $capability, __FILE__, $function); // Ok.

// Safeguard support for PHP 8.0+ named parameters.
add_submenu_page(
	page_title: $page_title,
	menu_title: $menu_title,
	parent_slug: __FILE__, // Bad.
	capability: $capability,
);

/*
 * Safeguard correct handling of all types of namespaced function calls.
 */
\add_menu_page( $page_title, $menu_title, $capability, __FILE__, $function, $icon_url, $position ); // Bad.
MyNamespace\add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, __FILE__, $function ); // Ok.
\MyNamespace\add_dashboard_page( $page_title, $menu_title, $capability, __FILE__, $function ); // Ok.
namespace\add_menu_page( $page_title, $menu_title, $capability, __FILE__, $function, $icon_url, $position ); // The sniff should start flagging this once it can resolve relative namespaces.
namespace\Sub\add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, __FILE__, $function ); // Ok.
