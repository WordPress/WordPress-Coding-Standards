<?php

// Menu Pages.
add_menu_page();
add_object_page();
add_utility_page();

// SubMenu Pages.
add_submenu_page();

// WordPress Administration Menus.
add_dashboard_page();
add_posts_page();
add_media_page();
add_pages_page();
add_comments_page();
add_plugins_page();
add_users_page();
add_management_page();
add_options_page();

// Add Theme Page is allowed.
add_theme_page(); // Ok.

// Method names within a Theme class should be fine.
Theme_Object::add_menu_page(); // Ok.
$this->add_plugins_page(); // Ok.
$theme_object->add_options_page(); // Ok.
