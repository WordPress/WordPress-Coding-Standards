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
class WordPress_Sniffs_XSS_EscapeOutputSniff implements PHP_CodeSniffer_Sniff
{

	public static $autoEscapedFunctions = array(
		'allowed_tags',
		'bloginfo',
		'body_class',
		'calendar_week_mod',
		'cancel_comment_reply_link',
		'category_description',
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
	);

	public static $okTokenContentSequences = array(
		array( '$this', '->', 'get_field_id' ),
		array( '$this', '->', 'get_field_name' ),
	);

	public static $sanitizingFunctions = array(
		'absint',
		'balanceTags',
		'esc_attr',
		'esc_attr__',
		'esc_attr_e',
		'esc_html',
		'esc_html__',
		'esc_html_e',
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
		'wp_kses',
		'wp_kses_allowed_html',
		'wp_kses_data',
		'wp_kses_post',
		'wp_redirect',
		'wp_rel_nofollow',
		'wp_safe_redirect',
	);

	public $needSanitizingFunctions = array( // Mostly locatization functions: http://codex.wordpress.org/Function_Reference#Localization
		'__',
		'_e',
		'_ex',
		'_n',
		'_ngettext',
		'_nx',
		'_x',
	);


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
	 * @todo Allow T_CONSTANT_ENCAPSED_STRING?
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr )
	{
		$tokens = $phpcsFile->getTokens();

		// If function, not T_ECHO nor T_PRINT
		if ( $tokens[$stackPtr]['code'] == T_STRING ) {
			// Skip if it is a function but is not of the printing functions ( self::needSanitizingFunctions )
			if ( ! in_array( $tokens[$stackPtr]['content'], $this->needSanitizingFunctions ) ) {
				return;
			}

			$stackPtr++; // Ignore the starting bracket
		}

		// Ensure that the next token is a whitespace.
		$stackPtr++;
		if ( $tokens[$stackPtr]['code'] === T_WHITESPACE ) {
			$stackPtr++;
		}

		// Checking for the ignore comment, ex: //xss ok
		$isAtEndOfStatement = false;
		$commentOkRegex     = '/xss\W*(ok|pass|clear|whitelist)/i';
		$tokensCount        = count( $tokens );
		for ( $i = $stackPtr; $i < $tokensCount; $i++ ) {
			if ( $tokens[$i]['code'] === T_SEMICOLON ) {
				$isAtEndOfStatement = true;
			}

			if ( $isAtEndOfStatement === true && in_array( $tokens[$i]['code'], array( T_SEMICOLON, T_WHITESPACE, T_COMMENT ) ) === false ) {
				break;
			}

			preg_match( $commentOkRegex, $tokens[$i]['content'], $matches );
			if ( ( $tokens[$i]['code'] === T_COMMENT ) && ( empty( $matches ) === false ) ) {
				return;
			}
		}


		// looping through echo'd components
		$watch = true;
		for ( $i = $stackPtr; $i < count( $tokens ); $i++ ) {

			foreach ( self::$okTokenContentSequences as $sequence ) {
				if ( $sequence[0] === $tokens[ $i ]['content'] ) {
					$token_string = join( '', array_map(
						array( $this, 'get_content_from_token' ),
						array_slice( $tokens, $i, count( $sequence ) )
					) );
					if ( $token_string === join( '', $sequence ) ) {
						return;
					}
				}
			}

			// End processing if found the end of statement
			if ( $tokens[$i]['code'] == T_SEMICOLON ) {
				return;
			}

			// Ignore whitespaces
			if ( $tokens[$i]['code'] == T_WHITESPACE )
				continue;

			// Wake up on concatenation characters, another part to check
			if ( in_array( $tokens[$i]['code'], array( T_STRING_CONCAT ) ) ) {
				$watch = true;
				continue;
			}

			if ( $watch === false )
				continue;

			$watch = false;

			// Allow T_CONSTANT_ENCAPSED_STRING eg: echo 'Some String';
			if ( in_array( $tokens[$i]['code'], array( T_CONSTANT_ENCAPSED_STRING ) ) ) {
				continue;
			}

			// Allow int/double/bool casted variables
			if ( in_array( $tokens[$i]['code'], array( T_INT_CAST, T_DOUBLE_CAST, T_BOOL_CAST ) ) ) {
				continue;
			}

			// Now check that next token is a function call.
			if ( in_array( $tokens[$i]['code'], array( T_STRING ) ) === false ) {
				$phpcsFile->addError( "Expected next thing to be a escaping function, not '%s'", $i, null, $tokens[$i]['content'] );
				continue;
			}

			// This is a function
			else {
				$functionName = $tokens[$i]['content'];
				if (
					in_array( $functionName, self::$autoEscapedFunctions ) === false
					&&
					in_array( $functionName, self::$sanitizingFunctions ) === false
					) {

					$phpcsFile->addError( "Expected a sanitizing function (see Codex for 'Data Validation'), but instead saw '%s'", $i, null, $tokens[$i]['content'] );
				}

				// Skip pointer to after the function
				if ( $_pos = $phpcsFile->findNext( array( T_OPEN_PARENTHESIS ), $i, null, null, null, true ) ) {
					$i = $tokens[$_pos]['parenthesis_closer'];
				}
				continue;
			}
		}

	}//end process()


	private function get_content_from_token( $token ) {
		return $token['content'];
	}

}//end class

?>
