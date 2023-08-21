<?php
// phpcs:set WordPress.Utils.I18nTextDomainFixer old_text_domain[]
// phpcs:set WordPress.Utils.I18nTextDomainFixer new_text_domain something-else

// Testing behaviour when the `old_text_domain` property is not set.

load_textdomain( 'old-domain', '/path/to/file.mo' );
__( $text, 'old-domain' );

// phpcs:set WordPress.Utils.I18nTextDomainFixer old_text_domain[]
// phpcs:set WordPress.Utils.I18nTextDomainFixer new_text_domain false
