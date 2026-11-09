<?php

isset( $_POST['key'] );

function test_use_inside_function_with_file_scope_validation() {
	echo /* testValidationTarget */ $_POST['key'];
}
