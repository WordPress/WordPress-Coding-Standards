<?php

function fooa(): array {} // Good.
function foob() : array {} // Bad.
function fooc()     : array {} // Bad.
function food() // Bad.
: array {}
function fooe():array {} // Bad.
function foof():    array {} // Bad.
function foog():
array {}

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
	float {
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
	public function f():Double;
	public function g():?double;
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
	public function z():? \ReturnType \MyType;
}
