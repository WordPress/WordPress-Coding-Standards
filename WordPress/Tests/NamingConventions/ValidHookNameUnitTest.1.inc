<?php

$this->do_action( 'someAction' ); // Ok - not WP do_action.
SomeClass::do_action( 'someAction' ); // Ok - not WP do_action.
prefix_do_action( 'someAction' ); // Ok - not WP do_action.

// Check for incorrect word separators.
do_action( "admin_head-$hook_suffix" ); // Warning - use underscore.
do_action( 'admin_head.media.upload_popup' ); // Warning - use underscore.
apply_filters( "bulk_actions {$this->screen->id}", $this->_actions ); // Warning - use underscore.
apply_filters( "current_theme/supports-{$feature}", true, $args, $_wp_theme_features[$feature] ); // Warning - use underscore.

// Simple strings.
do_action( "adminHead" ); // Error - use lowercase.
do_action_ref_array( 'ADMINHEAD', array( $variable ) ); // Error - use lowercase.
apply_filters( 'adminHead', $variable ); // Error - use lowercase.
apply_filters_ref_array( 'ADMINHEAD', array( $variable ) ); // Error - use lowercase.

// Variable hooks.
do_action( $Hook_name ); // Ok.
do_action( "{$Hook_Name}" ); // Ok.

// Compound hook names.
do_action( 'admin_head_' . $Type . '_action' ); // ok.
do_action( 'admin_head_' .  get_ID() . '_action' ); // Ok.
do_action( 'admin_head_' . $post->ID . '_action' ); // Ok.

do_action( 'admin_Head_' . $Type . '_Action' ); // Error - use lowercase.
do_action( 'admin_Head_' .  get_ID() . '_Action' ); // Error - use lowercase.
do_action( 'admin_Head_' . $post->ID . '_Action' ); // Error - use lowercase.

do_action(
	'admin_Head_' . $type,
	$variable
); // Error - use lowercase.

// More complex strings.
do_action( "admin_head_$Post" ); // Ok.
do_action( "admin_head_$Post[1]_action" ); // Ok.
do_action( "admin_head_$Post[Test]_action" ); // Ok.
do_action( "admin_head_${Post}_action" ); // Ok.
do_action( "admin_head_$Post->ID" ); // Ok.
do_action( "admin_head_{$Post}" ); // Ok.
do_action( "admin_head_{$Post['Key']}_action" ); // Ok.
do_action( "admin_head_{$Post[1][2]}_action" ); // Ok.
do_action( "admin_head_{$post->ID}_action" ); // Ok.
do_action( "admin_head_{$obj->Values[3]->name}_action" ); // Ok.
do_action( "admin_head_{${$Name}}_action" ); // Ok.
do_action( "admin_head_{$foo->{$baz[1]}}_action" ); // Ok.
do_action( "admin_head_{${getName()}}_action" ); // Ok.
do_action( "admin_head_{${$object->getName()}}_action" ); // Ok.

do_action( "admin_Head_$Post" ); // Error - use lowercase.
do_action( "admin_Head_$Post[1]_Action" ); // Error - use lowercase.
do_action( "admin_Head_$Post[Test]_Action" ); // Error - use lowercase.
do_action( "admin_Head_${Post}_Action" ); // Error - use lowercase.
do_action( "admin_Head_$Post->ID" ); // Error - use lowercase.
do_action( "admin_Head_{$Post}" );  // Error - use lowercase.
do_action( "admin_Head_{$Post['Key']}_Action" ); // Error - use lowercase.
do_action( "admin_Head_{$Post[1][2]}_Action" ); // Error - use lowercase.
do_action( "admin_Head_{$post->ID}_Action" ); // Error - use lowercase.
do_action( "admin_Head_{$obj->Values[3]->name}_Action" ); // Error - use lowercase.
do_action( "admin_Head_{${$Name}}_Action" ); // Error - use lowercase.
do_action( "admin_Head_{$foo->{$baz[1]}}_Action" ); // Error - use lowercase.
do_action( "admin_Head_{${getName()}}_Action" ); // Error - use lowercase.
do_action( "admin_Head_{${$object->getName()}}_Action" ); // Error - use lowercase.

