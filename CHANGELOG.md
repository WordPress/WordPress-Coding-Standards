# Change Log for WordPress Coding Standards

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com/).

## [Unreleased]

_Nothing yet._

## [0.13.1] - 2017-08-07

### Fixed
- Fatal error when using PHPCS 3.x with the `installed_paths` config variable set via the ruleset.

## [0.13.0] - 2017-08-03

### Added
- Support for PHP CodeSniffer 3.0.2+. The minimum required PHPCS version (2.9.0) stays the same.
- Support for the PHPCS 3 `--ignore-annotations` command line option. If you pass this option, both PHPCS native `@ignore ...` annotations as well as the WPCS specific [whitelist flags](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Whitelisting-code-which-flags-errors) will be ignored.

### Changed
- The minimum required PHP version is now 5.3 when used in combination with PHPCS 2.x and PHP 5.4 when used in combination with PHPCS 3.x.
- The way the unit tests can be run is now slightly different for PHPCS 2.x versus 3.x. For more details, please refer to the updated information in the [Contributing Guidelines](CONTRIBUTING.md).
- Release archives will no longer contain the unit tests and other typical development files. You can still get these by using Composer with `--prefer-source` or by checking out a git clone of the repository.
- Various textual improvements to the Readme.
- Various textual improvements to the Contributing Guidelines.
- Minor internal changes.

### Removed
- The `WordPress.Arrays.ArrayDeclaration` sniff has been deprecated. The last remaining checks this sniff contained have been moved to the `WordPress.Arrays.ArrayDeclarationSpacing` sniff.
- Work-arounds which were in place to support PHP 5.2.

### Fixed
- A minor bug where the auto-fixer could accidentally remove a comment near an array opener.


## [0.12.0] - 2017-07-21

