<?php
// phpcs:set WordPress.Utils.I18nTextDomainFixer new_text_domain

// Testing behaviour when the `new_text_domain` property is not set.

load_textdomain( 'something-else', '/path/to/file.mo' );
__( $text, 'something-else' );

// phpcs:set WordPress.Utils.I18nTextDomainFixer new_text_domain false
