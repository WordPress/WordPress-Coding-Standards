# Contributing to the WordPress Coding Standards

Hi, thank you for your interest in contributing to the WordPress Coding Standards! We look forward to working with you.

## Reporting Bugs

Please search the repo to see if your issue has been reported already and if so, comment in that issue instead of opening a new one.

Before reporting a bug, you should check what sniff an error is coming from.
Running `phpcs` with the `-s` flag will show the name of the sniff with each error.

Bug reports containing a minimal code sample which can be used to reproduce the issue are highly appreciated as those are most easily actionable.

### Upstream Issues

Since WordPressCS employs many sniffs that are part of PHP_CodeSniffer itself or PHPCSExtra, sometimes an issue will be caused by a bug in PHPCS or PHPCSExtra and not in WordPressCS itself.
If the error message in question doesn't come from a sniff whose name starts with `WordPress`, the issue is probably a bug in PHPCS or PHPCSExtra.

* Bugs for sniffs starting with `Generic`, `PEAR`, `PSR1`, `PSR2`, `PSR12`, `Squiz` or `Zend` should be [reported to PHPCS](https://github.com/PHPCSStandards/PHP_CodeSniffer/issues).
* Bugs for sniffs starting with `Modernize`, `NormalizedArrays` or `Universal` should be [reported to PHPCSExtra](https://github.com/PHPCSStandards/PHPCSExtra/issues).

## Contributing patches and new features

### Tips for Successful PRs

We welcome contributions from everyone, and want your PR to have the best chance of being reviewed and merged. To help with this, please keep the following in mind:

* **Respect copyright and licensing.**
  Only submit code that you have written yourself or that comes from sources where the license clearly allows inclusion. Submitting code that infringes on copyright or licensing terms puts both you and the project at legal risk, and such contributions cannot be accepted.

* **Do not submit AI-generated code.**
  Pull requests containing AI-generated code are not acceptable. Beyond copyright and licensing uncertainties, AI-generated contributions consistently require disproportionate amounts of maintainer time to review, correct, or rewrite. This wastes limited project resources and slows progress for everyone. Submitting AI-generated code may be treated as a violation of our [Code of Conduct](../CODE_OF_CONDUCT.md).

* **Focus on quality and clarity.**
  Take time to explain *why* the change is needed, and include tests or examples where appropriate. Clear, self-written explanations make it more straightforward for reviewers to understand what you are trying to achieve.

* **Think about long-term maintainability.**
  Code should align with WordPress Coding Standards and be written in a way that others can readily read, understand, and maintain.

* **Be collaborative.**
  If you're unsure about an approach, open an issue first to start a conversation.

By following these tips, you'll save time for both yourself and the maintainers — and increase the likelihood that your contribution can be merged smoothly.

### Branches

Ongoing development will be done in the `develop` branch with merges to `main` once considered stable.

To contribute an improvement to this project, fork the repo, run `composer install`, make your changes to the code, run the unit tests and code style checks by running `composer check-all`, and if all is good, open a pull request to the `develop` branch.
Alternatively, if you have push access to this repo, create a feature branch prefixed by `feature/` and then open an intra-repo PR from that branch to `develop`.

## Considerations when writing sniffs

### Public properties

When writing sniffs, always remember that any `public` sniff property can be overruled via a custom ruleset by the end-user.
Only make a property `public` if that is the intended behavior.

When you introduce new `public` sniff properties, or your sniff extends a class from which you inherit a `public` property, please don't forget to update the [public properties wiki page](https://github.com/WordPress/WordPress-Coding-Standards/wiki/Customizable-sniff-properties) with the relevant details once your PR has been merged into the `develop` branch.

## Unit Testing

### Pre-requisites
* WordPress-Coding-Standards
* PHP_CodeSniffer 3.13.4 or higher
* PHPCSUtils 1.1.0 or higher
* PHPCSExtra 1.5.0 or higher
* PHPUnit 8.x - 9.x

The WordPress Coding Standards use the `PHP_CodeSniffer` native unit test framework for unit testing the sniffs.

### Getting ready to test

Presuming you have cloned WordPressCS for development, to run the unit tests you need to make sure you have run `composer install` from the root directory of your WordPressCS git clone.

### Custom develop setups

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

### Running the unit tests

From the root of your WordPressCS install, run the unit tests like so:
```bash
composer run-tests

# Or if you want to use a globally installed version of PHPUnit:
phpunit --filter WordPress /path/to/PHP_CodeSniffer/tests/AllTests.php
```

Expected output:
```
PHPUnit 9.6.26 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.4.12
Configuration: /WordPressCS/phpunit.xml.dist

............................................................      60 / 60 (100%)

210 sniff test files generated 775 unique error codes; 50 were fixable (6%)

Time: 00:03.396, Memory: 60.00 MB

OK (60 tests, 6 assertions)
```

### Unit Testing conventions

If you look inside the `WordPress/Tests` subdirectory, you'll see the structure mimics the `WordPress/Sniffs` subdirectory structure. For example, the `WordPress/Sniffs/PHP/TypeCastsSniff.php` sniff has its unit test class defined in `WordPress/Tests/PHP/TypeCastsUnitTest.php` which checks the `WordPress/Tests/PHP/TypeCastsUnitTest.inc` test case file. See the file naming convention?

Lets take a look at what's inside `TypeCastsUnitTest.php`:

```php
namespace WordPressCS\WordPress\Tests\PHP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

final class TypeCastsUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array<int, int> Key is the line number, value is the number of expected errors.
	 */
	public function getErrorList() {
		return array(
			10 => 1,
			11 => 1,
			13 => 1,
			26 => 1,
			27 => 1,
			28 => 1,
		);
	}

    ...
}
```

Also note the class name convention. The method `getErrorList()` MUST return an array of line numbers indicating errors (when running `phpcs`) found in `WordPress/Tests/PHP/TypeCastsUnitTest.inc`. Similarly, the `getWarningList()` method must return an array of line numbers with the number of expected warnings.

If you run the following from the root directory of your WordPressCS clone:

```sh
$ "vendor/bin/phpcs" --standard=Wordpress -s ./WordPress/Tests/PHP/TypeCastsUnitTest.inc --sniffs=WordPress.PHP.TypeCasts
...
----------------------------------------------------------------------------------------------------
FOUND 6 ERRORS AND 4 WARNINGS AFFECTING 10 LINES
----------------------------------------------------------------------------------------------------
 10 | ERROR   | [x] Normalized type keywords must be used; expected "(float)" but found "(double)"
    |         |     (WordPress.PHP.TypeCasts.DoubleRealFound)
 11 | ERROR   | [x] Normalized type keywords must be used; expected "(float)" but found "(real)"
    |         |     (WordPress.PHP.TypeCasts.DoubleRealFound)
 13 | ERROR   | [ ] Using the "(unset)" cast is forbidden as the type cast is removed in PHP 8.0.
    |         |     Use the "unset()" language construct instead.
    |         |     (WordPress.PHP.TypeCasts.UnsetFound)
 15 | WARNING | [ ] Using binary casting is strongly discouraged. Found: "(binary)"
    |         |     (WordPress.PHP.TypeCasts.BinaryFound)
 16 | WARNING | [ ] Using binary casting is strongly discouraged. Found: "b"
    |         |     (WordPress.PHP.TypeCasts.BinaryFound)
 17 | WARNING | [ ] Using binary casting is strongly discouraged. Found: "b"
    |         |     (WordPress.PHP.TypeCasts.BinaryFound)
 26 | ERROR   | [x] Normalized type keywords must be used; expected "(float)" but found "(double)"
    |         |     (WordPress.PHP.TypeCasts.DoubleRealFound)
 27 | ERROR   | [x] Normalized type keywords must be used; expected "(float)" but found "(real)"
    |         |     (WordPress.PHP.TypeCasts.DoubleRealFound)
 28 | ERROR   | [ ] Using the "(unset)" cast is forbidden as the type cast is removed in PHP 8.0.
    |         |     Use the "unset()" language construct instead.
    |         |     (WordPress.PHP.TypeCasts.UnsetFound)
 29 | WARNING | [ ] Using binary casting is strongly discouraged. Found: "(binary)"
    |         |     (WordPress.PHP.TypeCasts.BinaryFound)
----------------------------------------------------------------------------------------------------
PHPCBF CAN FIX THE 4 MARKED SNIFF VIOLATIONS AUTOMATICALLY
----------------------------------------------------------------------------------------------------
```
You'll see the line number and number of ERRORs we need to return in the `getErrorList()` method.

The `--sniffs=...` directive limits the output to the sniff you are testing.

### Code Standards for this project

The sniffs and test files - not test _case_ files! - for WordPressCS should be written such that they pass the `WordPress-Extra` and the `WordPress-Docs` code standards using the custom ruleset as found in `/.phpcs.xml.dist`.
