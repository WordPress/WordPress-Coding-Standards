<!-- Annotation has to be on the second line as errors are thrown on line 1 and errors on annotation lines are ignored. -->
phpcs:set WordPress.Files.FileName strict_class_file_names false

<?php

class Non_Strict_Class {}

// phpcs:set WordPress.Files.FileName strict_class_file_names true
