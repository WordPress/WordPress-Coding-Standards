<?php
/**
 * Squiz_Sniffs_XSS_EscapeOutputSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Weston Ruter <weston@x-team.com>
 */

/**
 * Verifies that all outputted strings are sanitized
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Weston Ruter <weston@x-team.com>
 * @link     http://codex.wordpress.org/Data_Validation Data Validation on WordPress Codex
 */
class WordPress_Sniffs_XSS_EscapeOutputSniff extends WordPress_Sniff
{

	public $customAutoEscapedFunctions = array();

	public $customSanitizingFunctions = array();

	public $customPrintingFunctions = array();

	public static $autoEscapedFunctions = array(
		'allowed_tags',
		'bloginfo',
		'body_class',
		'calendar_week_mod',
		'cancel_comment_reply_link',
		'category_description',
		'checked',
		'comment_ID',
		'comment_author',
		'comment_author_IP',
		'comment_author_email',
		'comment_author_email_link',
		'comment_author_link',
		'comment_author_rss',
		'comment_author_url',
		'comment_author_url_link',
		'comment_class',
		'comment_date',
		'comment_excerpt',
		'comment_form',
		'comment_form_title',
		'comment_id_fields',
		'comment_reply_link',
		'comment_text',
		'comment_text_rss',
		'comment_time',
		'comment_type',
		'comments_link',
		'comments_number',
		'comments_popup_link',
		'comments_popup_script',
		'comments_rss_link',
		'delete_get_calendar_cache',
		'disabled',
		'do_shortcode_tag',
		'edit_bookmark_link',
		'edit_comment_link',
		'edit_post_link',
		'edit_tag_link',
		'get_archives_link',
		'get_attachment_link',
		'get_avatar',
		'get_bookmark',
		'get_bookmark_field',
		'get_calendar',
		'get_comment_author_link',
		'get_comment_date',
		'get_comment_time',
		'get_current_blog_id',
		'get_delete_post_link',
		'get_footer',
		'get_header',
		'get_search_form',
		'get_search_query',
		'get_sidebar',
		'get_template_part',
		'get_the_author',
		'get_the_author_link',
		'get_the_date',
		'get_the_post_thumbnail',
		'get_the_term_list',
		'get_the_title',
		'has_post_thumbnail',
		'is_attachment',
		'next_comments_link',
		'next_image_link',
		'next_post_link',
		'next_posts_link',
		'paginate_comments_links',
		'permalink_anchor',
		'post_class',
		'post_password_required',
		'post_type_archive_title',
		'posts_nav_link',
		'previous_comments_link',
		'previous_image_link',
		'previous_post_link',
		'previous_posts_link',
		'selected',
		'single_cat_title',
		'single_month_title',
		'single_post_title',
		'single_tag_title',
		'single_term_title',
		'sticky_class',
		'tag_description',
		'term_description',
		'the_ID',
		'the_attachment_link',
		'the_author',
		'the_author_link',
		'the_author_meta',
		'the_author_posts',
		'the_author_posts_link',
		'the_category',
		'the_category_rss',
		'the_content',
		'the_content_rss',
		'the_date',
		'the_date_xml',
		'the_excerpt',
		'the_excerpt_rss',
		'the_feed_link',
		'the_meta',
		'the_modified_author',
		'the_modified_date',
		'the_modified_time',
		'the_permalink',
		'the_post_thumbnail',
		'the_search_query',
		'the_shortlink',
		'the_tags',
		'the_taxonomies',
		'the_terms',
		'the_time',
		'the_title',
		'the_title_attribute',
		'the_title_rss',
		'vip_powered_wpcom',
		'walk_nav_menu_tree',
		'wp_attachment_is_image',
		'wp_dropdown_categories',
		'wp_dropdown_users',
		'wp_enqueue_script',
		'wp_generate_tag_cloud',
		'wp_get_archives',
		'wp_get_attachment_image',
		'wp_get_attachment_link',
		'wp_link_pages',
		'wp_list_authors',
		'wp_list_bookmarks',
		'wp_list_categories',
		'wp_list_comments',
		'wp_login_form',
		'wp_loginout',
		'wp_meta',
		'wp_nav_menu',
		'wp_register',
		'wp_shortlink_header',
		'wp_shortlink_wp_head',
		'wp_tag_cloud',
		'wp_title',
		'checked',
	);

	public static $sanitizingFunctions = array(
		'absint',
		'balanceTags',
		'esc_attr',
		'esc_attr__',
		'esc_attr_e',
		'esc_attr_x',
		'esc_html',
		'esc_html__',
		'esc_html_e',
		'esc_html_x',
		'esc_js',
		'esc_sql',
		'esc_textarea',
		'esc_url',
		'esc_url_raw',
		'filter_input',
		'filter_var',
		'intval',
		'is_email',
		'json_encode',
		'like_escape',
		'rawurlencode',
		'sanitize_bookmark',
		'sanitize_bookmark_field',
		'sanitize_email',
		'sanitize_file_name',
		'sanitize_html_class',
		'sanitize_key',
		'sanitize_meta',
		'sanitize_mime_type',
		'sanitize_option',
		'sanitize_sql_orderby',
		'sanitize_term',
		'sanitize_term_field',
		'sanitize_text_field',
		'sanitize_title',
		'sanitize_title_for_query',
		'sanitize_title_with_dashes',
		'sanitize_user',
		'sanitize_user_field',
		'tag_escape',
		'urlencode',
		'urlencode_deep',
		'validate_file',
		'wp_json_encode',
		'wp_kses',
		'wp_kses_allowed_html',
		'wp_kses_data',
		'wp_kses_post',
		'wp_parse_id_list',
		'wp_redirect',
		'wp_rel_nofollow',
		'wp_safe_redirect',
		'number_format',
		'ent2ncr',
	);