### Added
- A default file encoding setting to the `WordPress-Core` ruleset. All files sniffed will now be regarded as `utf-8` by default.
- `WordPress.Arrays.ArrayIndentation` sniff to the `WordPress-Core` ruleset to verify - and auto-fix - the indentation of array items and the array closer for multi-line arrays. This replaces the (partial) indentation fixing contained within the `WordPress.Array.ArrayDeclarationSpacing` sniff.
- `WordPress.Arrays.CommaAfterArrayItem` sniff to the `WordPress-Core` ruleset to enforce that each array item is followed by a comma - except for the last item in a single-line array - and checks the spacing around the comma. This replaces (and improves) the checks which were previously included in the `WordPress.Arrays.ArrayDeclaration` sniff which were causing incorrect fixes and fixer conflicts.
- `WordPress.Functions.FunctionCallSignatureNoParams` sniff to the `WordPress-Core` ruleset to verify that function calls without parameters do not have any whitespace between the parentheses.
- `WordPress.WhiteSpace.DisallowInlineTabs` to the `WordPress-Core` ruleset to verify - and auto-fix - that spaces are used for mid-line alignment.
- `WordPress.WP.CapitalPDangit` sniff to the `WordPress-Core` ruleset to - where relevant - verify that `WordPress` is spelled correctly. For misspellings in text strings and comment text, the sniff can auto-fix violations.
- `Squiz.Classes.SelfMemberReference` whitespace related checks to the `WordPress-Core` ruleset and the additional check for using `self` rather than a FQN to the `WordPress-Extra` ruleset.
- `Squiz.PHP.EmbeddedPhp` sniff to the `WordPress-Core` ruleset to check PHP code embedded within HTML blocks.
- `PSR2.ControlStructures.SwitchDeclaration` to the `WordPress-Core` ruleset to check for the correct layout of `switch` control structures.
- `WordPress.Classes.ClassInstantion` sniff to the `WordPress-Extra` ruleset to detect - and auto-fix - missing parentheses on object instantiation and superfluous whitespace in PHP and JS files. The sniff will also detect `new` being assigned by reference.
- `WordPress.CodeAnalysis.EmptyStatement` sniff to the `WordPress-Extra` ruleset to detect - and auto-fix - superfluous semi-colons and empty PHP open-close tag combinations.
- `WordPress.NamingConventions.PrefixAllGlobals` sniff to the `WordPress-Extra` ruleset to verify that all functions, classes, interfaces, traits, variables, constants and hook names which are declared/defined in the global namespace are prefixed with one of the prefixes provided via a custom property or via the command line.
    To activate this sniff, [one or more allowed prefixes should be provided to the sniff](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#naming-conventions-prefix-everything-in-the-global-namespace). This can be done using a custom ruleset or via the command line.
    PHP superglobals and WP global variables are exempt from variable name prefixing. Deprecated hook names will also be disregarded when non-prefixed. Back-fills for known native PHP functionality is also accounted for.
    For verified exceptions, [unprefixed code can be whitelisted](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Whitelisting-code-which-flags-errors#non-prefixed-functionclassvariableconstant-in-the-global-namespace).
    Code in unit test files is automatically exempt from this sniff.
- `WordPress.WP.DeprecatedClasses` sniff to the `WordPress-Extra` ruleset to detect usage of deprecated WordPress classes.
- `WordPress.WP.DeprecatedParameters` sniff to the `WordPress-Extra` ruleset to detect deprecated parameters being passed to WordPress functions with a value other than the expected default.
- The `sanitize_textarea_field()` function to the `sanitizingFunctions` list used by the `WordPress.CSRF.NonceVerification`, `WordPress.VIP.ValidatedSanitizedInput` and `WordPress.XSS.EscapeOutput` sniffs.
- The `find_array_open_closer()` utility method to the `WordPress_Sniff` class.
- Information about setting `installed_paths` using a custom ruleset to the Readme.
- Additional support links to the `composer.json` file.
- Support for Composer PHPCS plugins which sort out the `installed_paths` setting.
- Linting and code-style check of the XML ruleset files provided by WPCS.

### Changed
- The minimum required PHP_CodeSniffer version to 2.9.0 (was 2.8.1). **Take note**: PHPCS 3.x is not (yet) supported. The next release is expected to fix that.
- Improved support for detecting issues in code using heredoc and/or nowdoc syntax.
- Improved sniff efficiency, precision and performance for a number of sniffs.
- Updated a few sniffs to take advantage of new features and fixes which are included in PHP_CodeSniffer 2.9.0.
- `WordPress.Files.Filename`: The "file name mirrors the class name prefixed with 'class'" check for PHP files containing a class will no longer be applied to typical unit test classes, i.e. for classes which extend `WP_UnitTestCase`, `PHPUnit_Framework_TestCase` and `PHPUnit\Framework\TestCase`. Additional test case base classes can be passed to the sniff using the new [`custom_test_class_whitelist` property](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#custom-unit-test-classes).
- The `WordPress.Files.FileName` sniff allows now for more theme-specific template hierarchy based file name exceptions.
- The whitelist flag for the `WordPress.VIP.SlowQuery` sniff was `tax_query` which was unintuitive. This has now been changed to `slow query` to be in line with other whitelist flags.
- The `WordPress.WhiteSpace.OperatorSpacing` sniff will now ignore operator spacing within `declare()` statements.
- The `WordPress.WhiteSpace.OperatorSpacing` sniff now extends the upstream `Squiz.WhiteSpace.OperatorSpacing` sniff for improved results and will now also examine the spacing around ternary operators and logical (`&&`, `||`) operators.
- The `WordPress.WP.DeprecatedFunctions` sniff will now detect functions deprecated in WP 4.7 and 4.8. Additionally, a number of other deprecated functions which were previously not being detected have been added to the sniff and for a number of functions the "alternative" for the deprecated function has been added/improved.
- The `WordPress.XSS.EscapeOutput` sniff will now also detect unescaped output when the short open echo tags `<?=` are used.
- Updated the list of WP globals which is used by both the `WordPress.Variables.GlobalVariables` and the `WordPress.NamingConventions.PrefixAllGlobals` sniffs.
- Updated the information on using a custom ruleset and associated naming conventions in the Readme.
- Updated the [custom ruleset example](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/blob/develop/phpcs.xml.dist.sample) to provide a better starting point and renamed the file to follow current PHPCS best practices.
- Various inline documentation improvements.
- Updated the link to the PHPStorm documentation in the Readme.
- Various textual improvements to the Readme.
- Minor improvements to the build script.

### Removed
- `Squiz.Commenting.LongConditionClosingComment` sniff from the `WordPress-Core` ruleset. This rule has been removed from the WP Coding Standards handbook.
- The exclusion of the `Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace` error from the `WordPress-Core` ruleset.
- The exclusion of the `PEAR.Functions.FunctionCallSignature.ContentAfterOpenBracket` and `PEAR.Functions.FunctionCallSignature.CloseBracketLine` error from the `WordPress-Core` ruleset when used in combination with the fixer, i.e. `phpcbf`. The exclusions remain in place for `phpcs` runs.
- `wp_get_post_terms()`, `wp_get_post_categories()`, `wp_get_post_tags()` and `wp_get_object_terms()` from the `WordPress.VIP.RestrictedFunctions` sniff as these functions are now cached natively since WP 4.7.

### Fixed
- The `WordPress.Array.ArrayDeclarationSpacing` could be overeager when fixing associative arrays to be multi-line. Non-associative single-line arrays which contained a nested associative array would also be auto-fixed by the sniff, while only the nested associated array should be fixed.
- The `WordPress.Files.FileName` sniff did not play nice with IDEs passing a filename to PHPCS via `--stdin-path=`.
- The `WordPress.Files.FileName` sniff was being triggered on code passed via `stdin` where there is no file name to examine.
- The `WordPress.PHP.YodaConditions` sniff would give a false positive for the result of a condition being assigned to a variable.
- The `WordPress.VIP.RestrictedVariables` sniff was potentially underreporting issues when the variables being restricted were a combination of variables, object properties and array members.
- The auto-fixer in the `WordPress.WhiteSpace.ControlStructureSpacing` sniff which deals with "blank line after control structure" issues could cause comments at the end of control structures to be removed.
- The `WordPress.WP.DeprecatedFunctions` sniff was reporting the wrong WP version for the deprecation of a number of functions.
- The `WordPress.WP.EnqueuedResources` sniff would potentially underreport issues in certain circumstances.
- The `WordPress.XSS.EscapeOutput` sniff will no now longer report issues when it encounters a `__DIR__`, `(unset)` cast or a floating point number, and will correctly disregard more arithmetic operators when deciding whether to report an issue or not.
- The [whitelisting of errors using flags](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Whitelisting-code-which-flags-errors) was sometimes a bit too eager and could accidentally whitelist code which was not intended to be whitelisted.
- Various (potential) `Undefined variable`, `Undefined index` and `Undefined offset` notices.
- Grammer in one of the `WordPress.WP.I18n` error messages.


## [0.11.0] - 2017-03-20

### Important notes for end-users:

If you use the WordPress Coding Standards with a custom ruleset, please be aware that some of the checks have been moved between sniffs and that the naming of a number of error codes has changed.
If you exclude some sniffs or error codes, you may have to update your custom ruleset to be compatible with WPCS 0.11.0.

Additionally, to make it easier for you to customize your ruleset, two new wiki pages have been published with information on the properties you can adjust from your ruleset:
* [WPCS customizable sniff properties](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties)
* [PHPCS customizable sniff properties](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Customisable-Sniff-Properties)

For more detailed information about the changed sniff names and error codes, please refer to PR [#633](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/pull/633) and PR [#814](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/pull/814).

### Important notes for sniff developers:

If you maintain or develop sniffs based upon the WordPress Coding Standards, most notably, if you use methods and properties from the `WordPress_Sniff` class, extend one of the abstract sniff classes WPCS provides or extend other sniffs from WPCS to use their properties, please be aware that this release contains significant changes which will, more likely than not, affect your sniffs.

Please read this changelog carefully to understand how this will affect you.
For more detailed information on the most significant changes, please refer to PR [#795](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/pull/795), PR [#833](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/pull/833) and PR [#841](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/pull/841).
You are also encouraged to check the file history of any WPCS classes you extend.

### Added
- `WordPress.WP.DeprecatedFunctions` sniff to the `WordPress-Extra` ruleset to check for usage of deprecated WP version and show errors/warnings depending on a `minimum_supported_version` which [can be passed to the sniff from a custom ruleset](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#minimum-wp-version-to-check-for-usage-of-deprecated-functions-and-function-parameters). The default value for the `minimum_supported_version` property is three versions before the current WP version.
- `WordPress.WP.I18n`: ability to check for missing _translators comments_ when a I18n function call contains translatable text strings containing placeholders. This check will also verify that the _translators comment_ is correctly placed in the code and uses the correct comment type for optimal compatibility with the various tools available to create `.pot` files.
- `WordPress.WP.I18n`: ability to pass the `text_domain` to check for [from the command line](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#setting-text_domain-from-the-command-line).
- `WordPress.Arrays.ArrayDeclarationSpacing`: check + fixer for single line associative arrays. The [handbook](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#indentation) states that these should always be multi-line.
- `WordPress.Files.FileName`: verification that files containing a class reflect this in the file name as per the core guidelines. This particular check can be disabled in a custom ruleset by setting the new [`strict_class_file_names` property](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#disregard-class-file-name-rules).
- `WordPress.Files.FileName`: verification that files in `/wp-includes/` containing template tags - annotated with `@subpackage Template` in the file header - use the `-template` suffix.
- `WordPress.Files.FileName`: [`is_theme` property](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#themes-allow-filename-exceptions) which can be set in a custom ruleset. This property can be used to indicate that the project being checked is a theme and will allow for a predefined theme hierarchy based set of exceptions to the file name rules.
- `WordPress.VIP.AdminBarRemoval`: check for hiding the admin bar using CSS.
- `WordPress.VIP.AdminBarRemoval`: customizable [`remove_only` property](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#admin-bar-visibility-manipulations) to toggle whether to error of all manipulation of the visibility of the admin bar or to execute more thorough checking for removal only.
- `WordPress.WhiteSpace.ControlStructureSpacing`: support for checking the whitespace in `try`/`catch` constructs.
- `WordPress.WhiteSpace.ControlStructureSpacing`: check that the space after the open parenthesis and before the closing parenthesis of control structures and functions is *exactly* one space. Includes auto-fixer.
- `WordPress.WhiteSpace.CastStructureSpacing`: ability to automatically fix errors thrown by the sniff.
- `WordPress.VIP.SessionFunctionsUsage`: detection of the `session_abort()`, `session_create_id()`, `session_gc()` and `session_reset()` functions.
- `WordPress.CSRF.NonceVerification`: ability to pass [custom sanitization functions](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#custom-input-sanitization-functions) to the sniff.
- The `get_the_ID()` function to the `autoEscapedFunctions` list used by the `WordPress.XSS.EscapeOutput` sniff.
- The `wp_strip_all_tags()`, `sanitize_hex_color_no_hash()` and `sanitize_hex_color()` functions to the `sanitizingFunctions` list used by the `WordPress.CSRF.NonceVerification`, `WordPress.VIP.ValidatedSanitizedInput` and `WordPress.XSS.EscapeOutput` sniffs.
- The `floatval()` function to the `escapingFunctions`, `sanitizingFunctions`, `unslashingSanitizingFunctions`, `SQLEscapingFunctions` lists used by the `WordPress.CSRF.NonceVerification`, `WordPress.VIP.ValidatedSanitizedInput`, `WordPress.XSS.EscapeOutput` and `WordPress.WP.PreparedSQL` sniffs.
- The table name based `clean_*_cache()` functions to the `cacheDeleteFunctions` list used by the `WordPress.VIP.DirectDatabaseQuery` sniff.
- Abstract `AbstractFunctionParameter` parent class to allow for examining parameters passed in function calls.
- A number of utility functions to the `WordPress_Sniff` class: `strip_quotes()`, `addMessage()`, `addFixableMessage()`, `string_to_errorcode()`, `does_function_call_have_parameters()`, `get_function_call_parameter_count()`, `get_function_call_parameters()`, `get_function_call_parameter()`, `has_html_open_tag()`.
- `Squiz.Commenting.LongConditionClosingComment`, `Squiz.WhiteSpace.CastSpacing`, `Generic.Formatting.DisallowMultipleStatements` to the `WordPress-Core` ruleset.
- `Squiz.PHP.NonExecutableCode`, `Squiz.Operators.IncrementDecrementUsage`, `Squiz.Operators.ValidLogicalOperators`, `Squiz.Functions.FunctionDuplicateArgument`, `Generic.PHP.BacktickOperator`, `Squiz.PHP.DisallowSizeFunctionsInLoops` to the `WordPress-Extra` ruleset.
- Numerous additional unit tests covering the correct handling of properties overruled via a custom ruleset by various sniffs.
- Instructions on how to use WPCS with Visual Studio to the Readme.
- Section on how to use WPCS with CI Tools to the Readme, initially covering integration with Travis CI.
- Section on considerations when writing sniffs for WPCS to `Contributing.md`.

### Changed
- The minimum required PHP version to 5.2 (was 5.1).
- The minimum required PHP_CodeSniffer version to 2.8.1 (was 2.6).
- Improved support for detecting issues in code using closures (anonymous functions), short array syntax and anonymous classes.
- Improved sniff efficiency and performance for a number of sniffs.
- The discouraged/restricted functions sniffs have been reorganized and made more modular.
    * The new `WordPress.PHP.DevelopmentFunctions` sniff now contains the checks related to PHP functions typically used during development which are discouraged in production code.
    * The new `WordPress.PHP.DiscouragedPHPFunctions` sniff now contains checks related to various PHP functions, use of which is discouraged for various reasons.
    * The new `WordPress.WP.AlternativeFunctions` sniff contains the checks related to PHP functions for which WP offers an alternative which should be used instead.
    * The new `WordPress.WP.DiscouragedFunctions` sniff contains checks related to various WP functions, use of which is discouraged for various reasons.
    * A number of checks contained in the `WordPress.VIP.RestrictedFunctions` sniff have been moved to other sniffs.
    * The `WordPress.PHP.DiscouragedFunctions` sniff has been deprecated and is no longer used. The checks which were previously contained herein have been moved to other sniffs.
    * The reorganized sniffs also detect a number of additional functions which were previously ignored by these sniffs. For more detail, please refer to the [summary of the PR](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/pull/633#issuecomment-269693016) and to [PR #759](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/pull/759).
- The error codes for these sniffs as well as for `WordPress.DB.RestrictedClasses`, `WordPress.DB.RestrictedFunctions`, `WordPress.Functions.DontExtract`, `WordPress.PHP.POSIXFunctions` and a number of the `VIP` sniffs have changed. They were previously based on function group names and will now be based on function group name in combination with the identified function name. Complete function groups can still be silenced by using the [`exclude` property](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#excluding-a-group-of-checks) in a custom ruleset.
- `WordPress.NamingConventions.ValidVariableName`: The `customVariablesWhitelist` property which could be passed from the ruleset has been renamed to [`customPropertiesWhitelist`](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#mixed-case-property-name-exceptions) as it is only usable to whitelist class properties.
- `WordPress.WP.I18n`: now allows for an array of text domain names to be passed to the `text_domain` property from a custom ruleset.
- `WordPress.WhiteSpace.CastStructureSpacing`: the error level for the checks in this sniff has been raised from `warning` to `error`.
- `WordPress.Variables.GlobalVariables`: will no longer throw errors if the global variable override is done from within a test method. Whether something is considered a "test method" is based on whether the method is in a class which extends a predefined set of known unit test classes. This list can be enhanced by setting the [`custom_test_class_whitelist` property](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#global-variable-overloads-in-unit-tests) in your ruleset.
- The `WordPress.Arrays.ArrayDeclaration` sniff has been split into two sniffs: `WordPress.Arrays.ArrayDeclaration` and `WordPress.Arrays.ArrayDeclarationSpacing` for better compatibility with PHPCS upstream.
- The `WordPress.Arrays.ArrayDeclaration` sniff has been synced with the PHPCS upstream version to get the benefit of some bug fixes and improvements which had been made upstream since the sniff was originally copied over.
- The `WordPress.VIP.FileSystemWritesDisallow`, `WordPress.VIP.TimezoneChange` and `WordPress.VIP.SessionFunctionsUsage` sniffs now extend the `WordPress_AbstractFunctionRestrictionsSniff`.
- Property handling of custom properties set via a custom ruleset where the property is expected to be set in array format (`type="array"`) has been made more lenient and will now also handle properties passed as a comma delimited lists correctly. This affects all customizable properties which expect array format.
- Moved `Squiz.PHP.DisallowMultipleAssignments` from the `WordPress-Extra` to the `WordPress-Core` ruleset.
- Replaced the `WordPress.Classes.ValidClassName`, `WordPress.PHP.DisallowAlternativePHPTags` and the `WordPress.Classes.ClassOpeningStatement` sniffs with the existing `PEAR.NamingConventions.ValidClassName` and the new upstream `Generic.PHP.DisallowAlternativePHPTags` and `Generic.Classes.OpeningBraceSameLine` sniffs in the `WordPress-Core` ruleset.
- Use the upstream `Squiz.PHP.Eval` sniff for detecting the use of `eval()` instead of a WPCS native implementation.
- Made the `Generic.WhiteSpace.ScopeIndent` sniff in the `WordPress-Core` ruleset more lenient to allow for different indentation in inline HTML, heredoc and nowdoc structures.
- Made the `Generic.Strings.UnnecessaryStringConcat` sniff in the `WordPress-Extra` ruleset more lenient to allow for multi-line string concatenation.
- All sniffs are now also being tested against PHP 7.1 for consistent sniff results.
- The requirements for running the sniffs have been made more explicit in the readme.
- Updated composer installation instructions in the readme.
- Updated information about the rulesets in the readme and moved the information up to make it easier to find.
- Improved the information about running the unit tests in `Contributing.md`.
- Improved the inline documentation of the rulesets.
- Various other code quality and code consistency improvements under the hood, including refactoring of some of the abstract sniff classes, closer coupling of the child classes to the `WordPress_Sniff` parent class and changes to the visibility and staticness of properties for a large number of sniffs.

### Removed
- Warnings thrown by individual sniffs about parse errors they encounter. This is left up to the `Generic.PHP.Syntax` sniff which is included in the `WordPress-Extra` ruleset.
- The `post_class()` function from the `autoEscapedFunctions` list used by the `WordPress.XSS.EscapeOutput` sniff.
- The `Generic.Files.LowercasedFilename` sniff from the `WordPress-Core` ruleset in favour of the improved `WordPress.Files.FileName` sniff to prevent duplicate messages being thrown.
- Some temporary work-arounds for changes which were pulled and merged into PHPCS upstream.

### Fixed
- `WordPress.Variables.GlobalVariables`: **_All known bugs have been fixed. If you'd previously disabled the sniff in your custom ruleset because of these bugs, it should be fine to re-enable it now._**
    * Assignments to global variables using other assignment operators than the `=` operator were not detected.
    * If a `global ...;` statement was detected, the whole file would be checked for the variables which were made global, not just the code after the global statement.
    * If a `global ...;` statement was detected, the whole file would be checked for the variables which were made global, including code contained within a function/closure/class scope where there is no access to the global variable.
    * If a `global ...;` statement was detected within a function call or closure, the whole file would be checked for the variables which were made global, not just the code within the function or closure.
    * If a `global ...;` statement was detected and an assignment was made to a static class variable using the same name as one of the variables made global, an error would incorrectly be thrown.
    * An override of a protected global via `$GLOBALS` in combination with simple string concatenation obfuscation was not being detected.
- `WordPress.WP.I18n`: all reported bugs have been fixed.
    * A superfluous `UnorderedPlaceholders` error was being thrown when `%%` (a literal % sign) was encountered in a string.
    * The sniff would sometimes erroneously trigger errors when a literal `%` was found in a translatable string without placeholders.
    * Not all type of placeholders were being recognized.
    * No warning was being thrown when encountering a mix of ordered and unordered placeholders.
    * The fixer for unordered placeholders was erroneously replacing all placeholders as if they were the first one.
    * The fixer for unordered placeholders could cause faulty replacements in double quoted strings.
    * Compatibility with PHP nightly / PHP 7.2.
- `WordPress.WhiteSpace.ControlStructureSpacing`: synced in fixes from the upstream version.
    * The fixer would bork on control structures which contained only a single empty line.
    * The sniff did not check the spacing used for `do {} while ()` control structures.
    * Conditional function declarations could cause an infinite loop when using the fixer.
- `WordPress.VIP.PluginMenuSlug`: the sniff would potentially incorrectly process method calls and namespaced functions with the same function name as the targeted WordPress native functions.
- `WordPress.VIP.CronInterval`: the native WP time constants were not recognized leading to false positives.
- `WordPress.VIP.CronInterval`: the finding of the referenced function declaration has been made more accurate.
- `WordPress.PHP.YodaConditions`: minor clarification of the error message.
- `WordPress.NamingConventions.ValidVariableName`: now allows for a predefined list of known mixed case global variables coming from WordPress itself reducing false positives.
- The `unslashingSanitizingFunctions` list was not consistently taken into account when verifying whether a variable was sanitized for the `WordPress.VIP.ValidatedSanitizedInput` and `WordPress.CSRF.NonceVerification` sniffs.
- The passing of properties via the ruleset was buggy for a number of sniffs - most notably those sniffs using custom properties in array format - and could lead to unintended bleed-through between sniffs.
- Various (potential) `Undefined variable`, `Undefined index` and `Undefined offset` notices.
- An issue with placeholder replacement not taking place in some error messages.
- A (potential) issue which could play up when sniffs examined text strings which contained quotes.


## [0.10.0] - 2016-08-29

### Added
- `WordPress.WP.I18n` sniff to the `WordPress-Core` ruleset to flag dynamic translatable strings and textdomains.
- `WordPress.PHP.DisallowAlternativePHPTags` sniff to the `WordPress-Core` ruleset to flag - and fix - ASP and `<script>` PHP open tags.
- `WordPress.Classes.ClassOpeningStatement` sniff to the `WordPress-Core` ruleset to flag - and fix - class opening brace placement.
- `WordPress.NamingConventions.ValidHookName` sniff to the `WordPress-Core` ruleset to flag filter and action hooks which don't comply with the guideline of lowercase letters and underscores. For maintaining backward-compatibility of hook names an `additionalWordDelimiters` property can be added via a custom ruleset.
- `WordPress.Functions.DontExtract` sniff to the `WordPress-Core` ruleset to flag usage of the `extract()` function.
- `WordPress.PHP.POSIXFunctions` sniff to the `WordPress-Core` ruleset to flag usage of regex functions from the POSIX PHP extension which was deprecated since PHP 5.3 and removed in PHP 7.
- `WordPress.DB.RestrictedFunctions` and `WordPress.DB.RestrictedClasses` sniffs to the `WordPress-Core` ruleset to flag usage of direct database calls using PHP functions and classes rather than the WP functions for the same.
- Abstract `AbstractClassRestrictions` parent class to allow for easier sniffing for usage of specific classes.
- `Squiz.Strings.ConcatenationSpacing`, `PSR2.ControlStructures.ElseIfDeclaration`, `PSR2.Files.ClosingTag`, `Generic.NamingConventions.UpperCaseConstantName` to the `WordPress-Core` ruleset.
- Ability to add arbitrary variables to the whitelist via a custom ruleset property for the `WordPress.NamingConventions.ValidVariableName` sniff.
- Ability to use a whitelist comment for tax queries for the `WordPress.VIP.SlowDBQuery` sniff.
- Instructions on how to use WPCS with Atom and SublimeLinter to the Readme.
- Reference to the [wiki](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki) to the Readme.
- Recommendation to also use the [PHPCompatibility](https://github.com/wimg/PHPCompatibility) ruleset to the Readme.

### Changed
- The minimum required PHP_CodeSniffer version to 2.6.0.
- Moved the `WordPress.WP.PreparedSQL` sniff from `WordPress-Extra` to `WordPress-Core`.
- `WordPress.PHP.StrictInArray` will now also flag non-strict usage of `array_keys()` and `array_search()`.
- Added `_deprecated_constructor()` and `_deprecated_hook()` to the list of printing functions.
- Added numerous additional functions to sniff for to the `WordPress.VIP.RestrictedFunctions` sniff as per the VIP guidelines.
- Upped the `posts_per_page` limit from 50 to 100 in `WordPress.VIP.PostsPerPage` sniff as per the VIP guidelines.
- Added `cat_ID` to the whitelisted exceptions for the `WordPress.NamingConventions.ValidVariableName` sniff.
- Added `__debugInfo` to the magic method whitelist for class methods starting with double underscore in the `WordPress.NamingConventions.ValidFunctionName` sniff.
- An error will now also be thrown for non-magic _functions_ using a double underscore prefix - `WordPress.NamingConventions.ValidFunctionName` sniff.
- The `WordPress.Arrays.ArrayAssignmentRestrictions`, `WordPress.Functions.FunctionRestrictions`, `WordPress.Variables.VariableRestrictions` sniffs weren't in actual fact sniffs, but parent classes for child sniffs. These have now all been turned into proper abstract parent classes and moved to the main `WordPress` directory.
- The array provided to `AbstractFunctionRestrictions` can now take a `whitelist` key to whitelist select functions when blocking a group of functions by function prefix.
- Updated installation instructions in the readme.
- The `WordPress-Core` ruleset is now ordered according to the handbook
- The WPCS code base itself now complies with the WordPress-Core, -Extra and -Docs coding standards.
- Various other code quality and code consistency improvements under the hood.

### Removed
- `Squiz.Functions.FunctionDeclarationArgumentSpacing.SpacingBeforeClose` from the `WordPress-Core` standard (was causing duplicate messages for the same issue).
- `Squiz.Commenting.FunctionComment.ScalarTypeHintMissing`, `Squiz.Commenting.InlineComment.NotCapital` from the `WordPress-Docs` standard.
- Removed the sniffing for `get_pages()` from the `WordPress.VIP.RestrictedFunctions` sniff as per the VIP guidelines.
- Removed the sniffing for `extract()` from the `WordPress.VIP.RestrictedFunctions` sniff as it's now covered in a separate sniff.
- Removed the sniffing for the POSIX functions from the `WordPress.PHP.DiscouragedFunctions` sniff as it's now covered in a separate sniff.

### Fixed
- Error message precision for the `WordPress.NamingConventions.ValidVariableName` sniff.
- Bug in the `WordPress.WhiteSpace.ControlStructureSpacing.BlankLineAfterEnd` sniff which was incorrectly being triggered on last method of class.
- Function name sniffs based on the `AbstractFunctionRestrictions` parent class will now do a case-insensitive function name comparison.
- Function name sniffs in the `WordPress.PHP.DiscouragedFunctions` sniff will now do a case-insensitive function name comparison.
- Whitelist comments directly followed by a PHP closing tag were not being recognized.
- Some PHP Magic constants were not recognized by the `WordPress.XSS.EscapeOutput` sniff.
- An error message suggesting camel caps rather than the intended snake case format in the `WordPress.NamingConventions.ValidFunctionName` sniff.
- `WordPress.WhiteSpace.ControlStructureSpacing` should no longer throw error notices during live code review.
- Errors will be no longer be thrown for methods not complying with the naming conventions when the class extends a parent class or implements an interface - `WordPress.NamingConventions.ValidFunctionName` sniff.


## [0.9.0] - 2016-02-01

### Added
- `count()` to the list of auto-escaped functions.
- `Squiz.PHP.CommentedOutCode` sniff to `WordPress-VIP` ruleset.
- Support for PHP 5.2.
- `attachment_url_to_postid()` and `parse_url()` to the restricted functions for `WordPress-VIP`.
- `WordPress.VIP.OrderByRand` sniff.
- `WordPress.PHP.StrictInArray` sniff for `WordPress-VIP` and `WordPress-Extra`.
- `get_tag_link()`, `get_category_link()`, `get_cat_ID()`, `url_to_post_id()`, `attachment_url_to_postid()`
`get_posts()`, `wp_get_recent_posts()`, `get_pages()`, `get_children()`, `wp_get_post_terms()`
`wp_get_post_categories()`, `wp_get_post_tags()`, `wp_get_object_terms()`, `term_exists()`,
`count_user_posts()`, `wp_old_slug_redirect()`, `get_adjacent_post()`, `get_previous_post()`,
`get_next_post()` to uncached functions in `WordPress.VIP.RestrictedFunctions` sniff.
- `wp_handle_upload()` and `array_key_exists()` to the list of sanitizing functions.
- Checking for object properties in `WordPress.PHP.YodaConditions` sniff.
- `WordPress.NamingConventions.ValidVariableName` sniff.
- Flagging of function calls incorporated into database queries in `WordPress.WP.PreparedSQL`.
- Recognition of escaping and auto-escaped functions in `WordPress.WP.PreparedSQL`.
- `true`, `false`, and `null` to the tokens ignored in `WordPress.XSS.EscapeOutput`.

### Fixed
- Incorrect ternary detection in `WordPress.XSS.EscapeOutput` sniff.
- False positives when detecting variables interpolated into strings in the
`WordPress.WP.PreparedSQL` and `WordPress.VIP.ValidatedSanitizedInput` sniffs.
- False positives in `WordPress.PHP.YodaConditions` when the variable is being casted.
- `$wpdb` properties being flagged in `WordPress.WP.PreparedSQL` sniff.
- False positive in `WordPress.PHP.YodaConditions` when the a string is on the left side of the
comparison.

## [0.8.0] - 2015-10-02

### Added
- `implode()` and `join()` to the list of formatting functions in the `WordPress.XSS.EscapeOutput`
sniff. This is useful when you need to have HTML in the `$glue` parameter.
- Support in the `WordPress.XSS.EscapeOutput` sniff for escaping an array of values
using `array_map()`. (Otherwise the support for `implode()` isn't of much use :)
- Docs for running WPCS in Sublime Text.
- `nl2br()` to the list of formatting functions.
- `wp_dropdown_pages()` to the list of printing functions.
- Error codes to all error/warning messages.
- `WordPress.WP.PreparedSQL` sniff for flagging unprepared SQL queries.

### Removed
- Sniffing for the number of spaces before a closure's opening parenthesis from the
default configuration of the `WordPress.WhiteSpace.ControlStructureSpacing` sniff. It
can be re-enabled per-project as desired.

### Fixed
- The `WordPress.XSS.EscapeOutput` sniff giving error messages with the closing
parenthesis in them instead of the offending function's name.

## [0.7.1] - 2015-08-31

### Changed
- The default number of spaces before a closure's opening parenthesis from 1 to 0.

## [0.7.0] - 2015-08-30

### Added
- Automatic error fixing to the `WordPress.Arrays.ArrayKeySpacingRestrictions` sniff.
- Functions and closures to the control structures checked by the `WordPress.WhiteSpace.ControlStructureSpacing`
sniff.
- Sniffing and fixing for extra spacing in the `WordPress.WhiteSpace.ControlStructureSpacing`
sniff. (Previously it only checked for insufficient spacing.)
- `.twig` files to the default ignored files.
- `esc_url_raw()` and `hash_equals()` to the list of sanitizing functions.
- `intval()` and `boolval()` to list of unslashing functions.
- `do_shortcode()` to the list of auto-escaped functions.

### Removed
- `WordPress.Functions.FunctionDeclarationArgumentSpacing` in favor of the upstream
sniff `Squiz.Functions.FunctionDeclarationArgumentSpacing`.

### Fixed
- Reference to incorrect issue in the inline docs of the `WordPress.VIP.SessionVariableUsage`
sniff.
- `WordPress.XSS.EscapeOutput` sniff incorrectly handling ternary conditions in
`echo` statements without parentheses in some cases.

## [0.6.0] - 2015-06-30

### Added
- Support for `wp_cache_add()` and `wp_cache_delete()`, as well as custom cache 
functions,in the `WordPress.VIP.DirectDatabaseQuery` sniff.

### Removed
- `WordPress.Functions.FunctionRestrictions` and `WordPress.Variables.VariableRestrictions` 
from the `WordPress-VIP` standard, since they are just parents for other sniffs.

## [0.5.0] - 2015-06-01

### Added
- `WordPress.CSRF.NonceVerification` sniff to flag form processing without nonce verification.
- `in_array()` and `is_array()` to the list of sanitizing functions.
- Support for automatic error fixing to the `WordPress.Arrays.ArrayDeclaration` sniff.
- `WordPress.PHP.StrictComparisions` to the `WordPress-VIP` and `WordPress-Extra` rulesets.
- `WordPress-Docs` ruleset to sniff for proper commenting.
- `Generic.PHP.LowerCaseKeyword`, `Generic.Files.EndFileNewline`, `Generic.Files.LowercasedFilename`, 
`Generic.Formatting.SpaceAfterCast`, and `Generic.Functions.OpeningFunctionBraceKernighanRitchie` to the `WordPress-Core` ruleset.
- `Generic.PHP.DeprecatedFunctions`, `Generic.PHP.ForbiddenFunctions`, `Generic.Functions.CallTimePassByReference`, 
`Generic.Formatting.DisallowMultipleStatements`, `Generic.CodeAnalysis.EmptyStatement`, 
`Generic.CodeAnalysis.ForLoopShouldBeWhileLoop`, `Generic.CodeAnalysis.ForLoopWithTestFunctionCall`, 
`Generic.CodeAnalysis.JumbledIncrementer`, `Generic.CodeAnalysis.UnconditionalIfStatement`, 
`Generic.CodeAnalysis.UnnecessaryFinalModifier`, `Generic.CodeAnalysis.UselessOverridingMethod`, 
`Generic.Classes.DuplicateClassName`, and `Generic.Strings.UnnecessaryStringConcat` to the `WordPress-Extra` ruleset.
- Error for missing use of `wp_unslash()` on superglobal data to the `WordPress.VIP.ValidatedSanitizedInput` sniff. 

### Changed
- The `WordPress.VIP.ValidatedSanitizedInput` sniff to require sanitization of input even when it is being directly escaped and output.
- The minimum required PHP_CodeSniffer version to 2.2.0.
- The `WordPress.VIP.ValidatedSanitizedInput` and `WordPress.XSS.EscapeOutput` sniffs: 
the list of escaping functions was split from the list of sanitizing functions. The `customSanitizingFunctions` 
property has been moved to the `ValidatedSanitizedInput` sniff, and the `customEscapingFunctions`
property should now be used instead for the `EscapeOutput` sniff.
- The `WordPress.Arrays.ArrayDeclaration` sniff to give errors for `NoSpaceAfterOpenParenthesis`, `SpaceAfterArrayOpener`, and `SpaceAfterArrayCloser`, instead of warnings.
- The `WordPress.NamingConventions.ValidFunctionName` sniff to allow camelCase method names in classes that implement interfaces.

### Fixed
- The `WordPress.VIP.ValidatedSanitizedInput` sniff not reporting missing validation when reporting missing sanitization.
- The `WordPress.VIP.ValidatedSanitizedInput` sniff flagging superglobals as needing sanitization when they were only being used in a comparison using `if` or `switch`, etc.

## [0.4.0] - 2015-05-01

### Added
- Change log file.
- Handling for string-interpolated input variables in the `WordPress.VIP.ValidatedSanitizedInput` sniff.
- Errors for using uncached functions when cached equivalents exist.
- `space_before_colon` setting for the `WordPress.WhiteSpace.ControlStructureSpacing` sniff, for control structures using alternative syntax. Possible values: `'required'`, `'optional'`, `'forbidden'`.
- Support for `sanitization` whitelisting comments for the `WordPress.VIP.ValidatedSanitizedInput` sniff.
- Granular error/warning names for all errors and warnings.
- Handling for ternary conditions in the `WordPress.XSS.EscapeOutput` sniff.
- `die`, `exit`, `printf`, `vprintf`, `wp_die`, `_deprecated_argument`, `_deprecated_function`, `_deprecated_file`, `_doing_it_wrong`, `trigger_error`, and `user_error` to the list of printing functions in the `WordPress.XSS.EscapeOutput` sniff.
- `customPrintingFunctions` setting for the `WordPress.XSS.EscapeOutput` sniff.
- `rawurlencode()` and `wp_parse_id_list()` to the list of "sanitizing" functions in the `WordPress.XSS.EscapeOutput` sniff.
- `json_encode()` to the list of discouraged functions in the `WordPress.PHP.DiscouragedFunctions` sniff, in favor of `wp_json_encode()`.
- `vip_powered_wpcom()` to the list of auto-escaped functions in the `WordPress.XSS.EscapeOutput` sniff.
- `debug_print_backtrace()` and `var_export()` to the list of discouraged functions in the `WordPress.PHP.DiscouragedFunctions` sniff.
- Smart handling for formatting functions (`sprintf()` and `wp_sprintf()`) in the `WordPress.XSS.EscapeOutput` sniff.
- `WordPress.PHP.StrictComparisons` sniff.
- Correct handling of `array_map()` in the `WordPress.VIP.ValidatedSanitizedInput` sniff.
- `$_COOKIE` and `$_FILE` to the list of superglobals flagged by the `WordPress.VIP.ValidatedSanitizedInput` and `WordPress.VIP.SuperGlobalInputUsage` sniffs.
- `$_SERVER` to the list of superglobals flagged by the `WordPress.VIP.SuperGlobalInputUsage` sniff.
- `Squiz.ControlStructures.ControlSignature` sniff to the rulesets.

### Changed
- `WordPress.Arrays.ArrayKeySpacingRestrictions` sniff to give errors for `NoSpacesAroundArrayKeys` and `SpacesAroundArrayKeys` instead of just warnings.
- `WordPress.NamingConventions.ValidFunctionName` sniff to allow for camel caps method names in child classes.
- `WordPress.XSS.EscapeOutput` sniff to allow for integers (e.g. `echo 5` and `print( -1 )`).

### Removed
- Errors for mixed key/keyless array elements in the `WordPress.Arrays.ArrayDeclaration` sniff.
- BOM from `WordPress.WhiteSpace.OperatorSpacing` sniff file.
- `$content_width` from the list of non-overwritable globals in the `WordPress.Variables.GlobalVariables` sniff.
- `WordPress.Arrays.ArrayAssignmentRestrictions` sniff from the `WordPress-VIP` ruleset.

### Fixed
- Incorrect errors for `else` statements using alternative syntax.
- `WordPress.VIP.ValidatedSanitizedInput` sniff not always treating casting as sanitization.
- `WordPress.XSS.EscapeOutput` sniff flagging comments as needing to be escaped.
- `WordPress.XSS.EscapeOutput` sniff not sniffing comma-delimited `echo` arguments after encountering the first escaping function in the statement.
- `WordPress.PHP.YodaConditions` sniff not flagging comparisons to constants or function calls.
- `WordPress.Arrays.ArrayDeclaration` sniff not ignoring doc comments.
- Link to phpStorm instructions in `README.md`.
- Poor performance of the `WordPress.Arrays.ArrayAssignmentRestrictions` sniff.
- Poor performance of the `WordPress.Files.FileName` sniff.

## [0.3.0] - 2014-12-11

See the comparison for full list.

### Changed
- Use semantic version tags for releases.

## [2013-10-06]

See the comparison for full list.

## 2013-06-11

Initial tagged release.

[Unreleased]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/master...HEAD
[0.13.1]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.13.0...0.13.1
[0.13.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.12.0...0.13.0
[0.12.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.11.0...0.12.0
[0.11.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.10.0...0.11.0
[0.10.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.9.0...0.10.0
[0.9.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.8.0...0.9.0
[0.8.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.7.1...0.8.0
[0.7.1]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.7.0...0.7.1
[0.7.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.6.0...0.7.0
[0.6.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.5.0...0.6.0
[0.5.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.4.0...0.5.0
[0.4.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/2013-10-06...0.3.0
[2013-10-06]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/2013-06-11...2013-10-06
