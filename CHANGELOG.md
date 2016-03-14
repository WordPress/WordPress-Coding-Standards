# Change Log for WordPress Coding Standards

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com/).

## [Unreleased]

### Added
- Sniff to flag dynamic translatable strings and textdomains.

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

[Unreleased]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.9.0...HEAD
[0.9.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.8.0...0.9.0
[0.8.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.7.1...0.8.0
[0.7.1]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.7.0...0.7.1
[0.7.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.6.0...0.7.0
[0.6.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.5.0...0.6.0
[0.5.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.4.0...0.5.0
[0.4.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/2013-10-06...0.3.0
[2013-10-06]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/2013-06-11...2013-10-06
