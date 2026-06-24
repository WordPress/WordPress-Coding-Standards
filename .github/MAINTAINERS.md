# Maintaining the WordPress Coding Standards

This document describes the conventions maintainers of the WordPress Coding Standards (WordPressCS) follow when triaging issues, reviewing pull requests, and releasing new versions. It is aimed at maintainers and at contributors who want to understand how decisions are made.

If you are looking to contribute a patch or a new sniff, start with [CONTRIBUTING](CONTRIBUTING.md) instead.

## The rulesets we maintain

Which handbook, if any, governs a rule depends on the ruleset it belongs to:

- **`WordPress-Core`** is the sniff implementation of the [WordPress PHP Coding Standards handbook](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/).
- **`WordPress-Docs`** is the sniff implementation of the [inline documentation standards handbook](https://developer.wordpress.org/coding-standards/inline-documentation-standards/php/).
- **`WordPress-Extra`** is not tied to a handbook. It collects best practices and other helpful sniffs, and can include rules that are not relevant to WP Core, as well as rules that may yet be proposed for Core but are already worth encouraging for the wider community.

## Scope and philosophy: WordPressCS follows, it does not lead

For anything governed by a handbook — that is, the `WordPress-Core` and `WordPress-Docs` rulesets — WordPressCS follows, it does not lead. It encodes a decision that has already been made and written down in the handbook; it does not get to decide for itself what "correct" code looks like. This is the single most important principle for judging feature requests against those rulesets.

In practice, for handbook-governed rules, this means:

- **A change to the handbook must come first.** If someone wants WordPressCS to enforce (or stop enforcing) a particular convention, the convention needs to exist in the handbook before the sniff can change. WordPressCS should not invent or pre-empt standards.
- **Non-trivial changes need community buy-in.** For anything beyond a trivial clarification, that typically means a post on [Make Core](https://make.wordpress.org/core/) to gather consensus. Be realistic about this: it only takes a single Core committer to disagree for a proposed change to be blocked, and that does happen.
- **Review feature requests through this lens.** When triaging a feature request, the first question is rarely "is this a good idea?" but "is this already in the handbook, and if not, has it already been raised on Make Core?"

`WordPress-Extra` gives more latitude, since it is not bound to a handbook. A rule can live there on the strength of being a genuine, widely-accepted best practice. Even so, be conservative: `Extra` is still shipped to a huge number of projects, so a new rule needs to earn its place.

There is a tension here. The handbook says the standards are meant for the whole WordPress community and not only Core, which in theory makes Core just one consumer among tens of thousands of projects. That argument has never worked in practice. A rule that Core itself does not follow looks odd, so proposals get judged against Core anyway. Core does still keep some exceptions of its own, and not only for bundled third-party packages, but do not count on the "just one consumer" point to unblock a change.

## Code of conduct and moderation

- Follow and uphold the [WordPress Code of Conduct](https://github.com/WordPress/.github/blob/trunk/CODE_OF_CONDUCT.md).
- **Never delete a comment, issue or pull request.** This is a WordPress-organization-wide rule. If a comment needs to be removed from view, hide it with an appropriate reason rather than deleting it, so the record is preserved.

## Reviewing and merging pull requests

### Do not merge your own pull requests

As a rule of thumb, never merge your own PR. There are two exceptions:

1. **Release PRs**, which still need approvals but are merged on a planned date by whomever is running the release.
2. **PRs that require a branch protection rule change** — for example, a PR that adds or renames a required CI job. The workflow change itself belongs in the PR, but the matching branch protection rule has to be updated by hand, and doing that early would block every other open PR. So make the rule change only *after* the PR has its required approvals and *just before* it is merged.

### How many approvals a PR needs

- **Trivial PRs** — for example Dependabot PRs where CI passes and no further changes are needed — need approval from **one** maintainer and can then be merged straight away.
- **Most PRs** need approval from **two** maintainers, with the **second** maintainer doing the merge. The merging maintainer is also responsible for deciding whether the PR needs a squash-merge or not.
- **Policy PRs** — changes to how the project is governed or run rather than to the sniffs themselves, such as this guide, `CONTRIBUTING`, the Code of Conduct, or the release and branch-protection process — need approval from **all active maintainers**, since they set precedent for everyone.
- **Squash-merge when** the branch's individual commits don't each warrant keeping — work-in-progress commits, review fixups, "address feedback" commits. **Keep the commits (a regular merge) when** each one is atomic and self-contained, so the history is worth preserving.
- If the first approval has been given and the PR is then **changed significantly** (for instance, in response to the second maintainer's review), it needs a fresh review from either the first reviewer or another maintainer.
- **When in doubt about a PR, do not merge.** Ping another maintainer for an extra pair of eyes.

### Labels, milestones and related tickets

Either of the two approvers must:

- Verify that the **labels** and **milestone** are set and correct, and
- Check that any **related tickets** will be closed by the PR, and give those tickets the milestone too.

Even if the first reviewer has already checked these, the maintainer doing the merge should verify them again: a release may have happened in the meantime, leaving the milestone out of date.

### External PRs

For PRs from external contributors, **always favor the original poster (OP) making the changes** over a maintainer making them. Editing an external contributor's PR yourself should be rare, and only done when the PR is effectively abandoned or the change is genuinely trivial (such as fixing a typo). We do this on purpose: it helps the community grow and empowers contributors to take ownership of their work, rather than having maintainers quietly finish it for them.

## Working with branches

- The branching model is documented in [CONTRIBUTING](CONTRIBUTING.md#branches): ongoing development happens in `develop` and is merged to `main` once considered stable; feature branches are prefixed with `feature/`.
- **If you have push access, push PR branches to this repository, not to your fork.** This lets other maintainers check out your branch locally without adding an extra remote. Work-in-progress branches can, of course, still live on your own fork.
- **For your own PR branches**, set the labels yourself, and set the milestone if it is clear-cut which release the change belongs in. When in doubt, leave the milestone open.

## Branch protection

The `develop` and `main` branches are protected through **Settings → Branches** (the classic branch protection rules), rather than the newer **Settings → Rules → Rulesets**. This is a deliberate choice.

<!-- TODO(maintainers): document *why* we deliberately still use classic branch protection rules instead of Rulesets, rather than leaving this as an unexplained assertion. -->

When a PR adds or renames a CI job that should block merging, the matching required status check has to be added or updated in these rules by hand. Make that change just before merging the PR: a check that is newly required but does not yet exist on other branches will block every other open PR until they are rebased.

## Dependabot pull requests

Dependabot PRs are treated as trivial (one approval, then merge) when CI passes and no additional changes are needed. However:

- **Always check the changelog for the update.** Read it for two reasons:
  1. to see whether there is anything useful we could adopt in our own workflows, and
  2. to gauge the risk of the update before merging.

## Releases

The full release procedure lives in [`release-checklist.md`](release-checklist.md), the template used for the release PR from `develop` to `main`. Follow that checklist rather than restating its steps here, so the two cannot drift out of sync. As noted above, a release PR gathers its approvals ahead of time but is merged on the planned release date by whomever is running the release.

## Becoming a maintainer

Contributors are promoted to maintainer after a sustained track record of high-quality contributions and reviews, and with the agreement of the existing active maintainers. Once that decision has been made:

- Add them to the [`wpcs` team](https://github.com/orgs/WordPress/teams/wpcs) in the WordPress organization, which grants the repository access a maintainer needs.
- Also give them commit/maintain access to the [`wpcs-docs`](https://github.com/WordPress/wpcs-docs) repository; WordPressCS maintainers should always have this.

## Dependencies

WordPressCS builds on, and often coordinates releases with, the [PHPCSStandards](https://github.com/PHPCSStandards) ecosystem — [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer), [PHPCSUtils](https://github.com/PHPCSStandards/PHPCSUtils) and [PHPCSExtra](https://github.com/PHPCSStandards/PHPCSExtra). Keeping in step with their releases is part of the [release checklist](release-checklist.md).

### Adding a new dependency

A runtime dependency (anything in `require`) is installed into every project that uses WordPressCS, so the bar for adding one is high. Before adding a dependency, weigh:

- **Necessity.** Can the job be done with what we already depend on, or with a small amount of our own code? Prefer not adding a dependency at all.
- **License.** It must carry a license compatible with our MIT license.
- **PHP support.** It must support our full supported PHP range (currently PHP 7.2 and up) without narrowing it.
- **Maintenance and trust.** Look for an actively-maintained project with stable releases, a sound backward-compatibility and security track record, and a maintainer we can reasonably coordinate with. Prefer the PHPCSStandards ecosystem, which we already release in step with.
- **Supply-chain weight.** Every dependency is one more thing our users pull in, and one more thing that can break or be compromised. Keep the tree small.

Development-only tooling (in `require-dev`) does not ship to users, so the bar is lower, but the license, PHP-support and maintenance considerations still apply.
