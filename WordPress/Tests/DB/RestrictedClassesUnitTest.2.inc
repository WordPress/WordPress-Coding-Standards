<?php

// Additional unit tests testing the abstract class with namespace resolution.

namespace My;

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
