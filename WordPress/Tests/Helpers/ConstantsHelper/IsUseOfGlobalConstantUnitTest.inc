<?php

/*
 * The below should NOT be recognized as use of a global constant.
 */

/* testVariableAssignment */
$a = 'foo';

/* testFunctionCall */
some_function();

/* testFunctionDeclaration */
function PHP_OS() {}

/* testClassInstantiation */
$obj = new PHP_OS();

/* testStaticMethodCall */
PHP_OS::someMethod();

/* testStaticClassConstantAccess */
PHP_OS::SOME_CONST;

/* testClassNameResolution */
PHP_OS::class;

/* testNamespaceDeclaration */
namespace PHP_OS;

/* testUseStatement */
use PHP_OS\SomeClass;

class MyClass extends /* testClassExtends */ PHP_OS {}
class AnotherClass implements /* testClassImplements */ PHP_OS {}

/* testClassInstantiationNoParentheses */
$obj = new PHP_OS;

/* testInstanceof */
if ( $a instanceof PHP_OS ) {}

class MyClass {
	use TraitA, PHP_OS {
		TraitA::conflictMethod insteadof /* testPrecededByInsteadOf */ PHP_OS;
	}
}

/* testGotoLabel */
goto PHP_OS;

use const ABC as /* testPrecededByAs */ PHP_OS;

/* testClassDeclaration */
class PHP_OS {}

/* testInterfaceDeclaration */
interface PHP_OS {}

/* testTraitDeclaration */
trait PHP_OS {}

/* testEnumDeclaration */
enum PHP_OS {}

/* testObjectPropertyAccess */
$obj->PHP_OS;

/* testNullsafeObjectPropertyAccess */
$obj?->PHP_OS;

SomeClass::/* testClassConstantAccess */PHP_OS;

class ScopeModifiersTest {
	/* testPrecededByPublic */
	public PHP_OS $prop1;

	/* testPrecededByProtected */
	protected PHP_OS $prop2;

	/* testPrecededByPrivate */
	private PHP_OS $prop3;

	/* testPrecededByPublicSet */
	public public(set) PHP_OS $prop4;

	/* testPrecededByProtectedSet */
	public protected(set) PHP_OS $prop5;

	/* testPrecededByPrivateSet */
	public private(set) PHP_OS $prop6;
}

/* testPartiallyQualifiedNamespacedConstant */
MyNamespace\PHP_OS;

/* testFullyQualifiedNamespacedConstant */
\MyNamespace\PHP_OS;

/* testNamespaceRelativeConstant */
namespace\PHP_OS; // This should be considered use of a global constant in the future once the method is able to resolve relative namespaces.

/* testNamespaceRelativeSubConstant */
namespace\Sub\PHP_OS;

class Foo {
	/* testClassConstantDeclaration */
	const PHP_OS = 1;
}

use const SomeNamespace\{/* testUseConstStatementGrouped */ PHP_OS, PHP_VERSION_ID};

use Foo\{Bar, /* testUseStatementGrouped */ PHP_OS};

/*
 * The below should be recognized as use of a global constant.
 */

/* testEchoStatement */
echo PHP_OS;

/* testEchoStatementFullyQualified */
echo \PHP_OS;

// Note: this is counterintuitive behavior, as declaring a constant is not exactly the use of a constant. However, this
// seems intentional considering the docblock of DiscouragedConstantsSniff states that it warns against both usage and
// re-declaration of discouraged WP constants.
/* testConstDeclaration */
const my_const = 'something';
const ABC = '123',
	/* testConstDeclarationInList */ PHP_OS = 'something';

/* testUseConstStatement */
use const PHP_OS as SSP;
