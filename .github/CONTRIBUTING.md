Hi, thank you for your interest in contributing to the WordPress Coding Standards! We look forward to working with you.

# Reporting Bugs

Please search the repo to see if your issue has been reported already and if so, comment in that issue instead of opening a new one.

Before reporting a bug, you should check what sniff an error is coming from.
Running `phpcs` with the `-s` flag will show the name of the sniff with each error.

Bug reports containing a minimal code sample which can be used to reproduce the issue are highly appreciated as those are most easily actionable.

## Upstream Issues

Since WordPressCS employs many sniffs that are part of PHP_CodeSniffer itself or PHPCSExtra, sometimes an issue will be caused by a bug in PHPCS or PHPCSExtra and not in WordPressCS itself.
If the error message in question doesn't come from a sniff whose name starts with `WordPress`, the issue is probably a bug in PHPCS or PHPCSExtra.

* Bugs for sniffs starting with `Generic`, `PEAR`, `PSR1`, `PSR2`, `PSR12`, `Squiz` or `Zend` should be [reported to PHPCS](https://github.com/PHPCSStandards/PHP_CodeSniffer/issues).
* Bugs for sniffs starting with `Modernize`, `NormalizedArrays` or `Universal` should be [reported to PHPCSExtra](https://github.com/PHPCSStandards/PHPCSExtra/issues).

# Contributing patches and new features

## Branches

Ongoing development will be done in the `develop` branch with merges to `main` once considered stable.

To contribute an improvement to this project, fork the repo, run `composer install`, make your changes to the code, run the unit tests and code style checks by running `composer check-all`, and if all is good, open a pull request to the `develop` branch.
Alternatively, if you have push access to this repo, create a feature branch prefixed by `feature/` and then open an intra-repo PR from that branch to `develop`.

# Considerations when writing sniffs

## Public properties

When writing sniffs, always remember that any `public` sniff property can be overruled via a custom ruleset by the end-user.
Only make a property `public` if that is the intended behaviour.

When you introduce new `public` sniff properties, or your sniff extends a class from which you inherit a `public` property, please don't forget to update the [public properties wiki page](https://github.com/WordPress/WordPress-Coding-Standards/wiki/Customizable-sniff-properties) with the relevant details once your PR has been merged into the `develop` branch.

# Unit Testing

## Pre-requisites
* WordPress-Coding-Standards
* PHP_CodeSniffer 3.8.0 or higher
* PHPCSUtils 1.0.8 or higher
* PHPCSExtra 1.2.0 or higher
* PHPUnit 4.x, 5.x, 6.x or 7.x

The WordPress Coding Standards use the `PHP_CodeSniffer` native unit test framework for unit testing the sniffs.

## Getting ready to test

Presuming you have cloned WordPressCS for development, to run the unit tests you need to make sure you have run `composer install` from the root directory of your WordPressCS git clone.

## Custom develop setups

If you are developing with a stand-alone PHP_CodeSniffer (git clone) installation and want to use that git clone to test WordPressCS, there are three extra things you need to do:
1. Install [PHPCSUtils](https://github.com/PHPCSStandards/PHPCSUtils).
    If you are using a git clone of PHPCS, you may want to `git clone` PHPCSUtils as well.
2. Register PHPCSUtils with your stand-alone PHP_CodeSniffer installation by running the following commands:
    ```bash
    phpcs --config-show
    ```
    Copy the value from "installed_paths" and add the path to your local install of PHPCSUtils to it (and the path to WordPressCS if it's not registered with PHPCS yet).
    Now use the adjusted value to run:
    ```bash
    phpcs --config-set installed_paths /path/1,/path/2,/path/3
    ```
3. Make sure PHPUnit can find your `PHP_CodeSniffer` install.
    The most straight-forward way to do this is to add a `phpunit.xml` file to the root of your WordPressCS installation and set a `PHPCS_DIR` environment variable from within this file.
    Copy the existing `phpunit.xml.dist` file and add the below `<env>` directive within the `<php>` section. Make sure to adjust the path to reflect your local setup.
    ```xml
        <php>
            <env name="PHPCS_DIR" value="/path/to/PHP_CodeSniffer/"/>
        </php>
    ```

## Running the unit tests

From the root of your WordPressCS install, run the unit tests like so:
```bash
composer run-tests

# Or if you want to use a globally installed version of PHPUnit:
phpunit --filter WordPress /path/to/PHP_CodeSniffer/tests/AllTests.php
```

Expected output:
```
PHPUnit 7.5.20 by Sebastian Bergmann and contributors.

Runtime:       PHP 7.4.33
Configuration: /WordPressCS/phpunit.xml.dist

.........................................................         57 / 57 (100%)

201 sniff test files generated 744 unique error codes; 50 were fixable (6%)

Time: 10.19 seconds, Memory: 40.00 MB

OK (57 tests, 0 assertions)
```

## Unit Testing conventions

If you look inside the `WordPress/Tests` subdirectory, you'll see the structure mimics the `WordPress/Sniffs` subdirectory structure. For example, the `WordPress/Sniffs/PHP/POSIXFunctionsSniff.php` sniff has its unit test class defined in `WordPress/Tests/PHP/POSIXFunctionsUnitTest.php` which checks the `WordPress/Tests/PHP/POSIXFunctionsUnitTest.inc` test case file. See the file naming convention?

Lets take a look at what's inside `POSIXFunctionsUnitTest.php`:

```php
namespace WordPressCS\WordPress\Tests\PHP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

final class POSIXFunctionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
	 */
	public function getErrorList() {
		return array(
			13 => 1,
			16 => 1,
			18 => 1,
			20 => 1,
			22 => 1,
			24 => 1,
			26 => 1,
		);
	}

    ...
}
```

Also note the class name convention. The method `getErrorList()` MUST return an array of line numbers indicating errors (when running `phpcs`) found in `WordPress/Tests/PHP/POSIXFunctionsUnitTest.inc`. Similarly, the `getWarningList()` method must return an array of line numbers with the number of expected warnings.

If you run the following from the root directory of your WordPressCS clone:

```sh
$ "vendor/bin/phpcs" --standard=Wordpress -s ./Tests/PHP/POSIXFunctionsUnitTest.inc --sniffs=WordPress.PHP.POSIXFunctions
...
--------------------------------------------------------------------------------
FOUND 7 ERRORS AFFECTING 7 LINES
--------------------------------------------------------------------------------
 13 | ERROR | ereg() has been deprecated since PHP 5.3 and removed in PHP 7.0,
    |       | please use preg_match() instead.
    |       | (WordPress.PHP.POSIXFunctions.ereg_ereg)
 16 | ERROR | eregi() has been deprecated since PHP 5.3 and removed in PHP 7.0,
    |       | please use preg_match() instead.
    |       | (WordPress.PHP.POSIXFunctions.ereg_eregi)
 18 | ERROR | ereg_replace() has been deprecated since PHP 5.3 and removed in
    |       | PHP 7.0, please use preg_replace() instead.
    |       | (WordPress.PHP.POSIXFunctions.ereg_replace_ereg_replace)
 20 | ERROR | eregi_replace() has been deprecated since PHP 5.3 and removed in
    |       | PHP 7.0, please use preg_replace() instead.
    |       | (WordPress.PHP.POSIXFunctions.ereg_replace_eregi_replace)
 22 | ERROR | split() has been deprecated since PHP 5.3 and removed in PHP 7.0,
    |       | please use explode(), str_split() or preg_split() instead.
    |       | (WordPress.PHP.POSIXFunctions.split_split)
 24 | ERROR | spliti() has been deprecated since PHP 5.3 and removed in PHP
    |       | 7.0, please use explode(), str_split() or preg_split()
    |       | instead. (WordPress.PHP.POSIXFunctions.split_spliti)
 26 | ERROR | sql_regcase() has been deprecated since PHP 5.3 and removed in
    |       | PHP 7.0, please use preg_match() instead.
    |       | (WordPress.PHP.POSIXFunctions.ereg_sql_regcase)
--------------------------------------------------------------------------------
...
```
You'll see the line number and number of ERRORs we need to return in the `getErrorList()` method.

The `--sniffs=...` directive limits the output to the sniff you are testing.

## Code Standards for this project

The sniffs and test files - not test _case_ files! - for WordPressCS should be written such that they pass the `WordPress-Extra` and the `WordPress-Docs` code standards using the custom ruleset as found in `/.phpcs.xml.dist`.
