<?php

$ok = array(
	'value',
	123,
);

$ok_with_keys = [
	'key1' => 'value',
	'key2' => 'value', // Comment after item.
];

$ok_nested = array(
	'key1' => array(
		'key1' => 'value',
		'key2' => 'value',
	),
	'key2' => 'value',
);


$bad_phpcs_style = array(
					'value',
					123, // Comment after item.
				   );

$bad_phpcs_style_with_keys = array(
							  'key1' => 'value',
							  'key2' => 'value',
						     );

$bad_phpcs_style_nested = array(
						   'key1' => [
									  'key1' => 'value',

									  'key2' => 'value',

									 ],
						   'key2' => 'value',
						  );

// Arrays with initial indent.
			$bad_mixed_indent = [
'value',
										          	123,
					];

	$bad_mixed_indent_with_keys = array(

			'key1' => 'value',
'key2' => 'value',
		);

		$bad_mixed_indent_nested = [
				'key1' => array(
					'key1' => 'value',
						'key2' => 'value',
							),

								'key2' => 'value',
	];

$empty = [


    ];
