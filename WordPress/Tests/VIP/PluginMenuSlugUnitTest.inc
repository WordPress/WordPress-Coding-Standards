<?php

add_menu_page( $page_title, $menu_title, $capability, __FILE__, $function, $icon_url, $position ); // bad

add_dashboard_page( $page_title, $menu_title, $capability, __FILE__, $function); // bad

add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, 'awesome-submenu-page', $function ); // ok
