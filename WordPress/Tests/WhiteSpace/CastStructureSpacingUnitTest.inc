<?php

$array1 = (array)$array; // Bad
$array2 = (array) $array; // Good

$bool1 = (bool)$bool; // Bad
$bool2 = (bool) $bool; // Good

$int1 = (int)$int; // Bad
$int2 = (int) $int // Good

$obj1 = (object)$object; // Bad
$obj2 = (object) $object; // Good

$str1 = (string)$string; // Bad
$str2 = (string) $string; // Good

$unset1 = (unset)$unset; // Bad
$unset2 = (unset) $unset; // Good

$float1 = (float)$float; // Bad
$float2 = (float) $float; // Good
