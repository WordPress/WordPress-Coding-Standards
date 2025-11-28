<?php

/*
 * Safeguard correct handling of all types of namespaced class name references.
 */
$streams = new \WP_User_Search();
$streams = new MyNamespace\WP_HTTP_Fsockopen();
$streams = new \MyNamespace\WP_Customize_New_Menu_Section();
$streams = new namespace\Sub\WP_Customize_New_Menu_Control();
$streams = new namespace\WP_Privacy_Data_Export_Requests_Table();
class MyClass1 extends \WP_Privacy_Data_Removal_Requests_Table {}
class MyClass2 extends MyNamespace\Services_JSON {}
class MyClass3 extends \MyNamespace\Services_JSON_Error {}
class MyClass4 extends namespace\Sub\WP_Http_Curl {}
class MyClass5 extends namespace\WP_Http_Streams {}
\wp_user_search::do_something();
MyNamespace\WP_HTTP_Fsockopen::do_something();
\MyNamespace\WP_Customize_New_Menu_Section::do_something();
namespace\Sub\WP_Customize_New_Menu_Control::do_something();
namespace\WP_PRIVACY_DATA_EXPORT_REQUESTS_TABLE::do_something();
