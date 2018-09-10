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
