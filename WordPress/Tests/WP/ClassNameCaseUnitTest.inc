<?php

/*
 * These should be disregarded by the sniff.
 */
class Foo {
	function bar() {
		$obj = new self();
		echo static::MY_CONSTANT;
	}
}

$obj = new Not_A_WP_Core_Class();


/*
 * These all use the class name in proper case.
 */
$obj = new WP_Importer();
$obj = new \WP_Query;

class MyList extends WP_List_Table {}

echo WP_User_Search::$users_per_page;
WP_Customize_New_Menu_Control::foo();

// External libraries.
$obj = new getID3;
class MyMailer extends PHPMailer\PHPMailer\PHPMailer {}
$obj = new Requests_Cookie_Jar();
$anon = new class extends SimplePie_IRI {};


/*
 * These all use the class name in an unconventional case.
 */
$obj = new WPDB();
$obj = new \WP_date_query;

class MyList extends \WP_LIST_table {}

echo wp_user_search::$users_per_page;
WP_Customize_NEW_Menu_Control::foo();

// External libraries.
$obj = new GetID3();
class MyMailer extends PhpMailer\PhpMailer\PhpMailer {}
$obj = new Requests_cookie_jar();
$anon = new class extends SimplePie_Iri {};


/*
 * These will not (yet) be detected as the abstract doesn't handle these.
 * This will be fixed in the future when the PHPCSUtils abstract will be made available.
 */

try {
	// Do something.
} catch ( Wp_Error ) {
}

class NotYetDetected {
	public WalKer $property_type;

	public function paramTypeDeclaration( wp_role $role ) {}
	public function returnTypeDeclaration() : WP_TERM {}
}

class ImplementsInterfaceCorrectCase implements SimplePie_Cache_Base {}
class ImplementsInterfaceIncorrectCase implements simplepie_cache_base {}

/*
 * Safeguard correct handling of namespaced class references (the namespace types not handled below are already handled
 * in other tests above).
 */
$obj = new MyNamespace\atomEntry();
$obj = new \MyNamespace\core_upgrader();
$obj = new namespace\Sub\file_upload_upgrader();
$obj = new namespace\pop3(); // Warning.
class MyClass1 extends MyNamespace\twentytwenty_walker_page {}
class MyClass2 extends \MyNamespace\TWENTY_ELEVEN_EPHEMERA_WIDGET {}
class MyClass3 extends namespace\Sub\twenty_Twenty_One_SVG_icons {}
class MyClass4 extends namespace\twentynineteen_SVG_icons {} // Warning.
class MyClass5 implements \WPORG\REQUESTS\AUTH {} // Warning.
class MyClass6 implements \MyNamespace\SIMPLEPIE\Cache\namefilter {}
class MyClass7 implements MyNamespace\requests_auth {}
class MyClass8 implements namespace\Sub\WpOrg\REQUESTS\proxy {}
class MyClass9 implements namespace\simplepie\CACHE\base {} // Warning.
\avifinfo\Box::prepare_query(); // Warning.
MyNamespace\Avifinfo\CHAN_PROP::prepare_query();
\MyNamespace\Avifinfo\features::prepare_query();
namespace\Sub\AVIFINFO\parser::prepare_query();
namespace\AVIFINFO\TILE::prepare_query(); // Warning.
