<?php

switch_to_blog( $blogid ); // bad

eval( 'some_code' ); // bad

create_function( 'foo', 'bar(foo);' ); // bad

file_get_contents( $url ); // bad

vip_wp_file_get_contents( $url ); // bad

wp_remote_get( $url ); // bad

$ch = curl_init(); // bad

curl_close( $ch ); // bad

extract( array( 'a' => 1 ) ); // bad

add_role( 'test' ); // bad

array_pop( $array ); // ok

class Foo {
	function add_role() {} // ok
}
class Bar {
	static function add_role() {} // ok
}
$x = new Foo();
$x->add_role(); // ok
$y = Bar::add_role(); // ok
\SomeNamespace\add_role(); // ok

\add_role(); // bad
