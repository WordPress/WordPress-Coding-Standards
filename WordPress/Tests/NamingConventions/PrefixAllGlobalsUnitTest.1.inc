<?php
// @codingStandardsChangeSetting WordPress.NamingConventions.PrefixAllGlobals prefixes acronym,tgmpa

/*
 * OK - not in the global namespace.
 */
namespace Acronym;

function do_something() {}

const SOME_CONSTANT = 'value';

class Example {}
interface I_Example {}
trait T_Example {}

// Issue #1056.
define( __NAMESPACE__ . '\PLUGIN_FILE', __FILE__ );

// @codingStandardsChangeSetting WordPress.NamingConventions.PrefixAllGlobals prefixes false
