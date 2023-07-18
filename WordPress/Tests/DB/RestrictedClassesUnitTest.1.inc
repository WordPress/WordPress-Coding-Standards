<?php

$db_ok1 = new Mymysqli; // Ok.
$db_ok2 = new Mymysqli(); // Ok.

echo MyMysqli::$affected_rows; // Ok.
echo MyMysqli::get_charset(); // Ok.

echo parent::VERSION; // Ok.
echo self::$property; // Ok.
echo self::some_function(); // Ok.

/*
 * All the below should give an error.
 */

$db1 = new mysqli;
$db2 = new MYSQLI;
$db3 = new MySqli();
$db4 = new \mysqli();

echo mysqli::$affected_rows;
echo mysqli::$errno;
echo mysqli::get_charset();
mysqli::init();
mysqli::stat();
\mysqli::use_result();

class MyMysqli extends mysqli {}
class YourMysqli extends \mysqli {}

class OurMysqli implements mysqli {}
class TheirMysqli implements \mysqli {}

$db5 = new PDO();
$db6 = ( new PDO() )->exec();
PDO::getAvailableDrivers();

$db7 = new PDOStatement;
$db8 = new \PDOStatement();

$db9 = new \My\DBlayer;

/*
 * Test exclude property.
 */
// Exclude one group:
// phpcs:set WordPress.DB.RestrictedClasses exclude[] test
$db9 = new \My\DBlayer; // Ok - within excluded group.

echo mysqli::$affected_rows; // Error.
class YourMysqliA extends \mysqli {} // Error.

// Exclude all groups:
// phpcs:set WordPress.DB.RestrictedClasses exclude[] test,mysql
$db9 = new \My\DBlayer; // Ok - within excluded group.

echo mysqli::$affected_rows; // Ok - within excluded group.
class YourMysqliB extends \mysqli {} // Ok - within excluded group.

// Reset group exclusions.
// phpcs:set WordPress.DB.RestrictedClasses exclude[]
$db9 = new \My\DBlayer; // Error.

echo mysqli::$affected_rows; // Error.
class YourMysqliC extends \mysqli {} // Error.

// Bug fix: namespace keyword as operator is case-insensitive.
class YourMysqliC extends NameSpace\mysqli {} // Error.

// Issue #2184 Prevent false positives when searching for the class name before a double colon.
class NotOurTarget extends SomethingElse {
	public function doSomething() {
		echo mysqli();
		if (self::$property === static::$property
			|| $obj::$property === parent::$property
		) {}
	}
}

// Bug fix: false negative when an object instantiation ends on a PHP close tag without whitespace.
$db1 = new mysqli?><!-- Error. -->
<div>Something</div>
<?php

// Bug fix: prevent false negatives on object instantiation with chaining.
$db10 = (new PDO)->exec(); // Error.
$db11 = (new PDO)?->exec(); // Error.

// Safeguard handling of class which extends and implements.
class MyMysqliWithArrayAccess extends mysqli implements ArrayAccess {} // Error.

// Safeguard handling of new with hierarchy keywords.
$obj = new self();
$obj = new Parent();
$obj = new STATIC();

// Safeguard handling of PHP 7.0+ anonymous classes.
$anon = new class {
	public function PDO() {} // OK.
};

$anon = new class extends PDOStatement {}; // Error.

// PHP 8.1: enums can implement.
enum MysqliEnum implements mysqli {} // Error.
