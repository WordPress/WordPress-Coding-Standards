<link rel="stylesheet" href="http://someurl/somefile.css">
<script src="http://someurl/somefile.js"></script>

<?php

?><link rel="stylesheet" href="http://someurl/somefile.css"><?php
?><script src="http://someurl/somefile.js"></script><?php


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
<link rel="stylesheet" href="http://someurl/somefile.css">
<script src="http://someurl/somefile.js"></script>
EOT;

$head = <<<'EOD'
<link rel="stylesheet" href="http://someurl/somefile.css">
<script src="http://someurl/somefile.js"></script>
EOD;

?>

jQuery( document ).ready( function() {
	$('link[rel="stylesheet"]:not([data-inprogress])').forEach(StyleFix.link);
});
