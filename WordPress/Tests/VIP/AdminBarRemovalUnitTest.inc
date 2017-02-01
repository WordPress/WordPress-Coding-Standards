<?php

add_filter( 'show_admin_bar', '__return_false' ); // Bad.
add_filter( 'show_admin_bar', '__return_true' ); // Ok.

show_admin_bar( false ); // Bad.
show_admin_bar( true ); // Ok.

add_filter( 'show_admin_bar', 'my_own_return_false' ); // Bad.

// @codingStandardsChangeSetting WordPress.VIP.AdminBarRemoval remove_only false
add_filter( 'show_admin_bar', '__return_true' ); // Bad.
show_admin_bar( true ); // Bad.
// @codingStandardsChangeSetting WordPress.VIP.AdminBarRemoval remove_only true

// Testing T_CONSTANT_ENCAPSED_STRING.
echo '<style type="text/css">
#wpadminbar {
	visibility: hidden; /* Bad. */
	display: none; /* Bad. */
	opacity: 0; /* Bad. */
}
</style>';

// Various text before/after combinations.
echo '<style type="text/css">.show-admin-bar { visibility: hidden; }</style>'; // Bad.

echo 'Some text about .show-admin-bar before a style tag <style type="text/css">.nothing-here { display: ' . 'none' . '; }</style>'; // Ok.
echo '<style type="text/css">.nothing-here { display: ' . 'none' . '; }</style>Some text about .show-admin-bar after a style tag'; // Ok.

echo '<style type="text/css">';
echo '.show-admin-bar { visibility: hidden; }'; // Bad.
echo '</style>';

// Testing T_DOUBLE_QUOTED_STRING.
echo "
<style type=\"text/css\">
	.show-admin-bar {
		visibility: $hidden; /* Ok, value not 'hidden'. */
	}
</style>";

// Testing T_HEREDOC.
$style = <<<EOT
<style type="text/css">
	.show-admin-bar {
		visibility: $hidden; /* Ok, value not 'hidden'. */
	}
</style>
EOT;

// Testing T_NOWDOC.
$style = <<<'EOT'
<style type="text/css">
	.show-admin-bar {
		visibility: hidden; /* Bad. */
		display: none; /* Bad. */
		opacity: 0; /* Bad. */
	}
</style>
EOT;

// Testing T_INLINE_HTML
?>

<style type="text/css">
#wpadminbar {
	visibility: hidden; /* Bad. */
	display: none; /* Bad. */
	opacity: 0; /* Bad. */
}
#not-wpadminbar {
	visibility: hidden; /* OK. */
	display: none; /* OK. */
	opacity: 0; /* OK. */
}
</style>

<style type="text/css">
	.show-admin-bar {
		visibility: hidden; /* Bad. */
		display: none; /* Bad. */
		opacity: 0; /* Bad. */
	}
</style>

<?php
// @codingStandardsChangeSetting WordPress.VIP.AdminBarRemoval remove_only false
$style = <<<EOT
<style type="text/css">
	.show-admin-bar {
		visibility: $hidden; /* Bad. */
	}
	.my-admin-bar {
		visibility: $hidden; /* OK. */
	}
</style>
EOT;
?>

<style type="text/css">
	.show-admin-bar {
		visibility: visible; /* Bad. */
		display: block; /* Bad. */
		opacity: 1; /* Bad. */
	}
</style>
/* @codingStandardsChangeSetting WordPress.VIP.AdminBarRemoval remove_only true */
