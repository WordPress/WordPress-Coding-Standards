<?php
// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[] acronym,tgmpa,test_this,test\that

namespace TGMPA;

namespace Acronym\B\C;

namespace AcronymPlugin\B\C;

namespace Test_This\C;

namespace Test\This\C;

namespace Test\That\C;

namespace Test \ /* comment */ This \ C;

namespace A\B\C; // Bad.

use namespace\DEF; // Not our target.
namespace\cname::method(); // Not our target.
$acronym_result = namespace \ blah \ mine();  // Not our target.

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[] wordpress
namespace wordpress\myplugin;

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[] wordpress\myplugin
namespace wordpress\myplugin;

// phpcs:set WordPress.NamingConventions.PrefixAllGlobals prefixes[]
