Hi, thank you for your interest in contributing to the WordPress Coding Standards! We look forward to working with you.

# Reporting Bugs

Before reporting a bug, you should check what sniff an error is coming from.
Running `phpcs` with the `-s` flag will show the name of the sniff with each error.

Bug reports containing a minimal code sample which can be used to reproduce the issue are highly appreciated as those are most easily actionable.

## Upstream Issues

Since WPCS employs many sniffs that are part of PHPCS, sometimes an issue will be caused by a bug in PHPCS and not in WPCS itself. If the error message in question doesn't come from a sniff whose name starts with `WordPress`, the issue is probably a bug in PHPCS itself, and should be [reported there](https://github.com/squizlabs/PHP_CodeSniffer/issues).

# Contributing patches and new features

## Branches

Ongoing development will be done in the `develop` with merges done into `master` once considered stable.

To contribute an improvement to this project, fork the repo and open a pull request to the `develop` branch. Alternatively, if you have push access to this repo, create a feature branch prefixed by `feature/` and then open an intra-repo PR from that branch to `develop`.

Once a commit is made to `develop`, a PR should be opened from `develop` into `master` and named "Next release". This PR will provide collaborators with a forum to discuss the upcoming stable release.

# Considerations when writing sniffs

## Public properties

When writing sniffs, always remember that any `public` sniff property can be overruled via a custom ruleset by the end-user.
Only make a property `public` if that is the intended behaviour.

When you introduce new `public` sniff properties, or your sniff extends a class from which you inherit a `public` property, please don't forget to update the [public properties wiki page](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties) with the relevant details once your PR has been merged into the `develop` branch.

## Whitelist comments

Sometimes, a sniff will flag code which upon further inspection by a human turns out to be OK.
If the sniff you are writing is susceptible to this, please consider adding the ability to [whitelist lines of code](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Whitelisting-code-which-flags-errors).

To this end, the `WordPress\Sniff::has_whitelist_comment()` method was introduced.

Example usage:
```php
namespace WordPress\Sniffs\CSRF;

use WordPress\Sniff;

class NonceVerificationSniff extends Sniff {

	public function process_token( $stackPtr ) {

		// Check something.
		
		if ( $this->has_whitelist_comment( 'CSRF', $stackPtr ) ) {
			return;
		}
		
		$this->phpcsFile->addError( ... );
	}
}
```

When you introduce a new whitelist comment, please don't forget to update the [whitelisting code wiki page](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Whitelisting-code-which-flags-errors) with the relevant details once your PR has been merged into the `develop` branch.


# Unit Testing

## Pre-requisites
* WordPress-Coding-Standards
* PHP CodeSniffer 2.9.x or 3.x
* PHPUnit 4.x or 5.x

The WordPress Coding Standards use the PHP CodeSniffer native unit test suite for unit testing the sniffs.

