<?php

function fooa(): array {} // Good.
function foob() : array {} // Bad, space before colon.
function fooc()     : array {} // Bad, multiple spaces before colon.
function food():array {} // Bad, no space after colon.
function fooe():    array {} // Bad, more than 1 space after colon.

function foof() // Bad, but don't fix.
: array {}
function foog(): // Bad, but don't fix.
array {}
function fooh() /* Return declaration. */ : array { // Bad, but don't fix.
	return array();
}
function fooi(): /* Return declaration. */ array { // Bad, but don't fix.
	return array();
}

// Don't touch case statements here.
$v = 1;
switch ($v) {
	case 1:
		$x = $f1(-1 * $v);
		break;
	case (2) :
		$x = $f2(-1 * $v, $v);
		break;
	default:
		$x = $f1($v) + $f2(-1 * $v, $v);
		break;
}

class ReturnType
{
	public function method() :array // Bad.
	{
		return [];
	}

	private function priv(
		$a,
		$b
	)
	: int { // Bad.
		return $a * $b;
	}
}

$c = new class() {
	public function method() :
	float { // Bad on line above.
		return mt_rand();
	}
};

abstract class AbsCla
{
	abstract public function x() :bool; // Bad.
}

interface MyInterface
{ // All below are bad.
	public function a():vOid;
	public function b() : Int;
	public function c() :?int;
	public function d() :Float;
	public function e() :? float;
	public function h():Array;
	public function i() : ?array;
	public function j():String;
	public function k():?string;
	public function l():Parent;
	public function m():?parent;
	public function n():Callable;
	public function o():?callable;
	public function p():Bool;
	public function q():?bool;
	public function r():Self;
	public function s():?self;
	public function t():Iterable;
	public function u():?iterable;

	public function v($a) :  \DateTime;
	public function w():?\DateTime;

	public function y($a, $b, $c) : \My\TestClass;
}
