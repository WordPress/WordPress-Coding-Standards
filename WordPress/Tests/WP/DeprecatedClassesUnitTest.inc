<?php

// DEPRECATED WORDPRESS CLASSES.

/*
 * Error.
 */
/* ============ WP 3.1 ============ */
$a = new WP_User_Search;
$a = new \WP_User_Search();
echo WP_User_Search::$users_per_page;
echo \WP_User_Search::prepare_query();
class My_User_Search extends WP_User_Search {}
class Our_User_Search implements WP_User_Search {}
$a = (new WP_User_Search())->query();
/* ============ WP 4.9 ============ */
class Prefix_Menu_section extends Customize_New_Menu_Section {}
WP_Customize_New_Menu_Control::foo();

/*
 * Warning.
 */
/* ============ WP 5.3 ============ */
$json = new Services_JSON;
