<?php

/*
 * Tests with PHP 7.3+ flexible heredoc/nowdoc.
 */
$head = <<<EOT
	<link rel="stylesheet" href="http://someurl/somefile.css">
	<script src="http://someurl/somefile.js"></script>
	EOT;

$head = <<<"EOT"
        <link rel="stylesheet" href="https://someurl/somefile.css">
        <script src="https://someurl/somefile.js"></script>
    EOT;

$head = <<<'EOD'
		<link rel="stylesheet" href="http://someurl/somefile.css">
		<script src="http://someurl/somefile.js"></script>
		EOD;
