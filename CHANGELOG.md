# Change Log for WordPress Coding Standards

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](https://semver.org/) and [Keep a CHANGELOG](https://keepachangelog.com/).

## [1.0.0] - 2018-07-25

### Important information about this release:

If you use the WordPress Coding Standards with a custom ruleset, please be aware that a number of sniffs have been moved between categories and that the old sniff names have been deprecated.
If you selectively include any of these sniffs in your custom ruleset or set custom property values for these sniffs, your custom ruleset will need to be updated.

The `WordPress-VIP` ruleset has also been deprecated. If you used that ruleset to check your theme/plugin for hosting on the WordPress.com VIP platform, please use the [Automattic VIP coding standards](https://github.com/Automattic/VIP-Coding-Standards) instead.
If you used that ruleset for any other reason, you should probably use the `WordPress-Extra` or `WordPress` ruleset instead.

These and some related changes have been annotated in detail in the `Deprecated` section of this changelog.

Please read the complete changelog carefully before you upgrade.

If you are a maintainer of an external standard based on WPCS and any of your custom sniffs are based on or extend WPCS sniffs, the same applies.

### Added
- `WordPress.PHP.PregQuoteDelimiter` sniff to the `WordPress-Extra` ruleset to warn about calls to `preg_quote()` which don't pass the `$delimiter` parameter.
- `WordPress.Security.SafeRedirect` sniff to the `WordPress-Extra` ruleset to warn about potential open redirect vulnerabilities.
- `WordPress.WP.DeprecatedParameterValues` sniff to the `WordPress-Extra` ruleset to detect deprecated parameter values being passed to select functions.
- `WordPress.WP.EnqueuedResourceParameters` sniff to the `WordPress-Extra` ruleset to detect:
    - Calls to the script/style register/enqueue functions which don't pass a `$version` for the script/style, which can cause issues with browser caching; and/or
    - Calls to the register/enqueue script functions which don't pass the `$in_footer` parameter, which causes scripts - by default - to be loaded in the HTML header in a layout rendering blocking manner.
- Detection of calls to `strip_tags()` and various PHP native `..rand()` functions to the `WordPress.WP.AlternativeFunctions` sniff.
- `readonly()` to the list of auto-escaped functions `Sniff::$autoEscapedFunctions`. This affects the `WordPress.Security.EscapeOutput` sniff.
- The `WordPress.Security.PluginMenuSlug`, `WordPress.WP.CronInterval`, `WordPress.WP.PostsPerPage` and `WordPress.WP.TimezoneChange` sniffs are now included in the `WordPress-Extra` ruleset. Previously, they were already included in the `WordPress` and `WordPress-VIP` rulesets.
- New utility method `Sniff::is_use_of_global_constant()`.
- A rationale to the package suggestion made via `composer.json`.
- CI: Validation of the `composer.json` file on each build.
- A wiki page with instructions on how to [set up WPCS to run with Eclipse on XAMPP](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/How-to-use-WPCS-with-Eclipse-and-XAMPP).
- Readme: A link to an external resource with more examples for setting up PHPCS for CI.
- Readme: A badge-based quick overview of the project.

### Changed
- The `WordPress` ruleset no longer includes the `WordPress-VIP` ruleset, nor does it include any of the (deprecated) `VIP` sniffs anymore.
- The following sniffs have been moved to a new category:
    - `CronInterval` from the `VIP` category to the `WP` category.
    - `DirectDatabaseQuery` from the `VIP` category to the `DB` category.
    - `DontExtract` from the `Functions` category to the `PHP` category.
    - `EscapeOutput` from the `XSS` category to the `Security` category.
    - `GlobalVariables` from the `Variables` category to the `WP` category.
    - `NonceVerification` from the `CSRF` category to the `Security` category.
    - `PluginMenuSlug` from the `VIP` category to the `Security` category.
    - `PreparedSQL` from the `WP` category to the `DB` category.
    - `SlowDBQuery` from the `VIP` category to the `DB` category.
    - `TimezoneChange` from the `VIP` category to the `WP` category.
    - `ValidatedSanitizedInput` from the `VIP` category to the `Security` category.
- The `WordPress.VIP.PostsPerPage` sniff has been split into two distinct sniffs:
    - `WordPress.WP.PostsPerPage` which will check for the use of a high pagination limit and will throw a `warning` when this is encountered. For the `VIP` ruleset, the error level remains `error`.
    - `WordPress.VIP.PostsPerPage` wich will check for disabling of pagination.
- The default value for `minimum_supported_wp_version`, as used by a [number of sniffs detecting usage of deprecated WP features](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#minimum-wp-version-to-check-for-usage-of-deprecated-functions-classes-and-function-parameters), has been updated to `4.6`.
- The `WordPress.WP.AlternativeFunctions` sniff will now only throw a warning if/when the recommended alternative function is available in the minimum supported WP version of a project.
    In addition to this, certain alternatives are only valid alternatives in certain circumstances, like when the WP version only supports the first parameter of the PHP function it is trying to replace.
    This will now be taken into account for:
    - `wp_strip_all_tags()` is only a valid alternative for the PHP native `strip_tags()` when the second parameter `$allowed_tags` has not been passed.
    - `wp_parse_url()` only added support for the second parameter `$component` of the PHP native `parse_url()` function in WP 4.7.0.
- The `WordPress.WP.DeprecatedFunctions` sniff will now detect functions deprecated in WP 4.9.
- The `WordPress.WP.GlobalVariablesOverride` sniff will now display the name of the variable being overridden in the error message.
- The `WordPress.WP.I18n` sniff now extends the `AbstractFunctionRestrictionSniff`.
- Assignments in conditions in ternaries as detected by the `WordPress.CodeAnalysis.AssignmentInCondition` sniff will now be reported under a separate error code `FoundInTernaryCondition`.
- The default error level for the notices from the `WordPress.DB.DirectDatabaseQuery` sniff has been lowered from `error` to `warning`. For the `VIP` ruleset, the error level remains `error`.
- The default error level for the notices from the `WordPress.Security.PluginMenuSlug` sniff has been lowered from `error` to `warning`. For the `VIP` ruleset, the error level remains `error`.
- The default error level for the notices from the `WordPress.WP.CronInterval` sniff has been lowered from `error` to `warning`. For the `VIP` ruleset, the error level remains `error`.
- The `Sniff::get_function_call_parameters()` utility method now has improved handling of closures when passed as function call parameters.
- Rulesets: a number of error codes were previously silenced by explicitly `exclude`-ing them. Now, they will be silenced by setting the `severity` to `0` which makes it more easily discoverable for maintainers of custom rulesets how to enable these error codes again.
- Various performance optimizations which should most notably make a difference when running WPCS on PHP 7.
- References to the WordPress.com VIP platform have been clarified.
- Unit Tests: custom properties set in unit test files are reset after use.
- Various improvements to the ruleset used by the WPCS project itself and minor code clean up related to this.
- CI: Each change will now also be tested against the lowest supported PHPCS 3 version.
- CI: Each change will now also be checked for PHP cross-version compatibility.
- CI: The rulesets will now also be tested on each change to ensure no unexpected messages are thrown.
- CI: Minor changes to the script to make the build testing faster.
- Updated the [custom ruleset example](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/blob/develop/phpcs.xml.dist.sample) for the changes contained in this release and to reflect current best practices regarding the PHPCompatibility standard.
- The instructions on how to set up WPCS for various IDEs have been moved from the `README` to the [wiki](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki).
- Updated output examples in `README.md` and `CONTRIBUTING.md` and other minor changes to these files.
- Updated references to the PHPCompatibility standard to reflect its new location and recommend using PHPCompatibilityWP.

### Deprecated
- The `WordPress-VIP` ruleset has been deprecated.
    For checking a theme/plugin for hosting on the WordPress.com VIP platform, please use the [Automattic VIP coding standards](https://github.com/Automattic/VIP-Coding-Standards) instead.
    If you used the `WordPress-VIP` ruleset for any other reason, you should probably use the `WordPress-Extra` or `WordPress` ruleset instead.
- The following sniffs have been deprecated and will be removed in WPCS 2.0.0:
    - `WordPress.CSRF.NonceVerification` - use `WordPress.Security.NonceVerification` instead.
    - `WordPress.Functions.DontExtract` - use `WordPress.PHP.DontExtract` instead.
    - `WordPress.Variables.GlobalVariables` - use `WordPress.WP.GlobalVariablesOverride` instead.
    - `WordPress.VIP.CronInterval` - use `WordPress.WP.CronInterval` instead.
    - `WordPress.VIP.DirectDatabaseQuery` - use `WordPress.DB.DirectDatabaseQuery` instead.
    - `WordPress.VIP.PluginMenuSlug` - use `WordPress.Security.PluginMenuSlug` instead.
    - `WordPress.VIP.SlowDBQuery` - use `WordPress.DB.SlowDBQuery` instead.
    - `WordPress.VIP.TimezoneChange` - use `WordPress.WP.TimezoneChange` instead.
    - `WordPress.VIP.ValidatedSanitizedInput` - use `WordPress.Security.ValidatedSanitizedInput` instead.
    - `WordPress.WP.PreparedSQL` - use `WordPress.DB.PreparedSQL` instead.
    - `WordPress.XSS.EscapeOutput` - use `WordPress.Security.EscapeOutput` instead.
    - `WordPress.VIP.AdminBarRemoval` without replacement.
    - `WordPress.VIP.FileSystemWritesDisallow` without replacement.
    - `WordPress.VIP.OrderByRand` without replacement.
    - `WordPress.VIP.RestrictedFunctions` without replacement.
    - `WordPress.VIP.RestrictedVariables` without replacement.
    - `WordPress.VIP.SessionFunctionsUsage` without replacement.
    - `WordPress.VIP.SessionVariableUsage` without replacement.
    - `WordPress.VIP.SuperGlobalInputUsage` without replacement.
- The following sniff categories have been deprecated and will be removed in WPCS 2.0.0:
    - `CSRF`
    - `Variables`
    - `XSS`
- The `posts_per_page` property in the `WordPress.VIP.PostsPerPage` sniff has been deprecated as the related functionality has been moved to the `WordPress.WP.PostsPerPage` sniff.
    See [WP PostsPerPage: post limit](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#wp-postsperpage-post-limit) for more information about this property.
- The `exclude` property which is available to most sniffs which extend the `AbstractArrayAssignmentRestrictions`, `AbstractFunctionRestrictions` and `AbstractVariableRestrictions` classes or any of their children, used to be a `string` property and expected a comma-delimited list of groups to exclude.
    The type of the property has now been changed to `array`. Custom rulesets which pass this property need to be adjusted to reflect this change.
    Support for passing the property as a comma-delimited string has been deprecated and will be removed in WPCS 2.0.0.
    See [Excluding a group of checks](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#excluding-a-group-of-checks) for more information about the sniffs affected by this change.
- The `AbstractVariableRestrictionsSniff` class has been deprecated as all sniffs depending on this class have been deprecated. Unless a new sniff is created in the near future which uses this class, the abstract class will be removed in WPCS 2.0.0.
- The `Sniff::has_html_open_tag()` utility method has been deprecated as it is now only used by deprecated sniffs. The method will be removed in WPCS 2.0.0.

### Removed
- `cancel_comment_reply_link()`, `get_bookmark()`, `get_comment_date()`, `get_comment_time()`, `get_template_part()`, `has_post_thumbnail()`, `is_attachement()`, `post_password_required()` and `wp_attachment_is_image()` from the list of auto-escaped functions `Sniff::$autoEscapedFunctions`. This affects the `WordPress.Security.EscapeOutput` sniff.
- WPCS no longer explicitly supports HHVM and builds are no longer tested against HHVM.
    For now, running WPCS on HHVM to test PHP code may still work for a little while, but HHVM has announced they are [dropping PHP support](https://hhvm.com/blog/2017/09/18/the-future-of-hhvm.html).

### Fixed
- Compatibility with PHP 7.3. A change in PHP 7.3 was causing the `WordPress.DB.RestrictedClasses`, `WordPress.DB.RestrictedFunctions` and the `WordPress.WP.AlternativeFunctions` sniffs to fail to correctly detect issues.
- Compatibility with the latest releases from [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).
    PHPCS 3.2.0 introduced new annotations which can be used inline to selectively disable/ignore certain sniffs.
    **Note**: The initial implementation of the new annotations was buggy. If you intend to start using these new style annotations, you are strongly advised to use PHPCS 3.3.0 or higher.
    For more information about these annotations, please refer to the [PHPCS Wiki](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Advanced-Usage#ignoring-parts-of-a-file).
    - The [WPCS native whitelist comments](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Whitelisting-code-which-flags-errors) can now be combined with the new style PHPCS whitelist annotations in the `-- for reasons` part of the annotation.
    - `WordPress.Arrays.ArrayDeclarationSpacing`: the fixer will now handle the new style annotations correctly.
    - `WordPress.Arrays.CommaAfterArrayItem`: prevent a fixer loop when new style annotations are encountered.
    - `WordPress.Files.FileName`: respect the new style annotations if these would selectively disable this sniff.
    - `WordPress.WhiteSpace.ControlStructureSpacing`: handle the new style annotations correctly for the "blank line at the start/end of control structure" checks and prevent a fixer conflict when the new style annotations are encountered.
    - `WordPress.WhiteSpace.PrecisionAlignment`: allow for checking of for precision alignment on lines containing new style annotations when `phpcs` is run with `--ignore-annotations`.
- The `Sniff::is_test_class()` method now has improved recognition of namespaced test classes.
    This positively affects the `WordPress.Files.FileName`, `WordPress.NamingConventions.PrefixAllGlobals` and `WordPress.WP.GlobalVariablesOverride` sniffs, which each allow for test classes to (partially) not comply with the rules these sniffs check for.
    This fixes the following bugs:
    - Namespaced classes where the classname was one of the whitelisted global classes would incorrectly be recognized as a test class, even though they were not the same class.
        This also happened if a namespaced class `extend`ed one of the whitelisted global classes.
    - A namespaced custom test class where the name was split between the namespace declaration and the extended class declaration was not correctly recognized as the whitelisted test class.
    - A namespaced test class which extended another class using a FQCN prefixed with a `\\` would not be correctly recognized.
    - The `custom_test_class_whitelist` property which is available for each of these sniffs expects to be passed a Fully Qualified Class Name. FQCNs prefixed with a global namespace indicator will now be correctly handled.
- The determination of whether a `T_STRING` is a function call or not has been improved in the `AbstractFunctionRestrictions` class. This improvement benefits all sniffs which extend this abstract and any of its children (> 10 sniffs) and fixes the following false positives:
    - Class declarations will no longer be confused with function calls.
    - Use statement alias declarations will no longer be confused with function calls.
- Various bugs in the `WordPress.Arrays.ArrayIndentation` sniff:
    - The sniff will no longer throw false positives or try to fix multi-line text strings where the closing quote is on a line by itself.
    - The sniff would go into a fixer loop when it encountered a multi-line trailing comment after an array item.
- The `WordPress.CodeAnalysis.AssignmentInCondition` was throwing false positives for ternaries in nested, but unrelated, parentheses.
- The `WordPress.CodeAnalysis.EmptyStatement` and `WordPress.Files.FileName` sniffs underreported as they did not take PHP short open echo tags into account.
- Various bugs in the `WordPress.NamingConventions.PrefixAllGlobals` sniff:
    - Parameters in a closure declaration were incorrectly being regarded as global variables.
    - Non-prefixed variables created by a `foreach()` construct in the global namespace were previously not detected.
    - Non-prefixed globals found in namespaced test classes should be ignored by the sniff, but were not.
    - Definition of non-prefixed global WP constants which are intended to be overruled, should not trigger an error from this sniff.
    - The sniff presumed the WP naming conventions for PHP constructs, while it should check for the construct being prefixed regardless of whether camelCase, PascalCase, snake_case or other naming conventions are used.
    - The sniff presumed the WP naming conventions for prefixes used in hook names. The sniff will now be more tolerant when non-conventional word separators are used in prefixes for hooks.
- The `WordPress.NamingConventions.ValidFunctionName` sniff no longer "hides" one message behind another. The sniff will now correctly throw a message about function names not being in `snake_case`, even when the `FunctionDoubleUnderscore` or `MethodDoubleUnderscore` error codes have been excluded.
- The `WordPress.PHP.StrictInArray` sniff will no longer throw an error when `in_array`, `array_search` or `array_keys` are used in a file `use` statement.
- Various bugs in the `WordPress.Security.EscapeOutput` sniff:
    - A limited list of native PHP constants which are safe to use, such as `PHP_EOL`, has been added. When any of these constants are encountered, the sniff will no longer demand output escaping for them.
    - The sniff was underreporting issues with variables passed to `trigger_error()`.
    - While reporting an issue, sometimes the wrong error message was used. The sniff logic has been adjusted to prevent this.
    - The sniff will now correctly ignore the open and close brackets of short arrays.
    - The sniff would throw false positives when `echo`, `print`, `exit` or `die` were encountered as constants, function or class names. While it may not be a good idea to use PHP keywords in such a way, it is allowed, so the sniff should handle this correctly.
- The `WordPress.WhiteSpace.ControlStructureSpacing` sniff would inadvertently throw an error for the spacing around the colon for a return type in a function declaration.
- The `WordPress.WP.AlternativeFunctions` sniff used to flag all function calls to `file_get_contents()` twice, suggesting to use `wp_remote_get()` - which is only applicable for remote URLs - and the `WP_FileSystem` API - which is not needed when just _reading_ local files. These messages contradicted each other.
    The sniff will now try to determine whether the file requested is local or remote and will only throw a `warning` suggesting to use `wp_remote_get()`, if a remote URL is being requested or when it could not be determined if the requested file is local or remote.
- The expected default value for `wp_upload_bits()` in the `WordPress.WP.DeprecatedParameters` sniff.
- The `WordPress.WP.GlobalVariablesOverride` sniff previously did not detect variables created by a `foreach()` construct which would override WP global variables.
- Various bugs in the `WordPress.WP.I18n` sniff:
    - The sniff will no longer throw false positives for calls to methods carrying the same name as any of the global WP functions being targeted and has improved handling of parse errors and live coding.
    - A numeric `0` would throw a false positive for "no translatable content found".
- The fixer in the `WordPress.WhiteSpace.ControlStructureSpacing` sniff will no longer inadvertently remove return type declarations.
- Various bugs in the `WordPress.WhiteSpace.PrecisionAlignment` sniff:
    - Inline HTML before the first PHP open tag was not being examined.
    - Files which only contained short open echo tags for PHP were not being examined.
    - The last line of inline HTML in a file was not being examined.
- Some best practice sniffs presumed the WordPress coding style regarding code layout, which could lead to incorrect results (mostly underreporting).
    The following sniffs have received fixes related to this:
    - `WordPress.DB.PreparedSQL`
    - `WordPress.NamingConventions.ValidVariableName`
    - `WordPress.WP.CronInterval`
    - `WordPress.WP.I18n`
- Various minor fixes based on visual inspection and Scrutinizer analysis feedback.
- Typo in the instructions contained in `CONTRIBUTING.md`.
- Broken link in the `README.md` file.


## [0.14.1] - 2018-02-15

### Fixed
- The `WordPress.NamingConventions.PrefixAllGlobals` sniff contained a bug which could inadvertently trigger class autoloading of the project being sniffed and by extension could cause fatal errors during the PHPCS run.

## [0.14.0] - 2017-11-01

### Added
- `WordPress.Arrays.MultipleStatementAlignment` sniff to the `WordPress-Core` ruleset which will align the array assignment operator for multi-item, multi-line associative arrays.
    This new sniff offers four custom properties to customize its behaviour: [`ignoreNewlines`](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#array-alignment-allow-for-new-lines), [`exact`](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#array-alignment-allow-non-exact-alignment), [`maxColumn`](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#array-alignment-maximum-column) and [`alignMultilineItems`](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#array-alignment-dealing-with-multi-line-items).
- `WordPress.DB.PreparedSQLPlaceholders` sniff to the `WordPress-Core` ruleset which will analyse the placeholders passed to `$wpdb->prepare()` for their validity, check whether queries using `IN ()` and `LIKE` statements are created correctly and will check whether a correct number of replacements are passed.
    This sniff should help detect queries which are impacted by the security fixes to `$wpdb->prepare()` which shipped with WP 4.8.2 and 4.8.3.
    The sniff also adds a new ["PreparedSQLPlaceholders replacement count" whitelist comment](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Whitelisting-code-which-flags-errors#preparedsql-placeholders-vs-replacements) for pertinent replacement count vs placeholder mismatches. Please consider carefully whether something could be a bug when you are tempted to use the whitelist comment and if so, [report it](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/new).
- `WordPress.PHP.DiscourageGoto` sniff to the `WordPress-Core` ruleset.
- `WordPress.PHP.RestrictedFunctions` sniff to the `WordPress-Core` ruleset which initially forbids the use of `create_function()`.
    This was previous only discouraged under certain circumstances.
- `WordPress.WhiteSpace.ArbitraryParenthesesSpacing` sniff to the `WordPress-Core` ruleset which checks the spacing on the inside of arbitrary parentheses.
- `WordPress.WhiteSpace.PrecisionAlignment` sniff to the `WordPress-Core` ruleset which will throw a warning when precision alignment is detected in PHP, JS and CSS files.
- `WordPress.WhiteSpace.SemicolonSpacing` sniff to the `WordPress-Core` ruleset which will throw a (fixable) error when whitespace is found before a semi-colon, except for when the semi-colon denotes an empty `for()` condition.
- `WordPress.CodeAnalysis.AssignmentInCondition` sniff to the `WordPress-Extra` ruleset.
- `WordPress.WP.DiscouragedConstants` sniff to the `WordPress-Extra` and `WordPress-VIP` rulesets to detect usage of deprecated WordPress constants, such as `STYLESHEETPATH` and `HEADER_IMAGE`.
- Ability to pass the `minimum_supported_version` to use for the `DeprecatedFunctions`, `DeprecatedClasses` and `DeprecatedParameters` sniff in one go. You can pass a `minimum_supported_wp_version` runtime variable for this [from the command line or pass it using a `config` directive in a custom ruleset](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#setting-minimum-supported-wp-version-for-all-sniffs-in-one-go-wpcs-0140).
- `Generic.Formatting.MultipleStatementAlignment` - customized to have a `maxPadding` of `40` -, `Generic.Functions.FunctionCallArgumentSpacing` and `Squiz.WhiteSpace.ObjectOperatorSpacing` to the `WordPress-Core` ruleset.
- `Squiz.Scope.MethodScope`, `Squiz.Scope.MemberVarScope`, `Squiz.WhiteSpace.ScopeKeywordSpacing`, `PSR2.Methods.MethodDeclaration`, `Generic.Files.OneClassPerFile`, `Generic.Files.OneInterfacePerFile`, `Generic.Files.OneTraitPerFile`, `PEAR.Files.IncludingFile`, `Squiz.WhiteSpace.LanguageConstructSpacing`, `PSR2.Namespaces.NamespaceDeclaration` to the `WordPress-Extra` ruleset.
- The `is_class_constant()`, `is_class_property` and `valid_direct_scope()` utility methods to the `WordPress\Sniff` class.

### Changed
- When passing an array property via a custom ruleset to PHP_CodeSniffer, spaces around the key/value are taken as intentional and parsed as part of the array key/value. In practice, this leads to confusion and WPCS does not expect any values which could be preceded/followed by a space, so for the WordPress Coding Standard native array properties, like `customAutoEscapedFunction`, `text_domain`, `prefixes`, WPCS will now trim whitespace from the keys/values received before use.
- The WPCS native whitelist comments used to only work when they were put on the _end of the line_ of the code they applied to. As of now, they will also be recognized when they are be put at the _end of the statement_ they apply to.
- The `WordPress.Arrays.ArrayDeclarationSpacing` sniff used to enforce all associative arrays to be multi-line. The handbook has been updated to only require this for multi-item associative arrays and the sniff has been updated accordingly.
    [The original behaviour can still be enforced](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#arrays-forcing-single-item-associative-arrays-to-be-multi-line) by setting the new `allow_single_item_single_line_associative_arrays` property to `false` in a custom ruleset.
- The `WordPress.NamingConventions.PrefixAllGlobals` sniff will now allow for a limited list of WP core hooks which are intended to be called by plugins and themes.
- The `WordPress.PHP.DiscouragedFunctions` sniff used to include `create_function`. This check has been moved to the new `WordPress.PHP.RestrictedFunctions` sniff.
- The `WordPress.PHP.StrictInArray` sniff now has a separate error code `FoundNonStrictFalse` for when the `$strict` parameter has been set to `false`. This allows for excluding the warnings for that particular situation, which will normally be intentional, via a custom ruleset.
- The `WordPress.VIP.CronInterval` sniff now allows for customizing the minimum allowed cron interval by [setting a property in a custom ruleset](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#vip-croninterval-minimum-interval).
- The `WordPress.VIP.RestrictedFunctions` sniff used to prohibit the use of certain WP native functions, recommending the use of `wpcom_vip_get_term_link()`, `wpcom_vip_get_term_by()` and `wpcom_vip_get_category_by_slug()` instead, as the WP native functions were not being cached. As the results of the relevant WP native functions are cached as of WP 4.8, the advice has now been reversed i.e. use the WP native functions instead of `wpcom...` functions.
- The `WordPress.VIP.PostsPerPage` sniff now allows for customizing the `post_per_page` limit for which the sniff will trigger by [setting a property in a custom ruleset](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#vip-postsperpage-post-limit).
- The `WordPress.WP.I18n` sniff will now allow and actively encourage omitting the text-domain in I18n function calls if the text-domain passed via the `text_domain` property is `default`, i.e. the domain used by Core.
    When `default` is one of several text-domains passed via the `text_domain` property, the error thrown when the domain is missing has been downgraded to a `warning`.
- The `WordPress.XSS.EscapeOutput` sniff now has a separate error code `OutputNotEscapedShortEcho` and the error message texts have been updated.
- Moved `Squiz.PHP.Eval` from the `WordPress-Extra` and `WordPress-VIP` to the `WordPress-Core` ruleset.
- Removed two sniffs from the `WordPress-VIP` ruleset which were already included via the `WordPress-Core` ruleset.
- The unit test suite is now compatible with PHPCS 3.1.0+ and PHPUnit 6.x.
- Some tidying up of the unit test case files.
- All sniffs are now also being tested against PHP 7.2 for consistent sniff results.
- An attempt is made to detect potential fixer conflicts early via a special build test.
- Various minor documentation fixes.
- Improved the Atom setup instructions in the Readme.
- Updated the unit testing information in Contributing.
- Updated the [custom ruleset example](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/blob/develop/phpcs.xml.dist.sample) for the changes contained in this release and to make it more explicit what is recommended versus example code.
- The minimum recommended version for the suggested `DealerDirect/phpcodesniffer-composer-installer` Composer plugin has gone up to `0.4.3`. This patch version fixes support for PHP 5.3.

### Fixed
- The `WordPress.Arrays.ArrayIndentation` sniff did not correctly handle array items with multi-line strings as a value.
- The `WordPress.Arrays.ArrayIndentation` sniff did not correctly handle array items directly after an array item with a trailing comment.
- The `WordPress.Classes.ClassInstantiation` sniff will now correctly handle detection when using `new $array['key']` or `new $array[0]`.
- The `WordPress.NamingConventions.PrefixAllGlobals` sniff did not allow for arbitrary word separators in hook names.
- The `WordPress.NamingConventions.PrefixAllGlobals` sniff did not correctly recognize namespaced constants as prefixed.
- The `WordPress.PHP.StrictInArray` sniff would erronously trigger if the `true` for `$strict` was passed in uppercase.
- The `WordPress.PHP.YodaConditions` sniff could get confused over complex ternaries containing assignments. This has been remedied.
- The `WordPress.WP.PreparedSQL` sniff would erronously throw errors about comments found within a DB function call.
- The `WordPress.WP.PreparedSQL` sniff would erronously throw errors about `(int)`, `(float)` and `(bool)` casts and would also flag the subsequent variable which had been safe casted.
- The `WordPress.XSS.EscapeOutput` sniff would erronously trigger when using a fully qualified function call - including the global namespace `\` indicator - to one of the escaping functions.
- The lists of WP global variables and WP mixed case variables have been synchronized, which fixes some false positives.


## [0.13.1] - 2017-08-07

### Fixed
- Fatal error when using PHPCS 3.x with the `installed_paths` config variable set via the ruleset.

## [0.13.0] - 2017-08-03

### Added
- Support for PHP_CodeSniffer 3.0.2+. The minimum required PHPCS version (2.9.0) stays the same.
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
- `WordPress.WP.DeprecatedFunctions` sniff to the `WordPress-Extra` ruleset to check for usage of deprecated WP version and show errors/warnings depending on a `minimum_supported_version` which [can be passed to the sniff from a custom ruleset](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#minimum-wp-version-to-check-for-usage-of-deprecated-functions-classes-and-function-parameters). The default value for the `minimum_supported_version` property is three versions before the current WP version.
- `WordPress.WP.I18n`: ability to check for missing _translators comments_ when a I18n function call contains translatable text strings containing placeholders. This check will also verify that the _translators comment_ is correctly placed in the code and uses the correct comment type for optimal compatibility with the various tools available to create `.pot` files.
- `WordPress.WP.I18n`: ability to pass the `text_domain` to check for [from the command line](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#setting-text_domain-from-the-command-line-wpcs-0110).
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
- `WordPress.Variables.GlobalVariables`: will no longer throw errors if the global variable override is done from within a test method. Whether something is considered a "test method" is based on whether the method is in a class which extends a predefined set of known unit test classes. This list can be enhanced by setting the [`custom_test_class_whitelist` property](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties#custom-unit-test-classes) in your ruleset.
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
[1.0.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.14.1...1.0.0
[0.14.1]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.14.0...0.14.1
[0.14.0]: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/compare/0.13.1...0.14.0
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
