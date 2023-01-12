<?php
$varName  = 'hello'; // Bad.
$var_name = 'hello';
$varname  = 'hello';
$_varName = 'hello'; // Bad.

class MyClass {
	var $varName  = 'hello'; // Bad.
	var $var_name = 'hello';
	var $varname  = 'hello';
	var $_varName = 'hello'; // Bad.

	public $varNamf  = 'hello'; // Bad.
	public bool $var_namf = 'hello';
	public $varnamf  = 'hello';
	public $_varNamf = 'hello'; // Bad.

	protected $varNamg  = 'hello'; // Bad.
	protected $var_namg = 'hello';
	protected $varnamg  = 'hello';
	protected string $_varNamg = 'hello'; // Bad.

	private $_varNamh  = 'hello'; // Bad.
	private $_var_namh = 'hello';
	private $_varnamh  = 'hello';
	private int|string $varNamh   = 'hello'; // Bad.
}

echo $varName; // Bad.
echo $var_name;
echo $varname;
echo $_varName; // Bad.

echo "Hello $varName"; // Bad.
echo "Hello $var_name";
echo "Hello ${var_name}";
echo "Hello $varname";
echo "Hello $_varName"; // Bad.

echo 'Hello '.$varName; // Bad.
echo 'Hello '.$var_name;
echo 'Hello '.$varname;
echo 'Hello '.$_varName; // Bad.

echo $_SERVER['var_name'];
echo $_REQUEST['var_name'];
echo $_GET['var_name'];
echo $_POST['var_name'];
echo $GLOBALS['var_name'];

echo MyClass::$varName; // Bad.
echo MyClass::$var_name;
echo MyClass::$varname;
echo MyClass::$_varName; // Bad.
echo MyClass::$VAR_name; // Bad.

echo $this->varName2; // Bad.
echo $this->var_name2;
echo $this->varname2;
echo $this->_varName2; // Bad.
echo $object->varName2; // Bad.
echo $object->var_name2;
echo $object_name->varname2;
echo $object_name->_varName2; // Bad.
echo $object_name->VAR_name; // Bad.

echo $this->myFunction($one, $two);
echo $object->myFunction($one_two);

$error = "format is \$GLOBALS['$varName']"; // Bad.

echo $_SESSION['var_name'];
echo $_FILES['var_name'];
echo $_ENV['var_name'];
echo $_COOKIE['var_name'];

$XML       = 'hello'; // Bad.
$myXML     = 'hello'; // Bad.
$XMLParser = 'hello'; // Bad.
$xmlParser = 'hello'; // Bad.

$ID = 1; // Bad.
$post = get_post( $x );
echo $post->ID;

echo $comment_ID; // Bad.
echo $comment_post_ID; // Bad.
echo $comment_author_IP; // Bad.

$comment = get_comment( 1 );
echo $comment->comment_ID;
echo $comment->comment_post_ID;
echo $comment->comment_author_IP;

class Foo {
	public $_public_leading_underscore;
	private $private_no_underscore_loading;

	function Bar( $VARname ) { // Bad.
		$localVariable = false; // Bad.
		echo Some_Class::$VarName; // Bad.
		echo $this->VAR_name; // Bad.
		$_localVariable = false; // Bad.
		echo Some_Class::$_VarName; // Bad.
		echo $this->_VAR_name; // Bad.
	}

	function Baz( $var_name ) { // Ok.
		$local_variable = false; // Ok.
		echo Some_Class::$var_name; // Ok.
		echo $this->var_name; // Ok.
		$_local_variable = false; // Ok.
		echo Some_Class::$_var_name; // Ok.
		echo $this->_var_name; // Ok.
	}
}

if ( is_category() ) {
	$category = get_queried_object();
	$cat_id = $category->cat_ID;
	$cat_ID = $category->cat_ID; // Bad.
}

$EZSQL_ERROR = array(); // OK

echo "This is a $comment_ID"; // Bad
echo "This is $PHP_SELF with $HTTP_RAW_POST_DATA"; // Ok.

