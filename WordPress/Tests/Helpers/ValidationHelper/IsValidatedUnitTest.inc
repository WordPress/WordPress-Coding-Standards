<?php

/*
 * The below should NOT be considered validated.
 */

function test_not_validated() {
	echo /* testNotValidated */ $_POST['key'];
}

function test_outer_function_validation_not_counted() {
	if ( isset( $_POST['key'] ) ) {
		function test_inner_function() {
			echo /* testOuterFunctionValidationNotCounted */ $_POST['key'];
		}
	}
}

function test_closed_scope_validation_not_counted() {
	$callback = function () {
		if ( isset( $_POST['key'] ) ) {
			echo $_POST['key'];
		}
	};

	$obj = new class() {
		public function check() {
			if ( isset( $_POST['key'] ) ) {
				echo $_POST['key'];
			}
		}
	};

	echo /* testClosedScopeValidationNotCounted */ $_POST['key'];
}

function test_arrow_function_validation_not_counted() {
	$check = fn() => isset( $_POST['key'] );
	echo /* testArrowFunctionValidationNotCounted */ $_POST['key'];
}

function test_construct_wrong_variable_or_key() {
	if ( isset( $_GET['key'] ) || isset( $_POST['other'] ) ) {
		echo /* testConstructWrongVariableOrKey */ $_POST['key'];
	}
}

function test_construct_param_key_mismatch() {
	if ( isset( $_POST['key'] ) ) {
		echo /* testConstructParamKeyMismatch */ $_POST['key'];
	}
}

function test_function_name_used_as_constant() {
	echo ARRAY_KEY_EXISTS;
	echo /* testFunctionNameUsedAsConstant */ $_POST['key'];
}

function test_function_call_in_attribute() {
	#[array_key_exists('key', $_POST)]
	echo /* testFunctionCallInAttribute */ $_POST['key'];
}

function test_function_call_non_global() {
	if (
		$obj->array_key_exists( 'key', $_POST )
		|| $obj?->key_exists( 'key', $_POST )
		|| MyClass::array_key_exists( 'key', $_POST )
	) {
		echo /* testFunctionCallNonGlobal */ $_POST['key'];
	}
}

function test_function_call_namespaced() {
	if (
		MyNamespace\array_key_exists( 'key', $_POST )
		|| \MyNamespace\key_exists( 'key', $_POST )
		|| namespace\array_key_exists( 'key', $_POST ) // This should be considered validated in the future once the method is able to resolve relative namespaces.
		|| namespace\Sub\key_exists( 'key', $_POST )
	) {
		echo /* testFunctionCallNamespaced */ $_POST['key'];
	}
}

function test_function_call_missing_parameters() {
	if ( key_exists() ) {
		echo /* testFunctionCallMissingParameters */ $_POST['key'];
	}
}

function test_function_call_wrong_array_param() {
	if (
		key_exists( 'key', SOME_CONSTANT )
		|| array_key_exists( 'key', $_GET )
	) {
		echo /* testFunctionCallWrongArrayParam */ $_POST['key'];
	}
}

function test_function_call_mismatched_key() {
	if ( array_key_exists( 'other', $_POST ) ) {
		echo /* testFunctionCallMismatchedKey */ $_POST['key'];
	}
}

function test_coalesce_no_match() {
	$a = 'something' ?? 'default';
	$_GET['key'] ??= 'default';
	$_POST['other'] ??= 'default';
	echo /* testCoalesceNoMatch */ $_POST['key'];
}

/*
 * The below should be considered validated.
 */

function test_validated_with_isset() {
	if ( isset( $_POST["key"] ) ) {
		echo /* testValidatedWithIsset */ $_POST['key'];
	}
}

function test_validated_with_empty() {
	if ( empty( $_POST['key'] ) ) {
		echo /* testValidatedWithEmpty */ $_POST['key'];
	}
}

$closure = function() {
	if ( empty( $_POST['key'] ) ) {
		echo /* testValidatedInClosure */ $_POST['key'];
	}
};

function test_function_call() {
	if ( array_key_exists( 'key', $_POST ) ) {
		echo /* testFunctionCall */ $_POST['key'];
	}
}

function test_function_call_mixed_case() {
	if ( Key_Exists( 'key', $_POST ) ) {
		echo /* testFunctionCallMixedCase */ $_POST['key'];
	}
}

function test_function_call_fully_qualified() {
	if ( \array_key_exists( 'key', $_POST ) ) {
		echo /* testFunctionCallFullyQualified */ $_POST['key'];
	}
}

function test_function_call_fully_qualified_uppercase() {
	if ( \KEY_EXISTS( 'key', $_POST ) ) {
		echo /* testFunctionCallFullyQualifiedUppercase */ $_POST['key'];
	}
}

function test_validated_with_null_coalesce() {
	$_POST['key'] = $_POST['key'] ?? 'default';
	echo /* testValidatedWithNullCoalesce */ $_POST['key'];
}

function test_validated_with_coalesce_equal() {
	$_POST['key'] ??= 'default';
	echo /* testValidatedWithCoalesceEqual */ $_POST['key'];
}

/*
 * Test cases for `$in_condition_only` set to true.
 */

function test_in_condition_only_use_inside_condition() {
	if ( isset( $_POST['key'] ) ) {
		echo /* testInConditionOnlyUseInsideCondition */ $_POST['key'];
	}
}

function test_in_condition_only_use_outside_condition() {
	if ( empty( $_POST['key'] ) ) {
		return;
	}
	echo /* testInConditionOnlyUseOutsideCondition */ $_POST['key'];
}

/*
 * Test cases for multi-level array key matching across validation paths.
 */

function test_array_keys_construct() {
	if ( isset( $_POST['key']['sub'] ) ) {
		echo /* testArrayKeysConstruct */ $_POST['key']['sub'];
	}
}

function test_array_keys_function_call() {
	if ( array_key_exists( 'sub', $_POST['key'] ) ) {
		echo /* testArrayKeysFunctionCall */ $_POST['key']['sub'];
	}
}

function test_array_keys_function_call_key_param_mismatch() {
	if ( array_key_exists( 'wrong_key', $_POST['key'] ) ) {
		echo /* testArrayKeysFunctionCallKeyParamMismatch */ $_POST['key']['sub'];
	}
}

function test_array_keys_coalesce() {
	$_POST['key']['sub'] = $_POST['key']['sub'] ?? 'default';
	echo /* testArrayKeysCoalesce */ $_POST['key']['sub'];
}
