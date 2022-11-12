---
name: Bug report
about: Create a report to help us improve

---

<!--
PLEASE FILL OUT THE TEMPLATE COMPLETELY.
BUG REPORTS WHICH CANNOT BE REPRODUCED BASED ON THE INFORMATION PROVIDED WILL BE CLOSED.
-->

## Bug Description
<!--
Please provide a clear and concise description of what the bug is.

What did you expect to happen? What actually happened?
-->

## Minimal Code Snippet
<!-- Please provide example code that allows us to reproduce the issue. Do NOT paste screenshots of code! -->

The issue happens when running this command:
```bash
phpcs ...
```

... over a file containing this code:
```php
// Place your code sample here.
```

<!-- For bugs with fixers: How was the code fixed? How did you expect the code to be fixed? -->

The file was auto-fixed via `phpcbf` to:
```php
// Place your code sample here.
```

... while I expected the code to be fixed to:
```php
// Place your code sample here.
```

## Error Code
<!--
The error code for the sniff that is (or should be) being triggered (you
can see the sniff error codes by running `phpcs` with the `-s` flag).
e.g. `WordPress.PHP.NoSilencedErrors.Discouraged`

You can leave this section empty if you are reporting a false negative.
-->

## Custom ruleset
<!--
If the issue cannot be reproduced when using `--standard=WordPress` on the command line,
please post the relevant part of your custom ruleset here.
-->

```xml
<?xml version="1.0"?>
<ruleset name="My Custom Standard">
  ...
</ruleset>
```

## Environment
<!--
To find out the versions used:
* PHP: run `php -v`.
* PHPCS: run `[vendor/bin/]phpcs --version`
* WPCS: run `composer [global] info` for a Composer install.
-->

| Question               | Answer
| ------------------------| -------
| PHP version             | x.y.z
| PHP_CodeSniffer version | x.y.z
| WPCS version            | x.y.z
| WPCS install type       | e.g. Composer global, Composer project local, git clone, other (please expand)
| IDE (if relevant)       | Name and version e.g. PhpStorm 2018.2.2


## Additional Context (optional)
<!-- Add any other context about the problem here. -->

## Tested Against `develop` branch?
- [ ] I have verified the issue still exists in the `develop` branch of WPCS.
