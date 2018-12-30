<?php
// phpcs:set WordPress.Utils.I18nTextDomainFixer old_text_domain[] text-domain,other-text-domain,third-text-domain
// phpcs:set WordPress.Utils.I18nTextDomainFixer new_text_domain something-else

/*
 * The test cases in this file have a variety of whitespace for indentation and around
 * function call parameters to test that the whitespace is not unnecessarily affected
 * by the fixer.
 *
 * This test case file also tests passing several domains to the `old_text_domain` property.
 */

/*
 * Correct text domain, no replacement needed.
 */
load_textdomain( 'something-else', '/path/to/file.mo' );
load_plugin_textdomain( 'something-else', false, '/languages/' );
load_muplugin_textdomain( 'something-else', '/languages/' );
load_theme_textdomain( 'something-else', '/path/to/languages/' );
load_child_theme_textdomain( 'something-else', '/path/to/languages/' );
unload_textdomain( 'something-else' );

__( $text, 'something-else' );
_e( $text, 'something-else' );
_x( $text, $context, 'something-else' );
_ex( $text, $context, 'something-else' );
_n( $single, $plural, $number, 'something-else' );
_nx(
	$single,
	$plural,
	$number,
	$context,
	'something-else',
); // PHP 7.3 trailing comma in function call.
_n_noop( $singular, $plural, 'something-else' );
_nx_noop( $singular, $plural, $context, 'something-else' );
translate_nooped_plural( $nooped_plural, $count, 'something-else' );
_c( $text, 'something-else' );

esc_html__( $text, 'something-else' );
esc_html_e( $text, 'something-else' );
esc_html_x($text, $context, 'something-else');
esc_attr__  ( /* comment */ $text, 'something-else' );
esc_attr_e( $text, 'something-else' );
esc_attr_x( $text, $context, 'something-else' );

is_textdomain_loaded( "something-else" );
get_translations_for_domain( 'something-else' );

translate( $text, 'something-else' );
translate_with_gettext_context( $text, $context, 'something-else' );

_load_textdomain_just_in_time('something-else');
_get_path_to_translation_from_lang_dir( 'something-else' );
_get_path_to_translation( 'something-else', true );

/*
 * Situations which are not our concern and should be ignored.
 */
$this->translate( $text, 'third-text-domain' );
self::translate( $text, 'third-text-domain' );
MyNameSpace\Second\translate( $text, 'third-text-domain' );

__( $text, "a $interpolated string" );
_e( $text, 'concatenated' . 'string' );
_x( $text, $context, $variableTextdomain );
_ex( $text, $context, CONSTANT_TEXTDOMAIN );

/*
 * Text domains *not* in the whitelisted "old" domain list should be ignored.
 */
load_plugin_textdomain( 'tgmpa', false, '/languages/' );
_e( $text, 'default' );
is_textdomain_loaded( 'some-other-plugin' );

/*
 * Incorrect text domain, should be replaced.
 */
load_textdomain( 'text-domain', '/path/to/file.mo' );
load_plugin_textdomain( 'text-domain', false, '/languages/' );
load_muplugin_textdomain( 'other-text-domain', '/languages/' );
load_theme_textdomain( 'third-text-domain', '/path/to/languages/' );
load_child_theme_textdomain( 'text-domain', '/path/to/languages/' );
unload_textdomain( 'text-domain' );

__( $text, 'text-domain' );
_e( $text, 'text-domain' );
_x( $text, $context, 'text-domain' );
_ex( $text, $context, 'third-text-domain' );
_n($single,$plural,$number,'text-domain');
_nx( $single, $plural, $number, $context, 'text-domain' );
_n_noop( $singular, $plural, 'other-text-domain' );
_nx_noop( $singular,
	$plural,
	$context, 'text-domain' );
translate_nooped_plural( $nooped_plural, $count, "text-domain" );
_c( $text, 'text-domain' );

esc_html__( $text, 'third-text-domain' );
esc_html_e( $text, 'text-domain' );
esc_html_x($text, $context, 'text-domain');
esc_attr__( $text, 'text-domain' );
esc_attr_e( $text, 'other-text-domain' );
esc_attr_x(
	$text,
	$context,
	'text-domain'
);

is_textdomain_loaded( 'text-domain' );
get_translations_for_domain( 'other-text-domain' );

translate( $text, 'third-text-domain' );
translate_with_gettext_context(
	$text,
	$context,
	'text-domain',
);

_load_textdomain_just_in_time( 'third-text-domain' );
_get_path_to_translation_from_lang_dir( 'text-domain' );
_get_path_to_translation( 'other-text-domain', true );

/*
 * Missing text domain, should be added.
 */
load_textdomain();
load_plugin_textdomain( /* everything missing, but has a comment */ );
load_muplugin_textdomain(    );
load_theme_textdomain();
		load_child_theme_textdomain(
		);
unload_textdomain();

__( $text );
_e( $text );
_x( $text, $context, ); // PHP 7.3 trailing comma in function call.
_ex( $text, $context );
_n( $single, $plural, $number );
_nx( $single, $plural, $number, $context );
_n_noop( $singular, $plural );
_nx_noop( $singular, $plural, $context );
translate_nooped_plural( $nooped_plural, $count );
_c( $text );

esc_html__($text);
esc_html_e( $text );
esc_html_x(
    $text,
    $context,
); // PHP 7.3 trailing comma in multi-line function call.
esc_attr__( $text);
esc_attr_e($text );
esc_attr_x(
	$text,
	$context );

is_textdomain_loaded(
);
get_translations_for_domain();

translate( $text);
translate_with_gettext_context( $text, $context);

_load_textdomain_just_in_time();
_get_path_to_translation_from_lang_dir();
_get_path_to_translation();

/*
 * Missing text domain and preceeding args, only throw warning.
 */
__();
_e(   /* comment */  );
_x(    );
_ex();
_n( $single, $plural );
_nx( $single, $plural );
_n_noop($singular);
_nx_noop(
);
translate_nooped_plural( $nooped_plural );
_c();

esc_html__();
esc_html_e(  );
esc_html_x(
    $text,
); // PHP 7.3 trailing comma in multi-line function call.
esc_attr__();
esc_attr_e();
esc_attr_x(
	$text,
);

translate();
translate_with_gettext_context( $text);

/*
 * Additional deprecated functions.
 */
_nc( $single, $plural ); // Warning.
__ngettext( $singular, $plural, $number ); // Error.
__ngettext_noop( $singular, $plural, 'other-text-domain' ); // Error.
translate_with_context( $text, 'third-text-domain' ); // Error.

// phpcs:set WordPress.Utils.I18nTextDomainFixer old_text_domain[]
// phpcs:set WordPress.Utils.I18nTextDomainFixer new_text_domain false
