<?php

/*
 * Not the sniff target.
 */
my\ns\add_option($a, $b);
$this->add_option($a, $b);
MyClass::add_option($a, $b);
echo ADD_OPTION;
add_action('my_action', add_option(...)); // PHP 8.1 first class callable.
use function add_option;

/*
 * These should all be okay.
 */
$ok = \add_option( 'my_option', 'my_value', '', true );
$ok = add_option( 'my_option', array('some', 'big', 'array'), '', false );
$ok = add_option( 'my_option', new stdClass(), '', null );
$ok = add_option( 'my_option', autoload: FALSE, value: 'my_value' );
$ok = add_option( 'my_option', autoload: false );
$ok = add_option( autoload: false, deprecated: '', value: 'my_value', option: 'my_option' );
$ok = \update_option( 'my_option', 'new_value', \true );
$ok = update_option( 'my_option', 'new_value', \false );
$ok = update_option( 'my_option', 'new_value', \NULL );
$ok = update_option( 'my_option', autoload: TrUe, value: 'new_value' );
$ok = update_option( autoload: false, value: 'my_value', option: 'my_option' );
$ok = update_option( $option_name, get_option( $option_name ), false );
$ok = wp_set_options_autoload( array( 'option1', 'option2' ), \false );
$ok = wp_set_options_autoload( array( 'option1', 'option2' ), \true );
$ok = wp_set_options_autoload( autoload: false, options: array( 'option1', 'option2' ) );
$ok = wp_set_option_autoload( 'option1', false );
$ok = wp_set_option_autoload( 'option1', true );
$ok = wp_set_option_autoload( autoload: false, option: 'option1' );
$ok = wp_set_option_autoload_values(
    array(
        'option1' => true,
        'option2' => false,
    )
);
$ok = wp_set_option_autoload_values(
    options: array(
        'option1' => true,
        $option_name => false,
    )
);

/*
 * These should all be ignored by the sniff as the autoload value is undetermined or not easy to
 * determine.
 */
$ignored = add_option( 'my_option', 'my_value', '', $some_variable );
$ignored = add_option( 'my_option', 'my_value', '', $obj->yes );
$ignored = add_option( 'my_option', 'my_value', '', $some_variable ? 'yes' : 'no' );
$ignored = add_option( 'my_option', 'my_value', '', 'ye' . 's' );
$ignored = add_option( 'my_option', 'my_value', '', (function () { return 'yes'; })() );
$ignored = add_option( 'my_option', 'new_value', '', yes);
$ignored = wp_set_options_autoload( array( 'option1', 'option2' ), $some_variable );
$ignored = wp_set_option_autoload( 'option1', $some_variable );
$ignored = wp_set_option_autoload_values(
    array(
        'option1' => $some_variable,
        'option2' => $obj->yes,
        'option3' => $some_variable ? 'yes' : 'no',
        'option4' => 'ye' . 's',
        'option5' => (function () { return 'yes'; })(),
        'option6' => yes,
        'option7' => \my_function(),
        'option8' => \SOME_CONSTANT,
        'option9' => \YES,
    )
);

/*
 * These are ignored because it is not easy to determine the option names and the autoload values or
 * the `$options` parameter is empty.
 */
$ignored = wp_set_option_autoload_values( $options );
$ignored = wp_set_option_autoload_values( options: $options );
$ignored = wp_set_option_autoload_values(array());
$ignored = wp_set_option_autoload_values([]);
$ignored = wp_set_option_autoload_values(options: array());
$ignored = wp_set_option_autoload_values(options: []);

/*
 * Invalid function calls that are ignored by the sniff as a mandatory parameter is missing
 */
$ignored = wp_set_option_autoload_values();
$ignored = wp_set_options_autoload();
$ignored = wp_set_options_autoload( array( 'option1', 'option2' ) );
$ignored = wp_set_option_autoload();
$ignored = wp_set_option_autoload( 'option1' );

/*
 * These should all be flagged with a warning.
 */
