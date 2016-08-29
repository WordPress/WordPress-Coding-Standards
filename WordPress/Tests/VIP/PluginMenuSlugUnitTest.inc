<?php

add_menu_page( $page_title, $menu_title, $capability, __FILE__, $function, $icon_url, $position ); // Bad.

add_dashboard_page( $page_title, $menu_title, $capability, __FILE__, $function); // Bad.

add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, 'awesome-submenu-page', $function ); // Ok.
