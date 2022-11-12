<?php
// phpcs:set WordPress.Utils.I18nTextDomainFixer old_text_domain[] something-else
// phpcs:set WordPress.Utils.I18nTextDomainFixer new_text_domain php-compatibility-checker

/*
Plugin Name: Correct text domain, multi-line comment, minimal headers.
Description: Make sure your plugins and themes are compatible with newer PHP versions.
Text Domain: php-compatibility-checker
*/

// phpcs:set WordPress.Utils.I18nTextDomainFixer old_text_domain[] bbpress
// phpcs:set WordPress.Utils.I18nTextDomainFixer new_text_domain something-else

/**
 * Not the plugin header.
 *
 * bbPress is forum software with a twist from the creators of WordPress.
 *
 * $Id: bbpress.php 6699 2017-09-13 21:35:09Z johnjamesjacoby $
 *
 * @package bbPress
 * @subpackage Main
 */

/**
 * Plugin Name: Text domain which should be fixed, docblock format.
 * Plugin URI:  https://bbpress.org
 * Description: bbPress is forum software with a twist from the creators of WordPress.
 * Author:      The bbPress Contributors
 * Author URI:  https://bbpress.org
 * Version:     2.6-rc-5
 * Text Domain: bbpress
 * Domain Path: /languages/
 * License:     GPLv2 or later (license.txt)
 */

// phpcs:set WordPress.Utils.I18nTextDomainFixer old_text_domain[] debug-bar

/*
 Plugin Name: Text domain which should be fixed, multi-line comment, minimal headers.
 Description: Adds a debug menu to the admin bar that shows query, cache, and other helpful debugging information.
 Text Domain: debug-bar
 */

/***
 * Not the plugin header, don't get confused with consecutive comments.
 *
 * When logged in as a super admin, these functions will run to provide
 * debugging information when specific super admin menu items are selected.
 *
 * They are not used when a regular user is logged in.
 */
class Debug_Bar {

	/**
	 * Not the plugin header.
	 *
	 * @var string Text Domain: let's make sure not the sniff doesn't confuse docblocks.
	 */
	const VERSION = '0.9.1-alpha';
}

// phpcs:set WordPress.Utils.I18nTextDomainFixer old_text_domain[] debug-bar-constants

/**
 * Text domain which should be fixed, docblock format with PHP doc tags and tab aligned.
 *
 * @package     WordPress\Plugins\Debug Bar Constants
 * @author      Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
 * @link        https://github.com/jrfnl/Debug-Bar-Constants
 * @version     2.0.0
 *
 * @copyright   2013-2018 Juliette Reinders Folmer
 * @license     http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License, version 2 or higher
 *
 * @wordpress-plugin
 * Plugin Name	:	Debug Bar Constants
 * Plugin URI	:	https://wordpress.org/plugins/debug-bar-constants/
 * Description	:	Debug Bar Constants adds new panels to Debug Bar that display all the defined constants for the current request. Requires "Debug Bar" plugin.
 * Version		:	2.0.0
 * Author		:	Juliette Reinders Folmer
 * Author URI	:	http://www.adviesenzo.nl/
 * Depends		:	Debug Bar
 * Text Domain	:	debug-bar-constants
 * Domain Path	:	/languages
 * Copyright	:	2013-2018 Juliette Reinders Folmer
 */

// phpcs:set WordPress.Utils.I18nTextDomainFixer old_text_domain[] a2-optimized

//  Plugin Name: Text domain which should be fixed, multi-line //-style comment.
//  Plugin URI: https://wordpress.org/plugins/a2-optimized/
//  Version: 2.0.1
//  Author: A2 Hosting
//  Author URI: https://www.a2hosting.com/
//  Description: A2 Optimized - WordPress Optimization Plugin
//  Text Domain: a2-optimized
//  License: GPLv2 or Later

//  Plugin Name: Not enough headers to verify this is the header.
//  Plugin URI: https://wordpress.org/plugins/a2-optimized/

/**
 * Plugin Name: Missing text domain, docblock format.
 * Plugin URI: http://www.bigvoodoo.com
 * Description: Ensures the Git repositories are kept current and up to date with uploads made within WordPress.
 * Author: Big Voodoo Interactive
 * Version: 0.1.0
 * Author URI: http://www.bigvoodoo.com
 * @author Big Voodoo Interactive
 * @TODO error reporting
 */

//  Plugin Name: Missing text domain, multi-line //-style comment, minimal headers.
//  Plugin URI: https://wordpress.org/plugins/a2-optimized/
//  Version: 2.0.1

// phpcs:set WordPress.Utils.I18nTextDomainFixer old_text_domain[]
// phpcs:set WordPress.Utils.I18nTextDomainFixer new_text_domain false
