# Upstream Issues

Since WPCS employs many sniffs that are part of PHPCS, sometimes an issue will be caused by a bug in PHPCS and not in WPCS itself. Before reporting a bug, you should check what sniff an error is coming from. Running `phpcs` with the `-s` flag, which will show the names of the sniffs with each error. If the error message in question doesn't come from a sniff whose name starts with `WordPress`, the issue is probably a bug in PHPCS itself, and should be [reported there](https://github.com/squizlabs/PHP_CodeSniffer/issues).

# Branches

Ongoing development will be done in the `develop` with merges done into `master` once considered stable.

To contribute an improvement to this project, fork the repo and open a pull request to the `develop` branch. Alternatively, if you have push access to this repo, create a feature branch prefixed by `feature/` and then open an intra-repo PR from that branch to `develop`.

Once a commit is made to `develop`, a PR should be opened from `develop` into `master` and named "Next release". This PR will then serve provide a second round of Travis CI checks (especially for any hotfixes pushed directly to the `develop` branch), and provide collaborators with a forum to discuss the upcoming stable release.

# Considerations when writing sniffs

## Public properties

When writing sniffs, always remember that any `public` sniff property can be overruled via a custom ruleset by the end-user.
Only make a property `public` if that is the intended behaviour.

When you introduce new `public` sniff properties, or your sniff extends a class from which you inherit a `public` property, please don't forget to update the [public properties wiki page](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties) with the relevant details once your PR has been merged into the `develop` branch.

## Whitelist comments

Sometimes, a sniff will flag code which upon further inspection by a human turns out to be OK.
If the sniff you are writing is susceptible to this, please consider adding the ability to [whitelist lines of code](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Whitelisting-code-which-flags-errors).

To this end, the `WordPress_Sniff::has_whitelist_comment()` method was introduced.

Example usage:
```php
class WordPress_Sniffs_CSRF_NonceVerificationSniff extends WordPress_Sniff {

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

TL;DR

If you have installed `phpcs` and the WordPress-Coding-Standards as [noted in the README](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards#how-to-use-this), then you can navigate to the directory where the `phpcs` repo is checked out and do:

```sh
composer install
vendor/bin/phpunit --filter WordPress tests/AllTests.php
```

Expected output:

[![asciicast](https://asciinema.org/a/98078.png)](https://asciinema.org/a/98078)

You can ignore any skipped tests as these are for `PHP_CodeSniffer` external tools.

The reason why we need to checkout from `PHP_CodeSniffer` git repo to run the tests is because
PEAR installation is intended for ready-to-use not for development. At some point `WordPress-Coding-Standards`
might be submitted to `PHP_CodeSniffer` repo and using their existing convention for unit tests
will eventually help them to test the code before merging in.

## Unit Testing conventions

If you see inside the `WordPress/Tests`, the structure mimics the `WordPress/Sniffs`. For example,
the `WordPress/Sniffs/Arrays/ArrayDeclarationSniff.php` sniff has unit test class defined in
`WordPress/Tests/Arrays/ArrayDeclarationUnitTest.php` that check `WordPress/Tests/Arrays/ArrayDeclarationUnitTest.inc`
file. See the file naming convention? Lets take a look what inside `ArrayDeclarationUnitTest.php`:

```php
...
class WordPress_Tests_Arrays_ArrayDeclarationUnitTest extends AbstractSniffUnitTest
{
    public function getErrorList()
    {
        return array(
                3 => 1,
                7 => 1,
                9 => 1,
                16 => 1,
                31 => 2,
               );

    }//end getErrorList()
}
...
```

Also note the class name convention. The method `getErrorList` MUST return an array of line numbers
indicating errors (when running `phpcs`) found in `WordPress/Tests/Arrays/ArrayDeclarationUnitTest.inc`.
If you run:

```sh
$ cd /path-to-cloned/phpcs
$ ./scripts/phpcs --standard=Wordpress -s CodeSniffer/Standards/WordPress/Tests/Arrays/ArrayDeclarationUnitTest.inc
...
--------------------------------------------------------------------------------
FOUND 8 ERROR(S) AND 2 WARNING(S) AFFECTING 6 LINE(S)
--------------------------------------------------------------------------------
  3 | ERROR   | Array keyword should be lower case; expected "array" but found
    |         | "Array" (WordPress.Arrays.ArrayDeclaration)
  7 | ERROR   | There must be no space between the Array keyword and the
    |         | opening parenthesis (WordPress.Arrays.ArrayDeclaration)
  9 | ERROR   | Empty array declaration must have no space between the
    |         | parentheses (WordPress.Arrays.ArrayDeclaration)
 12 | WARNING | No space after opening parenthesis of array is bad style
    |         | (WordPress.Arrays.ArrayDeclaration)
 12 | WARNING | No space before closing parenthesis of array is bad style
    |         | (WordPress.Arrays.ArrayDeclaration)
 16 | ERROR   | Each line in an array declaration must end in a comma
    |         | (WordPress.Arrays.ArrayDeclaration)
 31 | ERROR   | Expected 1 space between "'type'" and double arrow; 0 found
    |         | (WordPress.Arrays.ArrayDeclaration)
 31 | ERROR   | Expected 1 space between double arrow and "'post'"; 0 found
    |         | (WordPress.Arrays.ArrayDeclaration)
 31 | ERROR   | Expected 1 space before "=>"; 0 found
    |         | (WordPress.WhiteSpace.OperatorSpacing)
 31 | ERROR   | Expected 1 space after "=>"; 0 found
    |         | (WordPress.WhiteSpace.OperatorSpacing)
--------------------------------------------------------------------------------
....
```

You'll see the line number and number of ERRORs we need to return in `getErrorList` method.
In line #31 there are two ERRORs belong to `WordPress.WhiteSpace.OperatorSpacing` sniff and
it MUST not included in `ArrayDeclarationUnitTest` (that's why we only return 2 errros for line #31).
Also there's `getWarningList` method in unit test class that returns an array of line numbers
indicating WARNINGs.

## Sniff Code Standards

The sniffs for WPCS should be written such that they pass the `WordPress-Core` code standards.