	/**
	 * Functions which print output incorporating the values passed to them.
	 *
	 * @var array
	 */
	public static $printingFunctions = array(
		'_deprecated_argument',
		'_deprecated_function',
		'_deprecated_file',
		'_doing_it_wrong',
		'_e',
		'_ex',
		'printf',
		'vprintf',
		'trigger_error',
		'user_error',
		'wp_die',
	);

	/**
	 * Printing functions that incorporate unsafe values.
	 *
	 * @var array
	 */
	public static $unsafePrintingFunctions = array(
		'_e' => 'esc_html_e() or esc_attr_e()',
		'_ex' => 'esc_html_ex() or esc_attr_ex()',
	);

	/**
	 * Functions that format strings.
	 *
	 * These functions are often used for formatting translation strings, and it is
	 * common practice to escape the individual parameters passed to them as needed
	 * instead of escaping the entire result. This is especially true when the string
	 * being formatted contains HTML, which makes escaping the full result more
	 * difficult.
	 *
	 * @since 0.4.0
	 *
	 * @var array
	 */
	public static $formattingFunctions = array(
		'sprintf',
		'vsprintf',
		'wp_sprintf',
	);

	/**
	 * Whether the custom functions were added to the default lists yet.
	 *
	 * @var bool
	 */
	public static $addedCustomFunctions = false;

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
			T_ECHO,
			T_PRINT,
			T_EXIT,
			T_STRING,
		);

	}//end register()


	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @return int|void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr )
	{
		// Merge any custom functions with the defaults, if we haven't already.
		if ( ! self::$addedCustomFunctions ) {
			self::$sanitizingFunctions = array_merge( self::$sanitizingFunctions, $this->customSanitizingFunctions );
			self::$autoEscapedFunctions = array_merge( self::$autoEscapedFunctions, $this->customAutoEscapedFunctions );
			self::$printingFunctions = array_merge( self::$printingFunctions, $this->customPrintingFunctions );
			self::$addedCustomFunctions = true;
		}

		$this->init( $phpcsFile );
		$tokens = $phpcsFile->getTokens();

		$function = $tokens[ $stackPtr ]['content'];

		// Find the opening parenthesis (if present; T_ECHO might not have it).
		$open_paren = $phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, $stackPtr + 1, null, true );

		// If function, not T_ECHO nor T_PRINT
		if ( $tokens[$stackPtr]['code'] == T_STRING ) {
			// Skip if it is a function but is not of the printing functions ( self::printingFunctions )
			if ( ! in_array( $tokens[$stackPtr]['content'], self::$printingFunctions ) ) {
				return;
			}

			if ( isset( $tokens[ $open_paren ]['parenthesis_closer'] ) ) {
				$end_of_statement = $tokens[ $open_paren ]['parenthesis_closer'];
			}

			// These functions only need to have the first argument escaped.
			if ( in_array( $function, array( 'trigger_error', 'user_error' ) ) ) {
				$end_of_statement = $phpcsFile->findEndOfStatement( $open_paren + 1 );
			}
		}

		// Checking for the ignore comment, ex: //xss ok
		if ( $this->has_whitelist_comment( 'xss', $stackPtr ) ) {
			return;
		}

		if ( isset( $end_of_statement, self::$unsafePrintingFunctions[ $function ] ) ) {
			$error = $phpcsFile->addError( "Expected next thing to be an escaping function (like %s), not '%s'", $stackPtr, 'UnsafePrintingFunction', array( self::$unsafePrintingFunctions[ $function ], $function ) );

			// If the error was reported, don't bother checking the function's arguments.
			if ( $error ) {
				return $end_of_statement;
			}
		}

		$ternary = false;

		// This is already determined if this is a function and not T_ECHO.
		if ( ! isset( $end_of_statement ) ) {

			$end_of_statement = $phpcsFile->findNext( array( T_SEMICOLON, T_CLOSE_TAG ), $stackPtr );
			$last_token = $phpcsFile->findPrevious( PHP_CodeSniffer_Tokens::$emptyTokens, $end_of_statement - 1, null, true );

			// Check for the ternary operator. We only need to do this here if this
			// echo is lacking parenthesis. Otherwise it will be handled below.
			if ( T_OPEN_PARENTHESIS !== $tokens[ $open_paren ]['code'] || T_CLOSE_PARENTHESIS !== $tokens[ $last_token ]['code'] ) {

				$ternary = $phpcsFile->findNext( T_INLINE_THEN, $stackPtr, $end_of_statement );

				// If there is a ternary skip over the part before the ?. However, if
				// there is a closing parenthesis ending the statement, we only do
				// this when the opening parenthesis comes after the ternary. If the
				// ternary is within the parentheses, it will be handled in the loop.
				if (
					$ternary
					&& (
						T_CLOSE_PARENTHESIS !== $tokens[ $last_token ]['code']
						|| $ternary < $tokens[ $last_token ]['parenthesis_opener']
					)
				) {
					$stackPtr = $ternary;
				}
			}
		}

		// Ignore the function itself.
		$stackPtr++;

		$in_cast = false;

		// looping through echo'd components
		$watch = true;
		for ( $i = $stackPtr; $i < $end_of_statement; $i++ ) {

			// Ignore whitespaces and comments.
			if ( in_array( $tokens[ $i ]['code'], array( T_WHITESPACE, T_COMMENT ) ) ) {
				continue;
			}

			if ( T_OPEN_PARENTHESIS === $tokens[ $i ]['code'] ) {

				if ( $in_cast ) {

					// Skip to the end of a function call if it has been casted to a safe value.
					$i       = $tokens[ $i ]['parenthesis_closer'];
					$in_cast = false;

				} else {

					// Skip over the condition part of a ternary (i.e., to after the ?).
					$ternary = $phpcsFile->findNext( T_INLINE_THEN, $i, $tokens[ $i ]['parenthesis_closer'] );

					if ( $ternary ) {

						$next_paren = $phpcsFile->findNext( T_OPEN_PARENTHESIS, $i, $tokens[ $i ]['parenthesis_closer'] );

						// We only do it if the ternary isn't within a subset of parentheses.
						if ( ! $next_paren || $ternary > $tokens[ $next_paren ]['parenthesis_closer'] ) {
							$i = $ternary;
						}
					}
				}

				continue;
			}

			// Handle arrays for those functions that accept them.
			if ( $tokens[ $i ]['code'] === T_ARRAY ) {
				$i++; // Skip the opening parenthesis.
				continue;
			}

			if ( in_array( $tokens[ $i ]['code'], array( T_DOUBLE_ARROW, T_CLOSE_PARENTHESIS ) ) ) {
				continue;
			}

			// Handle magic constants for debug functions.
			if ( in_array( $tokens[ $i ]['code'], array( T_METHOD_C, T_FUNC_C, T_FILE, T_CLASS_C ) ) ) {
				continue;
			}

			// Wake up on concatenation characters, another part to check
			if ( in_array( $tokens[$i]['code'], array( T_STRING_CONCAT ) ) ) {
				$watch = true;
				continue;
			}

			// Wake up after a ternary else (:).
			if ( $ternary && in_array( $tokens[$i]['code'], array( T_INLINE_ELSE ) ) ) {
				$watch = true;
				continue;
			}

			// Wake up for commas.
			if ( $tokens[ $i ]['code'] === T_COMMA ) {
				$in_cast = false;
				$watch = true;
				continue;
			}

			if ( $watch === false )
				continue;

			// Allow T_CONSTANT_ENCAPSED_STRING eg: echo 'Some String';
			// Also T_LNUMBER, e.g.: echo 45; exit -1;
			if ( in_array( $tokens[$i]['code'], array( T_CONSTANT_ENCAPSED_STRING, T_LNUMBER, T_MINUS ) ) ) {
				continue;
			}

			$watch = false;

			// Allow int/double/bool casted variables
			if ( in_array( $tokens[$i]['code'], array( T_INT_CAST, T_DOUBLE_CAST, T_BOOL_CAST ) ) ) {
				$in_cast = true;
				continue;
			}

			// Now check that next token is a function call.
			if ( in_array( $tokens[$i]['code'], array( T_STRING ) ) === false ) {
				$phpcsFile->addError( "Expected next thing to be a escaping function, not '%s'", $i, 'OutputNotEscaped', $tokens[$i]['content'] );
				continue;
			}

			// This is a function
			else {
				$functionName = $tokens[$i]['content'];

				$is_formatting_function = in_array( $functionName, self::$formattingFunctions );

				if (
					! $is_formatting_function
					&&
					in_array( $functionName, self::$autoEscapedFunctions ) === false
					&&
					in_array( $functionName, self::$sanitizingFunctions ) === false
					) {

					$phpcsFile->addError( "Expected a sanitizing function (see Codex for 'Data Validation'), but instead saw '%s'", $i, 'OutputNotSanitized', $tokens[$i]['content'] );
				}

				// Skip pointer to after the function
				if ( $_pos = $phpcsFile->findNext( array( T_OPEN_PARENTHESIS ), $i, null, null, null, true ) ) {

					// If this is a formatting function we just skip over the opening
					// parenthesis. Otherwise we skip all the way to the closing.
					if ( $is_formatting_function ) {
						$i = $_pos + 1;
						$watch = true;
					} else {
						$i = $tokens[ $_pos ]['parenthesis_closer'];
					}
				}
				continue;
			}
		}

		return $end_of_statement;

	}//end process()

}//end class

?>
