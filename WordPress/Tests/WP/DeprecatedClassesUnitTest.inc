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
/* ============ WP 3.7 ============ */
$anon = new class extends WP_HTTP_Fsockopen {};
/* ============ WP 4.9 ============ */
class Prefix_Menu_section extends WP_Customize_New_Menu_Section {}
WP_Customize_New_Menu_Control::foo();
/* ============ WP 5.3 ============ */
$json = new Services_JSON;
$json = new Services_JSON_Error;
class Prefix_Menu_section extends WP_Privacy_Data_Export_Requests_Table {}
WP_Privacy_Data_Removal_Requests_Table::foo();

/*
 * Warning
 */
/* ============ WP 6.4 ============ */
WP_Http_Curl::do_something();
$streams = new WP_Http_Streams();
