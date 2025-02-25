<?php
// phpcs:set WordPress.Utils.I18nTextDomainFixer old_text_domain[] old-domain
// phpcs:set WordPress.Utils.I18nTextDomainFixer new_text_domain something-else

/*
 * Intentional parse error (nothing after opening parenthesis).
 * This should be the only test in this file.
 *
 * Test to document that `AbstractFunctionParameterSniff::is_targetted_token()` ignores unfinished
 * function calls. This test is added here as there are no dedicated tests for the WPCS abstract
 * classes. The WPCS abstract classes will be replaced with PHPCSUtils similar classes in the
 * future, so it is not worth creating dedicated tests at this point.
 */

__(

// phpcs:set WordPress.Utils.I18nTextDomainFixer old_text_domain[]
// phpcs:set WordPress.Utils.I18nTextDomainFixer new_text_domain false
