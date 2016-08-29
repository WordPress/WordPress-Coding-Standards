<?php

$db_ok1 = new Mymysqli; // Ok.
$db_ok2 = new Mymysqli(); // Ok.

echo MyMysqli::$affected_rows; // Ok.
echo MyMysqli::get_charset(); // Ok.

echo parent::VERSION; // Ok.
echo self::$property; // Ok.
echo self::some_function(); // Ok.

/**
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
$db6 - new PDO->exec();
PDO::getAvailableDrivers();

$db7 = new PDOStatement;
$db8 = new \PDOStatement();

