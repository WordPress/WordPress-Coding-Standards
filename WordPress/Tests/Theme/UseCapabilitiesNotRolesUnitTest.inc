<?php
/**
* Test file : UseCapabilitiesNotRoles Sniff
*/

add_dashboard_page( 'page_title' , 'menu_title' , 'super_admin' , 'menu_slug' , 'function'); // bad ERROR
add_posts_page( 'page_title' , 'menu_title' , 'administrator' , 'menu_slug' , 'function' ); // bad ERROR
add_media_page( 'page_title' , 'menu_title' , 'editor' , 'menu_slug' , 'function' ); // bad ERROR
add_pages_page( 'page_title' , 'menu_title' , 'author' , 'menu_slug' , 'function' ); // bad ERROR
add_comments_page( 'page_title' , 'menu_title' , 'contributor' , 'menu_slug' , 'function' ); // bad ERROR
add_theme_page( 'page_title' , $menu_title , 'subscriber' , 'menu_slug' , 'function' );  // bad ERROR
add_plugins_page( 'page_title' , 'menu_title' , 'super_admin' , 'menu_slug' , 'function' );  // bad ERROR
add_users_page( 'page_title' , 'menu_title' , 'administrator' , 'menu_slug' , 'function' );  // bad ERROR
add_management_page( 'page_title' , 'menu_title' , 'editor' , 'menu_slug' , 'function' );  // bad ERROR
add_options_page( 'page_title' , 'menu_title' , 'contributor' , 'menu_slug' , 'function' );  // bad ERROR
add_menu_page( $pagetitle , $menu_title , $subscriber , 'handle' , 'function' , 'icon_url' ); // bad WARNING

add_plugins_page( 'page_title' , 'menu_title' , $cap , 'menu_slug' , 'function' );  // bad WARNING
add_users_page( 'page_title' , 'menu_title' , 'xxx' , 'menu_slug' , 'function' ); // OK
add_management_page( 'page_title' , 'menu_title' , 'xxx' , 'menu_slug' , 'function' ); // OK
add_options_page( $pagetitle , $menu_title , $xxx , 'menu_slug' , 'function' ); // bad WARNING
add_menu_page( $pagetitle , 'menu_title' , 'xxx' , 'handle' , 'function' , 'icon_url' ); // OK
add_utility_page(
 'page_title' , 'menu_title' , 'super_admin' , 'menu_slug' , 'function', 'icon_url'  // bad ERROR
);
add_submenu_page(
  'parent_slug' ,
  'page_title' ,
  'menu_title' ,
  $varible ,  // bad WARNING
  'menu_slug' ,
  'function' );

if ( author_can( $post, $capability ) ) { } // bad WARNING
if ( author_can( $post, 'FFFFF' ) ) { }  // OK
if ( current_user_can( 'super_admin' ) ) { } // bad ERROR
if ( current_user_can( 'SSSSS' ) ) { } // OK
if( current_user_can_for_blog( '3' , 'EEEEEE' ) ) { }  // OK
if( current_user_can_for_blog( '1' , 'editor' ) ) { }  // bad ERROR
