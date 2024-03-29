<link rel="stylesheet" href="http://someurl/somefile.css">
<script src="http://someurl/somefile.js"></script>

<?php

?><link rel="stylesheet" href="https://someurl/somefile.css"><?php
?><script src="https://someurl/somefile.js"></script><?php


echo '<script src="' . SOMESCRIPT . '">';
echo '<link rel="stylesheet" src="' . SOMESCRIPT . '">';

$double_quoted = "<link rel=\"stylesheet\" href=\"{$stylesheet}\">
<script src=\"{$script}\"></script>";

$double_quoted = "<link rel='stylesheet' href='{$stylesheet}' />
<script src='{$script}'></script>";

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

?>

jQuery( document ).ready( function() {
	$('link[rel="stylesheet"]:not([data-inprogress])').forEach(StyleFix.link);
});

<?php
// Test multi-line text string.
echo '<script type="text/javascript"
	src="' . $script . '"/>';

// Test multi-line text string with multiple issues.
echo '<script type="text/javascript"
	src="http://someurl/somefile1.js"/>
	<script src="http://someurl/somefile2.js"></script>
	<script type="text/javascript"
	src="http://someurl/somefile3.js"/>
	';

// Test multi-line text string with multiple issues.
echo '<link rel="stylesheet" href="https://someurl/somefile1.css"/>
	<link rel="stylesheet" href="https://someurl/somefile2.css"/>';

// Safeguard the handling of a particular type of parse error (forgotten concat operator).
echo 'not a text we need to worry' 'about anyway, but we shouldn\'t see an uncaught runtime exception because of it';