/*
 * Testing custom properties.
 */
// phpcs:set WordPress.NamingConventions.ValidVariableName allowed_custom_properties[] varName,DOMProperty
echo MyClass::$varName; // Ok, allowed.
echo $this->DOMProperty; // Ok, allowed.
echo $object->varName;  // Ok, allowed.
// phpcs:set WordPress.NamingConventions.ValidVariableName allowed_custom_properties[]

echo $object->varName;  // Bad, no longer allowed.

// Code style independent token checking.
echo $object
	// Silly but allowed.
	->
		// Bad.
		varName2
			// More sillyness.
			['test'];
echo $object
	// Silly but allowed.
	->
		// OK.
		var_name2
			// More sillyness.
			['test'];

echo ClassName
	// Silly but allowed.
	::
		// Bad.
		$varName2
			// More sillyness.
			['test'];
echo ClassName
	// Silly but allowed.
	::
		// OK.
		$var_name2
			// More sillyness.
			['test'];

class MultiVarDeclarations {
	public $multiVar1, $multiVar2, // Bad x 2.
		$multiVar3, // Bad.
		// Some comment.
		$multiVar4, // Bad.
		$multiVar5 = false, // Bad.
		$multiVar6 = 123, // Bad.
		$multi_var7 = 'string'; // Ok.

	public function testMultiGlobalAndStatic() {
		global $multiGlobal1, $multi_global2, // Bad x 1.
			$multiGlobal3; // Bad.

		static $multiStatic1, $multi_static2 = false, // Bad x 1.
			// Comment.
			$multiStatic3 = ''; // Bad.
	}
}

echo "This is $post_ID with $ThisShouldBeFlagged"; // Bad.

// Safeguard that illegal property declarations are ignored.
interface PropertiesNotAllowed {
	public $notAllowed;
}

echo "This is \$someName"; // OK, variable is literal text.

echo "This is ${$someName}"; // Bad.
echo "This is ${Foo}"; // Bad.
echo "This is {${getName()}}"; // OK, expression, should be ignored.
echo "This is $Foo?->bar"; // Bad, expression, but the $Foo in it should still be flagged.
echo "This is {$foo['bar']?->baz()()}"; // OK.
echo "This is {$Foo['bar']?->baz()()}"; // Bad, expression, but the $Foo in it should still be flagged.

// Safeguard that parameters in all types of function declarations, including PHP 7.4+ arrow functions, are flagged.
function has_params( $without_default, $with_default = 'default' ) {} // OK.
$closure = function ( $without_default, $with_default = 'default' ) {}; // OK.
$arrow = fn ( $without_default, $with_default = 'default' ) => 10; // OK.

function has_params( $withoutDefault, $withDefault = 'default' ) {} // Bad x 2.
$closure = function ( $withoutDefault, $withDefault = 'default' ) {}; // Bad x 2.
$arrow = fn ( $withoutDefault, $withDefault = 'default' ) => 10; // Bad x 2.

// Safeguard recognizing property access using PHP 8.0 nullsafe operator.
echo $this?->varName2; // Bad.
echo $this?->var_name2;
echo $this?->varname2;
echo $this?->_varName2; // Bad.

// Safeguard handling of PHP 8.1 enums.
enum EnumExample {
	public $notAllowed; // OK, well, not really, but properties are not allowed in enums, so ignore.

	public function method( $paramName ) { // Bad.
		$local_variable = 'OK';
		$localVariable  = 'Bad';
	}
}

// Safeguard ignoring of allowed mixed case property names.
class Has_Mixed_Case_Property {
	public $post_ID; // OK.
}

// Issue #1891 - ensure the sniff does not throw an error if the suggested alternative would be the same as the original name.
$lähtöaika = true; // OK.
$lÄhtÖaika = true; // Bad, but only handled by the sniff if Mbstring is available.
$lÄhtOaika = true; // Bad, handled via transliteration of non-ASCII chars if Mbstring is not available.
