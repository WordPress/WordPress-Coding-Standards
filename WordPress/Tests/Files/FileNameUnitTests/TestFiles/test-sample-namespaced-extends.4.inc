<!-- Annotation has to be on the second line as errors are thrown on line 1 and errors on annotation lines are ignored. -->
phpcs:set WordPress.Files.FileName custom_test_class_whitelist[] Some\Name\TestSample
<?php

class TestCase extends TestSample {}
/* phpcs:set WordPress.Files.FileName custom_test_class_whitelist[] */
