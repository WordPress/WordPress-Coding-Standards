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
	public $var_namf = 'hello';
	public $varnamf  = 'hello';
	public $_varNamf = 'hello'; // Bad.

	protected $varNamg  = 'hello'; // Bad.
	protected $var_namg = 'hello';
	protected $varnamg  = 'hello';
	protected $_varNamg = 'hello'; // Bad.

	private $_varNamh  = 'hello'; // Bad.
	private $_var_namh = 'hello';
	private $_varnamh  = 'hello';
	private $varNamh   = 'hello'; // Bad.
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
 * Unit test whitelisting.
 */
// phpcs:set WordPress.NamingConventions.ValidVariableName customPropertiesWhitelist[] varName,DOMProperty
echo MyClass::$varName; // Ok, whitelisted.
echo $this->DOMProperty; // Ok, whitelisted.
echo $object->varName;  // Ok, whitelisted.
// phpcs:set WordPress.NamingConventions.ValidVariableName customPropertiesWhitelist[]

echo $object->varName;  // Bad, no longer whitelisted.

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
