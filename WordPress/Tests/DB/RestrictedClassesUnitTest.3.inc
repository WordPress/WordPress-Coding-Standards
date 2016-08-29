<?php

// Additional unit tests testing the abstract class with namespace resolution.

namespace My {

	class DBlayer {
		const VERSION = '1.0';
	
		public static $property = true;
	
		public static function connect() {
		}
	}
	
	class DBextended extends DBlayer { // Bad.
	}
	
	class DBextender implements PDO { // Ok -> resolves to \My\PDO.
	}
	
	class DBextendes implements \PDO { // Bad -> fully qualified as \PDO.
	}
	
	$db0 = new \DBlayer; // Ok - fully qualified as \DBlayer.
	$db1 = new DBlayer; // Bad - resolves to \My\DBlayer.
	$db2 = new DBlayer(); // Bad - resolves to \My\DBlayer.
	
	echo DBlayer::VERSION; // Bad - resolves to \My\DBlayer.
	echo DBlayer::$property; // Bad - resolves to \My\DBlayer.
	echo DBlayer::connect(); // Bad - resolves to \My\DBlayer.
	
	$db3 = new Yours\DBlayer; // Ok - resolves to \My\Yours\DBlayer.
	
	echo Yours\DBlayer::VERSION; // Ok - resolves to \My\Yours\DBlayer.
	echo Yours\DBlayer::$property; // Ok - resolves to \My\Yours\DBlayer.
	echo Yours\DBlayer::connect(); // Ok - resolves to \My\Yours\DBlayer.
	
	$db4 = new \My\DBlayer; // Bad - fully qualified as \My\DBlayer.
	
	echo \My\DBlayer::VERSION; // Bad - fully qualified as \My\DBlayer.
	echo \My\DBlayer::$property; // Bad - fully qualified as \My\DBlayer.
	echo \My\DBlayer::connect(); // Bad - fully qualified as \My\DBlayer.
	
	echo namespace\DBlayer::VERSION; // Bad - resolves to \My\DBlayer.
	echo namespace\DBlayer::$property; // Bad - resolves to \My\DBlayer.
	echo namespace\DBlayer::connect(); // Bad - resolves to \My\DBlayer.

}

// Now we're outside the namespace, so things should work differently.
$db0 = new \DBlayer; // Ok.
$db1 = new DBlayer; // Ok.
$db2 = new DBlayer(); // Ok.

echo DBlayer::VERSION; // Ok.
echo DBlayer::$property; // Ok.
echo DBlayer::connect(); // Ok.

$db3 = new Yours\DBlayer; // Ok - resolves to \Yours\DBlayer.

echo Yours\DBlayer::VERSION; // Ok - resolves to \Yours\DBlayer.
echo Yours\DBlayer::$property; // Ok - resolves to \Yours\DBlayer.
echo Yours\DBlayer::connect(); // Ok - resolves to \Yours\DBlayer.

$db4 = new \My\DBlayer; // Bad - fully qualified as \My\DBlayer.

echo \My\DBlayer::VERSION; // Bad - fully qualified as \My\DBlayer.
echo \My\DBlayer::$property; // Bad - fully qualified as \My\DBlayer.
echo \My\DBlayer::connect(); // Bad - fully qualified as \My\DBlayer.

echo namespace\DBlayer::VERSION; // Ok.
echo namespace\DBlayer::$property; // Ok.
echo namespace\DBlayer::connect(); // Ok.


// Testing second namespace within one file.
namespace AdoDb {

	class Test {}

	class Tester {}

	class TestAgain extends Test {} // Bad.
	
	class TestYetAgain extends Tester {} // Bad.

	$db5 = new Test; // Bad.
	$db6 = new Tester(); // Bad.
}

$db7 = new Test; // Ok.
