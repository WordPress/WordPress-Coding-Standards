<link rel="stylesheet" href="http://someurl/somefile.css">
<script src="http://someurl/somefile.js"></script>

<?php

?><link rel="stylesheet" href="http://someurl/somefile.css"><?php
?><script src="http://someurl/somefile.js"></script><?php


echo '<script src="' . SOMESCRIPT . '">';
echo '<link rel="stylesheet" src="' . SOMESCRIPT . '">';