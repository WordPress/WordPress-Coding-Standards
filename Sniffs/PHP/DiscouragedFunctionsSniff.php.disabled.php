<?php
/**
 * WordPress_Sniffs_PHP_DiscouragedFunctionsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    John Godley
 */

if (class_exists('Generic_Sniffs_PHP_ForbiddenFunctionsSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class Generic_Sniffs_PHP_ForbiddenFunctionsSniff not found');
}

/**
 * WordPress_Sniffs_PHP_DiscouragedFunctionsSniff.
 *
 * Discourages the use of debug functions and suggests deprecated WordPress alternatives
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    John Godley
 */
class WordPress_Sniffs_PHP_DiscouragedFunctionsSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff
{

    /**
     * A list of forbidden functions with their alternatives.
     *
     * The value is NULL if no alternative exists. IE, the
     * function should just not be used.
     *
     * @var array(string => string|null)
     */
    protected $forbiddenFunctions = array(
                                     'error_log'     => null,
                                     'print_r'       => null,
									 'ereg_replace'  => 'preg_replace',
									 'ereg'          => null,
									 'eregi_replace' => 'preg_replace',
									 'split'         => null,
									 'spliti'        => null,
									 
									 // WordPress
									 'find_base_dir'            => 'WP_Filesystem::abspath',
									 'get_base_dir'             => 'WP_Filesystem::abspath',
									 'dropdown_categories'      => 'wp_link_category_checklist',
									 'dropdown_link_categories' => 'wp_link_category_checklist',
									 'get_link'                 => 'get_bookmark',
									 'get_catname'              => 'get_cat_name',
									 'register_globals'         => null,
									 'wp_setcookie'             => 'wp_set_auth_cookie',
									 'wp_get_cookie_login'      => null,
									 'wp_login'                 => 'wp_signon',
									 'get_the_attachment_link'  => 'wp_get_attachment_link',
									 'get_attachment_icon_src'  => 'wp_get_attachment_image_src',
									 'get_attachment_icon'      => 'wp_get_attachment_image',
									 'get_attachment_innerHTML' => 'wp_get_attachment_image',

                                    );

    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var bool
     */
    protected $error = false;

}//end class

?>
