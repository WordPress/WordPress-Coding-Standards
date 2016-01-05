<?php
$varName  = 'hello';
$var_name = 'hello'; // Bad
$varname  = 'hello';
$_varName = 'hello'; // Bad

class MyClass {
	$varName  = 'hello'; // Bad
	$var_name = 'hello';
	$varname  = 'hello';
	$_varName = 'hello'; // Bad

	public $varName  = 'hello'; // Bad
	public $var_name = 'hello';
	public $varname  = 'hello';
	public $_varName = 'hello'; // Bad

	protected $varName  = 'hello'; // Bad
	protected $var_name = 'hello';
	protected $varname  = 'hello';
	protected $_varName = 'hello'; // Bad

	private $_varName  = 'hello'; // Bad
	private $_var_name = 'hello';
	private $_varname  = 'hello';
	private $varName   = 'hello'; // Bad
}

echo $varName; // Bad
echo $var_name;
echo $varname;
echo $_varName; // Bad

echo "Hello $varName"; // Bad
echo "Hello $var_name";
echo "Hello ${var_name}";
echo "Hello $varname";
echo "Hello $_varName"; // Bad

echo 'Hello '.$varName; // Bad
echo 'Hello '.$var_name;
echo 'Hello '.$varname;
echo 'Hello '.$_varName; // Bad

echo $_SERVER['var_name'];
echo $_REQUEST['var_name'];
echo $_GET['var_name'];
echo $_POST['var_name'];
echo $GLOBALS['var_name'];

echo MyClass::$varName; // Bad
echo MyClass::$var_name;
echo MyClass::$varname;
echo MyClass::$_varName; // Bad

echo $this->varName2; // Bad
echo $this->var_name2;
echo $this->varname2;
echo $this->_varName2; // Bad
echo $object->varName2; // Bad
echo $object->var_name2;
echo $object_name->varname2;
echo $object_name->_varName2; // Bad

echo $this->myFunction($one, $two);
echo $object->myFunction($one_two);

$error = "format is \$GLOBALS['$varName']"; // Bad

echo $_SESSION['var_name'];
echo $_FILES['var_name'];
echo $_ENV['var_name'];
echo $_COOKIE['var_name'];

$XML       = 'hello'; // Bad
$myXML     = 'hello'; // Bad
$XMLParser = 'hello'; // Bad
$xmlParser = 'hello'; // Bad

$ID = 1; // Bad
$post = get_post( $x );
echo $post->ID;

echo $comment_ID; // Bad
echo $comment_post_ID; // Bad
echo $comment_author_IP; // Bad

$comment = get_comment( 1 );
echo $comment->comment_ID;
echo $comment->comment_post_ID;
echo $comment->comment_author_IP;

class Foo {
	public $_public_leading_underscore;
	private $private_no_underscore_loading;
}
