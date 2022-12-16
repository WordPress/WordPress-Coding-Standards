<?php

// Boolean not operator: All OK.
if ( 'bb' !== 'bb' ) {
	if (
		empty( $_GET['refid'] ) &&
		empty( $_GET['nolinks'] ) &&
		! is_page_template( 'page_strategy-center.php' ) &&
		! is_page_template( 'page_confirmation.php' ) &&
		! is_page_template( 'page_debartolo.php' ) &&
		! is_singular( 'offer' )
	) {
		hello();
	}
}

// Good.
if ( ! $var ) {
	// ...
}

// Bad.
if (!$var ) {
	// ...
}

// Bad.
if (  !   $var ) {
	// ...
}

// Logical operators: Ok.
if ( $a === $b && $b === $c ) {}
if ( $a === $b || $b === $c ) {}
if ( $a === $b and $b === $c ) {}
if ( $a === $b or $b === $c ) {}
if ( $a === $b xor $b === $c ) {}

// Logical operators: Too little space.
if ( $a === $b&&$b === $c ) {}
if ( $a === $b||$b === $c ) {}
if ( $a === {$b}and$b === $c ) {}
if ( $a === {$b}or$b === $c ) {}
if ( $a === {$b}xor$b === $c ) {}

// Logical operators: Too much space.
if ( $a === $b     &&     $b === $c ) {}
if ( $a === $b     ||     $b === $c ) {}
if ( $a === $b     and     $b === $c ) {}
if ( $a === $b     or     $b === $c ) {}
if ( $a === $b     xor     $b === $c ) {}

// Logical operators: Multi-line, OK.
if ( $a === $b
	&& $b === $c
) {}
if (
	$a === $b
	||
	$b === $c
) {}
if ( $a === $b
	and $b === $c ) {}

if ( $a === $b or
	$b === $c ) {}

// Safeguard that the "|" in PHP 8.0 union types is disregarded.
function foo( int|float $param ) : string|false {}

// Safeguard that the "&" in PHP 8.1 intersection types is disregarded.
function foo( TypeA&namespace\TypeB $param ) : \TypeC&Partially\Qualified {}

// Safeguard handling of union type separator for readonly properties.
class Foo {
	public readonly int|string $prop;
}
