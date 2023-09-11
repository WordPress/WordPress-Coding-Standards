# Template to use for release PRs from `develop` to `main`

:warning: **DO NOT MERGE (YET)** :warning:

**Please **do** add approvals if you agree as otherwise we won't be able to release.**

PR for tracking changes for the x.x.x release. Target release date: **DOW MONTH DAY YEAR**.

## Release checklist

### General

- [ ] Verify, and if necessary, update the allowed version ranges for various dependencies in the `composer.json` - PR #xxx
- [ ] PHPCS: check if there have been [releases][phpcs-releases] since the last WordPressCS release and check through the changelog to see if there is anything WordPressCS could take advantage of - PR #xxx
- [ ] PHPCSUtils: check if there have been [releases][phpcsutils-releases] since the last WordPressCS release and update WordPressCS code to take advantage of any new utilities - PR #xxx
- [ ] PHPCSExtra: check if there have been [releases][phpcsextra-releases] since the last WordPressCS release and check through the changelog to see if there is anything WordPressCS could take advantage of - PR #xxx
- [ ] Check if the minimum WP version property needs updating in `MinimumWPVersionTrait::$default_minimum_wp_version` and if so, action it - PR #xxx
- [ ] Check if any of the list based sniffs need updating and if so, action it.
    :pencil2: Make sure the "last updated" annotation in the docblocks for these lists has also been updated!
    List based sniffs:
    - [ ] `WordPress.WP.ClassNameCase` - PR #xxx
    - [ ] `WordPress.WP.DeprecatedClasses` - PR #xxx
    - [ ] `WordPress.WP.DeprecatedFunctions` - PR #xxx
    - [ ] `WordPress.WP.DeprecatedParameters` - PR #xxx
    - [ ] `WordPress.WP.DeprecatedParameterValues` - PR #xxx
- [ ] Check if any of the other lists containing information about WP Core need updating and if so, action it.
    - [ ] `$allowed_core_constants` in `WordPress.NamingConventions.PrefixAllGlobals` - PR #xxx
    - [ ] `$pluggable_functions` in `WordPress.NamingConventions.PrefixAllGlobals` - PR #xxx
    - [ ] `$pluggable_classes` in `WordPress.NamingConventions.PrefixAllGlobals` - PR #xxx
    - [ ] `$target_functions` in `WordPress.Security.PluginMenuSlug` - PR #xxx
    - [ ] `$reserved_names` in `WordPress.NamingConventions.ValidPostTypeSlug` - PR #xxx
    - [ ] `$wp_time_constants` in `WordPress.WP.CronInterval` - PR #xxx
    - [ ] `$known_test_classes` in `IsUnitTestTrait` - PR #xxx
    - [ ] ...etc...

### Release prep

- [ ] Add changelog for the release - PR #xxx
    :pencil2: Remember to add a release link at the bottom!
- [ ] Update `README` (if applicable) - PR #xxx
- [ ] Update wiki (new customizable properties etc.) (if applicable)

### Release

- [ ] Merge this PR.
- [ ] Make sure all CI builds are green.
- [ ] Tag and create a release against `main` (careful, GH defaults to `develop`!) & copy & paste the changelog to it.
    :pencil2: Check if anything from the link collection at the bottom of the changelog needs to be copied in!
- [ ] Make sure all CI builds are green.
- [ ] Close the milestone.
- [ ] Open a new milestone for the next release.
- [ ] If any open PRs/issues which were milestoned for this release did not make it into the release, update their milestone.
- [ ] Fast-forward `develop` to be equal to `main`.

### After release

- [ ] Open a Trac ticket for WordPress Core to update.

### Publicize

- [ ] [Major releases only] Publish post about the release on Make WordPress.
- [ ] Tweet, toot, etc about the release.
- [ ] Post about it in Slack.
- [ ] Submit for ["Month in WordPress"][month-in-wp].
- [ ] Submit for the ["Monthy Dev Roundup"][dev-roundup].

[phpcs-releases]:      https://github.com/squizlabs/PHP_CodeSniffer/releases
[phpcsutils-releases]: https://github.com/PHPCSStandards/PHPCSUtils/releases
[phpcsextra-releases]: https://github.com/PHPCSStandards/PHPCSExtra/releases
[month-in-wp]:         https://make.wordpress.org/community/month-in-wordpress-submissions/
[dev-roundup]:         https://github.com/WordPress/developer-blog-content/issues?q=is%3Aissue+label%3A%22Monthly+Roundup%22
