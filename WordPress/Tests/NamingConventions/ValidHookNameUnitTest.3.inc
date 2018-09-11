<?php

// @codingStandardsChangeSetting WordPress.NamingConventions.ValidHookName additionalWordDelimiters -/.

// These should now be ok.
do_action( "admin_head-$hook_suffix" );
do_action( 'admin_head.media-upload_popup' );
apply_filters( "bulk_actions-{$this->screen->id}", $this->_actions );
apply_filters( "current_theme/supports-{$feature}", true, $args, $_wp_theme_features[$feature] );

// These should still give warnings.
do_action( "admin_head*$hook_suffix" ); // Warning - use underscore.
do_action( 'admin_head&media+upload_popup' ); // Warning - use underscore.
apply_filters( "bulk_actions {$this->screen->id}", $this->_actions ); // Warning - use underscore.
apply_filters( "current_theme#supports-{$feature}", true, $args, $_wp_theme_features[$feature] ); // Warning - use underscore.

// @codingStandardsChangeSetting WordPress.NamingConventions.ValidHookName additionalWordDelimiters _
