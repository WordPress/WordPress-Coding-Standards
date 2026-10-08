<?php

function test_validation_in_function() {
	isset( $_POST['key'] );
}

/* testValidationTarget */
echo $_POST['key'];