$warning = add_option( 'my_option' );
$warning = \add_option( 'my_option', 'my_value' );
if ( add_option( 'my_option', 'my_value', true ) ) {}
$warning = ADD_OPTION( 'my_option', 'my_value', '', 'auto' );
$warning = add_option( 'my_option', 'my_value', '', 'auto-on' );
$warning = add_option(
    'my_option',
    'my_value',
    '',
    'auto-off'
);
$warning = add_option( 'my_option', 'my_value', '', 'invalid_value' );
$warning = add_option( 'my_option', 'my_value', '', 'true' );
$warning = add_option( 'my_option', 'my_value', '', "false" );
$warning = add_option( 'my_option', 'my_value', '', array('some', 'array') );
$warning = add_option( 'my_option', 'my_value', '', ['some', 'array'] );
$warning = add_option( option: 'my_option' );
$warning = add_option( value: 'my_value', option: 'my_option' );
$warning = add_option( value: 'my_value', deprecated: '', option: 'my_option' );
$warning = add_option( value: 'my_value', deprecated: '', autoload: 'auto', option: 'my_option' );
$warning = add_option( 'my_option', 'my_value', '', 'YES' ); // the uppercase versions of the valid values are not supported.
$warning = update_option( 'my_option', 'new_value' );
$warning = update_option( 'my_option', 'new_value', 'auto' );
$warning = update_option( 'my_option', 'new_value', 'auto-on' );
$warning = update_option( 'my_option', 'new_value', 'auto-off' );
$warning = update_option( 'my_option', 'new_value', 'invalid_value' );
$warning = update_option( value: 'new_value', option: 'my_option' );
$warning = update_option( value: 'new_value', autoload: 'auto', option: 'my_option' );
$warning = update_option( $option_name, get_option( $option_name ) );
$warning = wp_set_options_autoload( array( 'option1', 'option2' ), 'invalid_value' );
$warning = wp_set_options_autoload( array( 'option1' ), null );
$warning = wp_set_option_autoload( 'option1', 'invalid_value' );
$warning = wp_set_option_autoload( 'option1', \NuLL );
$warning = wp_set_option_autoload_values( ['option1' => null] );
$warning = wp_set_option_autoload_values(
    [
        'option1' => '',
        'option2' => 'true',
        'option3' => "false",
        'option4' => array('some', 'array'),
        'option5' => ['some', 'array'],
        'option6' => 'YES',
        'option7' => 'invalid_value',
        'option8' => 'auto',
        'option9' => 'auto-on',
        'option10' => 'auto-off',
        'option11',
        /* some comment */ 'option12' /* some comment */,
    ]
);

// Incorrect function calls, should be flagged by the sniff.
$incorrect_but_warning = add_option();
$incorrect_but_warning = update_option();
$incorrect_but_warning = update_option( 'my_option' );
$incorrect_but_warning = update_option( autoloader: false, value: 'my_value', option: 'my_option' ); // Typo in the autoload parameter.
$incorrect_but_warning = add_option( autoload: 'yes', 'my_option' );
$incorrect_but_warning = add_option( autoload: 'no' );

/*
 * These should all be flagged with a warning and auto-fixed.
 */
$autofix_warning = add_option( 'my_option', 'my_value', '', 'yes' );
$autofix_warning = add_option( 'my_option', 'my_value', '', 'no' );
$autofix_warning = add_option( 'my_option', 'my_value', '', "yes" );
$autofix_warning = add_option( 'my_option', 'my_value', '', "no" );
$autofix_warning = add_option( 'my_option', 'my_value', '', 'on' );
$autofix_warning = add_option( 'my_option', 'my_value', '', 'off' );
$autofix_warning = add_option( 'my_option', 'my_value', '', /* weirdly placed comment */ 'off' /* weirdly placed comment */ );
$autofix_warning = add_option(
    'my_option',
    'my_value',
    '',
    // weirdly placed comment
    'off'
);
$autofix_warning = add_option(
    'my_option',
    'my_value',
    '',
    // phpcs:ignore Standard.Category.Sniff -- For test reasons.
    'off'
);
$autofix_warning = add_option( 'my_option', 'my_value', '','off');
$autofix_warning = add_option( value: 'my_value', deprecated: '', autoload: 'yes', option: 'my_option' );
$autofix_warning = update_option( 'my_option', 'new_value', 'yes' );
$autofix_warning = update_option( 'my_option', 'new_value', 'no' );
$autofix_warning = update_option( 'my_option', 'new_value', 'on' );
$autofix_warning = update_option( 'my_option', 'new_value', 'off' );
$autofix_warning = update_option( value: 'new_value', autoload: 'yes', option: 'my_option' );

$autofix_warning = wp_set_options_autoload( array( 'option1', 'option2' ), 'yes' );
$autofix_warning = wp_set_options_autoload( array( 'option1', 'option2' ), 'no' );
$autofix_warning = wp_set_option_autoload( 'option1', 'yes' );
$autofix_warning = wp_set_option_autoload( 'option1', 'no' );
$autofix_warning = WP_SET_OPTION_AUTOLOAD_VALUES(
    array(
        'option1' => 'yes',
        'option2' => 'no',
        'option3' => 'on',
        'option4' => 'off',
    )
);
$autofix_warning = Wp_Set_Option_Autoload_Values(
    options: array(
        'option1' => 'yes',
    )
);

$mixed_autoload_values = wp_set_option_autoload_values(
    array(
        'option1' => 'yes',
        'option2' => false,
        'option3' => 'on',
        'option4' => true,
        'option5' => $option_value,
        'option6' => 'auto',
    )
);