do_action( "admin_Head_$Post admin_Head_$Post" ); // Error - use lowercase + warning about space.
do_action( "admin_Head_$Post[1]_Action_$Post[1]_Action" ); // Error - use lowercase.
do_action( "admin_Head_$Post[Test]_Action_$Post[Test]_Action" ); // Error - use lowercase.
do_action( "admin_Head_${Post}_Action_${Post}_Action" ); // Error - use lowercase.
do_action( "admin_Head_$Post->ID admin_Head_$Post->ID" ); // Error - use lowercase + warning about space.
do_action( "admin_Head_{$Post}_admin_Head_{$Post}" );  // Error - use lowercase.
do_action( "admin_Head_{$Post['Key']}_Action_{$Post['Key']}_Action" ); // Error - use lowercase.
do_action( "admin_Head_{$Post[1][2]}_Action_{$Post[1][2]}_Action" ); // Error - use lowercase.
do_action( "admin_Head_{$post->ID}_Action_{$post->ID}_Action" ); // Error - use lowercase.
do_action( "admin_Head_{$obj->Values[3]->name}-Action_{$obj->Values[3]->name}_Action" ); // Error - use lowercase + warning about dash.
do_action( "admin_Head_{${$Name}}_Action_{${$Name}}_Action" ); // Error - use lowercase.
do_action( "admin_Head_{$foo->{$baz[1]}}_Action_{$foo->{$baz[1]}}_Action" ); // Error - use lowercase.
do_action( "admin_Head_{${getName()}}_Action_{${getName()}}_Action" ); // Error - use lowercase.
do_action( "admin_Head_{${$object->getName()}}_Action_{${$object->getName()}}_Action" ); // Error - use lowercase.

// Make sure that deprecated hook names are ignored for this sniff.
do_action_deprecated( "admin_Head_$Post admin_Head_$Post" ); // Ok.
apply_filters_deprecated( "admin_Head_$Post->ID admin_Head_$Post->ID" ); // Ok.

// Ignore array keys.
do_action( 'prefix_block_' . $block['blockName'] ); // Ok.
do_action( 'prefix_block_' . $block  [  'blockName'  ] . '_More_hookname' ); // Error - use lowercase (second part of the hook name).
do_action( "prefix_block_{$block['blockName']}" ); // Ok.

// Don't include comments in the suggestion.
do_action(
	// phpcs:ignore Stnd.Cat.Sniff -- For reasons.
	'prefix_hook-name' /* comment */
);

// Ignore text strings when passed as parameters to a function call. WPCS #2055.
$value = apply_filters(
	get_filter_name( 'UPPERCASE', 'wrong-delimiter' ),
	$value,
	$attributes
);

// ... but do not ignore text strings in arbitrary parentheses.
$value = apply_filters(
	( $name ? 'UPPERCASE' : 'wrong-delimiter' ),
	$value,
	$attributes
);

// Test handling of more complex embedded variables and expressions.
do_action( "admin_head_${Foo->{$Baz}}_action_$Post->ID" ); // OK.
do_action( "admin_Head_${Foo?->{$Baz}}_Action_{$Post?->ID}_Bla" ); // Error - use lowercase.
do_action( "admin_Head_${Foo->{"${'A'}"}}-Action_$Post[A]_Bla" ); // Error - use lowercase + warning about dash.

// Safeguard that variable function calls are handled correctly.
do_action( 'admin_head_' .  $fn( 'UPPERCASE', 'wrong-delimiter' ) . '_action' ); // Ok.

// Safeguard support for PHP 8.0+ named parameters.
do_action_ref_array( hook: 'My-Hook', args: $args ); // OK. Well, not really, but using the wrong parameter name, so not our concern.
do_action_ref_array( args: $args, hook_name: 'my_hook', ); // OK.
do_action_ref_array( args: $args, hook_name: 'My-Hook', ); // Error - use lowercase + warning about dash.
