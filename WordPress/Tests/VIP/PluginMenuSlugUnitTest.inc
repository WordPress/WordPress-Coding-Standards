<?php

add_menu_page( $page_title, $menu_title, $capability, __FILE__, $function, $icon_url, $position ); // Bad.

add_dashboard_page( $page_title, $menu_title, $capability, __FILE__, $function); // Bad.

add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, 'awesome-submenu-page', $function ); // Ok.

add_submenu_page( __FILE__ . 'parent', $page_title, $menu_title, $capability, __FILE__, $function ); // Bad x 2.

// These are all ok: not calling the WP core function.
$my_class->add_dashboard_page( $page_title, $menu_title, $capability, __FILE__, $function); // Ok.
Some_Class::add_dashboard_page( $page_title, $menu_title, $capability, __FILE__, $function); // Ok.
\My_Namespace\add_dashboard_page( $page_title, $menu_title, $capability, __FILE__, $function); // Ok.
