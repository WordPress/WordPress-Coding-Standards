<?php

$array1 = (array)$array; // bad
$array2 = (array) $array; // good

$bool1 = (bool)$bool; // bad
$bool2 = (bool) $bool; // good

$int1 = (int)$int; // bad
$int2 = (int) $int // good

$obj1 = (object)$object; // bad
$obj2 = (object) $object; // good

$str1 = (string)$string; // bad
$str2 = (string) $string; // good

$unset1 = (unset)$unset; // bad
$unset2 = (unset) $unset; // good

$float1 = (float)$float; // bad
$float2 = (float) $float; // good