Presuming you have installed PHP CodeSniffer and the WordPress-Coding-Standards as [noted in the README](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards#how-to-use-this), all you need now is `PHPUnit`.

N.B.: If you installed WPCS using Composer, make sure you used `--prefer-source` or run `composer install --prefer-source` now to make sure the unit tests are available.

If you already have PHPUnit installed on your system: Congrats, you're all set.

If not, you can navigate to the directory where the `PHP_CodeSniffer` repo is checked out and do `composer install` to install the `dev` dependencies.
Alternatively, you can [install PHPUnit](https://phpunit.de/manual/5.7/en/installation.html) as a PHAR file.

## Before running the unit tests

N.B.: _If you used Composer to install the WordPress Coding Standards, you can skip this step._

For the unit tests to work, you need to make sure PHPUnit can find your `PHP_CodeSniffer` install.

The easiest way to do this is to add a `phpunit.xml` file to the root of your WPCS installation and set a `PHPCS_DIR` environment variable from within this file. Make sure to adjust the path to reflect your local setup.
```xml
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:noNamespaceSchemaLocation="http://schema.phpunit.de/4.1/phpunit.xsd"
	backupGlobals="true">
	<php>
		<env name="PHPCS_DIR" value="/path/to/PHP_CodeSniffer/"/>
	</php>
</phpunit>
```

## Running the unit tests

The WordPress Coding Standards are compatible with both PHPCS 2.x as well as 3.x. This has some implications for running the unit tests.

* Navigate to the directory in which you installed WPCS.
* To run the unit tests with PHPCS 3.x:
    ```sh
    phpunit --bootstrap="./Test/phpcs3-bootstrap.php" --filter WordPress /path/to/PHP_CodeSniffer/tests/AllTests.php
    ```
* To run the unit tests with PHPCS 2.x:
    ```sh
    phpunit --bootstrap="./Test/phpcs2-bootstrap.php" --filter WordPress ./Test/AllTests.php
    ```

Expected output:
```
PHPUnit 4.8.19 by Sebastian Bergmann and contributors.

Runtime:        PHP 7.1.3 with Xdebug 2.5.1
Configuration:  /WordPressCS/phpunit.xml

......................................................

Tests generated 558 unique error codes; 48 were fixable (8.6%)

Time: 12.25 seconds, Memory: 24.00Mb

OK (54 tests, 0 assertions)
```

[![asciicast](https://asciinema.org/a/98078.png)](https://asciinema.org/a/98078)

## Unit Testing conventions

If you look inside the `WordPress/Tests` subdirectory, you'll see the structure mimics the `WordPress/Sniffs` subdirectory structure. For example, the `WordPress/Sniffs/PHP/POSIXFunctionsSniff.php` sniff has its unit test class defined in `WordPress/Tests/PHP/POSIXFunctionsUnitTest.php` which checks the `WordPress/Tests/PHP/POSIXFunctionsUnitTest.inc` test case file. See the file naming convention?

Lets take a look at what's inside `POSIXFunctionsUnitTest.php`:

```php
...
namespace WordPress\Tests\PHP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class POSIXFunctionsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
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
```

Also note the class name convention. The method `getErrorList()` MUST return an array of line numbers indicating errors (when running `phpcs`) found in `WordPress/Tests/PHP/POSIXFunctionsUnitTest.inc`.
If you run:

```sh
$ cd /path-to-cloned/phpcs
$ ./bin/phpcs --standard=Wordpress -s /path/to/WordPress/Tests/PHP/POSIXFunctionsUnitTest.inc --sniffs=WordPress.PHP.POSIXFunctions
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
 18 | ERROR | ereg_replace() has been deprecated since PHP 5.3 and removed in PHP
    |       | 7.0, please use preg_replace() instead.
    |       | (WordPress.PHP.POSIXFunctions.ereg_replace_ereg_replace)
 20 | ERROR | eregi_replace() has been deprecated since PHP 5.3 and removed in PHP
    |       | 7.0, please use preg_replace() instead.
    |       | (WordPress.PHP.POSIXFunctions.ereg_replace_eregi_replace)
 22 | ERROR | split() has been deprecated since PHP 5.3 and removed in PHP 7.0,
    |       | please use explode(), str_split() or preg_split() instead.
    |       | (WordPress.PHP.POSIXFunctions.split_split)
 24 | ERROR | spliti() has been deprecated since PHP 5.3 and removed in PHP 7.0,
    |       | please use explode(), str_split() or preg_split() instead.
    |       | (WordPress.PHP.POSIXFunctions.split_spliti)
 26 | ERROR | sql_regcase() has been deprecated since PHP 5.3 and removed in PHP
    |       | 7.0, please use preg_match() instead.
    |       | (WordPress.PHP.POSIXFunctions.ereg_sql_regcase)
--------------------------------------------------------------------------------
....
```
You'll see the line number and number of ERRORs we need to return in the `getErrorList()` method.

The `--sniffs=...` directive limits the output to the sniff you are testing.

## Code Standards for this project

The sniffs and test files - not test _case_ files! - for WPCS should be written such that they pass the `WordPress-Extra` and the `WordPress-Docs` code standards using the custom ruleset as found in `/bin/phpcs.xml`.
