<?php
/**
 * Represents a PHP_CodeSniffer sniff for sniffing WordPress coding standards.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Represents a PHP_CodeSniffer sniff for sniffing WordPress coding standards.
 *
 * Provides a bootstrap for the sniffs, to reduce code duplication.
 *
 * @package WPCS\WordPressCodingStandards
 * @since   0.4.0
 */
abstract class WordPress_Sniff implements PHP_CodeSniffer_Sniff {

	/**
	 * List of the functions which verify nonces.
	 *
	 * @since 0.5.0
	 *
	 * @var array
	 */
	public static $nonceVerificationFunctions = array(
		'wp_verify_nonce'     => true,
		'check_admin_referer' => true,
		'check_ajax_referer'  => true,
	);

	/**
	 * Functions that escape values for display.
	 *
	 * @since 0.5.0
	 *
	 * @var array
	 */
	public static $escapingFunctions = array(
		'absint'               => true,
		'esc_attr__'           => true,
		'esc_attr_e'           => true,
		'esc_attr_x'           => true,
		'esc_attr'             => true,
		'esc_html__'           => true,
		'esc_html_e'           => true,
		'esc_html_x'           => true,
		'esc_html'             => true,
		'esc_js'               => true,
		'esc_sql'              => true,
		'esc_textarea'         => true,
		'esc_url_raw'          => true,
		'esc_url'              => true,
		'filter_input'         => true,
		'filter_var'           => true,
		'intval'               => true,
		'json_encode'          => true,
		'like_escape'          => true,
		'number_format'        => true,
		'rawurlencode'         => true,
		'sanitize_html_class'  => true,
		'sanitize_user_field'  => true,
		'tag_escape'           => true,
		'urlencode_deep'       => true,
		'urlencode'            => true,
		'wp_json_encode'       => true,
		'wp_kses_allowed_html' => true,
		'wp_kses_data'         => true,
		'wp_kses_post'         => true,
		'wp_kses'              => true,
	);

	/**
	 * Functions whose output is automatically escaped for display.
	 *
	 * @since 0.5.0
	 *
	 * @var array
	 */
	public static $autoEscapedFunctions = array(
		'allowed_tags'              => true,
		'bloginfo'                  => true,
		'body_class'                => true,
		'calendar_week_mod'         => true,
		'cancel_comment_reply_link' => true,
		'category_description'      => true,
		'checked'                   => true,
		'comment_author_email_link' => true,
		'comment_author_email'      => true,
		'comment_author_IP'         => true,
		'comment_author_link'       => true,
		'comment_author_rss'        => true,
		'comment_author_url_link'   => true,
		'comment_author_url'        => true,
		'comment_author'            => true,
		'comment_class'             => true,
		'comment_date'              => true,
		'comment_excerpt'           => true,
		'comment_form_title'        => true,
		'comment_form'              => true,
		'comment_id_fields'         => true,
		'comment_ID'                => true,
		'comment_reply_link'        => true,
		'comment_text_rss'          => true,
		'comment_text'              => true,
		'comment_time'              => true,
		'comment_type'              => true,
		'comments_link'             => true,
		'comments_number'           => true,
		'comments_popup_link'       => true,
		'comments_popup_script'     => true,
		'comments_rss_link'         => true,
		'count'                     => true,
		'delete_get_calendar_cache' => true,
		'disabled'                  => true,
		'do_shortcode'              => true,
		'do_shortcode_tag'          => true,
		'edit_bookmark_link'        => true,
		'edit_comment_link'         => true,
		'edit_post_link'            => true,
		'edit_tag_link'             => true,
		'get_archives_link'         => true,
		'get_attachment_link'       => true,
		'get_avatar'                => true,
		'get_bookmark_field'        => true,
		'get_bookmark'              => true,
		'get_calendar'              => true,
		'get_comment_author_link'   => true,
		'get_comment_date'          => true,
		'get_comment_time'          => true,
		'get_current_blog_id'       => true,
		'get_delete_post_link'      => true,
		'get_footer'                => true,
		'get_header'                => true,
		'get_search_form'           => true,
		'get_search_query'          => true,
		'get_sidebar'               => true,
		'get_template_part'         => true,
		'get_the_author_link'       => true,
		'get_the_author'            => true,
		'get_the_date'              => true,
		'get_the_post_thumbnail'    => true,
		'get_the_term_list'         => true,
		'get_the_title'             => true,
		'has_post_thumbnail'        => true,
		'is_attachment'             => true,
		'next_comments_link'        => true,
		'next_image_link'           => true,
		'next_post_link'            => true,
		'next_posts_link'           => true,
		'paginate_comments_links'   => true,
		'permalink_anchor'          => true,
		'post_class'                => true,
		'post_password_required'    => true,
		'post_type_archive_title'   => true,
		'posts_nav_link'            => true,
		'previous_comments_link'    => true,
		'previous_image_link'       => true,
		'previous_post_link'        => true,
		'previous_posts_link'       => true,
		'selected'                  => true,
		'single_cat_title'          => true,
		'single_month_title'        => true,
		'single_post_title'         => true,
		'single_tag_title'          => true,
		'single_term_title'         => true,
		'sticky_class'              => true,
		'tag_description'           => true,
		'term_description'          => true,
		'the_attachment_link'       => true,
		'the_author_link'           => true,
		'the_author_meta'           => true,
		'the_author_posts_link'     => true,
		'the_author_posts'          => true,
		'the_author'                => true,
		'the_category_rss'          => true,
		'the_category'              => true,
		'the_content_rss'           => true,
		'the_content'               => true,
		'the_date_xml'              => true,
		'the_date'                  => true,
		'the_excerpt_rss'           => true,
		'the_excerpt'               => true,
		'the_feed_link'             => true,
		'the_ID'                    => true,
		'the_meta'                  => true,
		'the_modified_author'       => true,
		'the_modified_date'         => true,
		'the_modified_time'         => true,
		'the_permalink'             => true,
		'the_post_thumbnail'        => true,
		'the_search_query'          => true,
		'the_shortlink'             => true,
		'the_tags'                  => true,
		'the_taxonomies'            => true,
		'the_terms'                 => true,
		'the_time'                  => true,
		'the_title_attribute'       => true,
		'the_title_rss'             => true,
		'the_title'                 => true,
		'vip_powered_wpcom'         => true,
		'walk_nav_menu_tree'        => true,
		'wp_attachment_is_image'    => true,
		'wp_dropdown_categories'    => true,
		'wp_dropdown_users'         => true,
		'wp_enqueue_script'         => true,
		'wp_generate_tag_cloud'     => true,
		'wp_get_archives'           => true,
		'wp_get_attachment_image'   => true,
		'wp_get_attachment_link'    => true,
		'wp_link_pages'             => true,
		'wp_list_authors'           => true,
		'wp_list_bookmarks'         => true,
		'wp_list_categories'        => true,
		'wp_list_comments'          => true,
		'wp_login_form'             => true,
		'wp_loginout'               => true,
		'wp_meta'                   => true,
		'wp_nav_menu'               => true,
		'wp_register'               => true,
		'wp_shortlink_header'       => true,
		'wp_shortlink_wp_head'      => true,
		'wp_tag_cloud'              => true,
		'wp_title'                  => true,
	);

	/**
	 * Functions that sanitize values.
	 *
	 * @since 0.5.0
	 *
	 * @var array
	 */
	public static $sanitizingFunctions = array(
		'_wp_handle_upload'          => true,
		'absint'                     => true,
		'array_key_exists'           => true,
		'esc_url_raw'                => true,
		'filter_input'               => true,
		'filter_var'                 => true,
		'hash_equals'                => true,
		'in_array'                   => true,
		'intval'                     => true,
		'is_array'                   => true,
		'is_email'                   => true,
		'number_format'              => true,
		'sanitize_bookmark_field'    => true,
		'sanitize_bookmark'          => true,
		'sanitize_email'             => true,
		'sanitize_file_name'         => true,
		'sanitize_html_class'        => true,
		'sanitize_key'               => true,
		'sanitize_meta'              => true,
		'sanitize_mime_type'         => true,
		'sanitize_option'            => true,
		'sanitize_sql_orderby'       => true,
		'sanitize_term_field'        => true,
		'sanitize_term'              => true,
		'sanitize_text_field'        => true,
		'sanitize_title_for_query'   => true,
		'sanitize_title_with_dashes' => true,
		'sanitize_title'             => true,
		'sanitize_user_field'        => true,
		'sanitize_user'              => true,
		'validate_file'              => true,
		'wp_handle_sideload'         => true,
		'wp_handle_upload'           => true,
		'wp_kses_allowed_html'       => true,
		'wp_kses_data'               => true,
		'wp_kses_post'               => true,
		'wp_kses'                    => true,
		'wp_parse_id_list'           => true,
		'wp_redirect'                => true,
		'wp_safe_redirect'           => true,
	);

	/**
	 * Sanitizing functions that implicitly unslash the data passed to them.
	 *
	 * @since 0.5.0
	 *
	 * @var array
	 */
	public static $unslashingSanitizingFunctions = array(
		'absint'       => true,
		'boolval'      => true,
		'intval'       => true,
		'is_array'     => true,
		'sanitize_key' => true,
	);

	/**
	 * Functions that format strings.
	 *
	 * These functions are often used for formatting values just before output, and
	 * it is common practice to escape the individual parameters passed to them as
	 * needed instead of escaping the entire result. This is especially true when the
	 * string being formatted contains HTML, which makes escaping the full result
	 * more difficult.
	 *
	 * @since 0.5.0
	 *
	 * @var array
	 */
	public static $formattingFunctions = array(
		'array_fill' => true,
		'ent2ncr'    => true,
		'implode'    => true,
		'join'       => true,
		'nl2br'      => true,
		'sprintf'    => true,
		'vsprintf'   => true,
		'wp_sprintf' => true,
	);

	/**
	 * Functions which print output incorporating the values passed to them.
	 *
	 * @since 0.5.0
	 *
	 * @var array
	 */
	public static $printingFunctions = array(
		'_deprecated_argument'    => true,
		'_deprecated_constructor' => true,
		'_deprecated_file'        => true,
		'_deprecated_function'    => true,
		'_deprecated_hook'        => true,
		'_doing_it_wrong'         => true,
		'_e'                      => true,
		'_ex'                     => true,
		'die'                     => true,
		'echo'                    => true,
		'exit'                    => true,
		'print'                   => true,
		'printf'                  => true,
		'trigger_error'           => true,
		'user_error'              => true,
		'vprintf'                 => true,
		'wp_die'                  => true,
		'wp_dropdown_pages'       => true,
	);

	/**
	 * Functions that escape values for use in SQL queries.
	 *
	 * @since 0.9.0
	 *
	 * @var array
	 */
	public static $SQLEscapingFunctions = array(
		'absint'      => true,
		'esc_sql'     => true,
		'intval'      => true,
		'like_escape' => true,
	);

	/**
	 * Functions whose output is automatically escaped for use in SQL queries.
	 *
	 * @since 0.9.0
	 *
	 * @var array
	 */
	public static $SQLAutoEscapedFunctions = array(
		'count' => true,
	);

	/**
	 * A list of functions that get data from the cache.
	 *
	 * @since 0.6.0
	 *
	 * @var array
	 */
	public static $cacheGetFunctions = array(
		'wp_cache_get' => true,
	);

	/**
	 * A list of functions that set data in the cache.
	 *
	 * @since 0.6.0
	 *
	 * @var array
	 */
	public static $cacheSetFunctions = array(
		'wp_cache_set' => true,
		'wp_cache_add' => true,
	);

	/**
	 * A list of functions that delete data from the cache.
	 *
	 * @since 0.6.0
	 *
	 * @var array
	 */
	public static $cacheDeleteFunctions = array(
		'wp_cache_delete' => true,
	);

	/**
	 * A list of functions that invoke WP hooks (filters/actions).
	 *
	 * @since 0.10.0
	 *
	 * @var array
	 */
	public static $hookInvokeFunctions = array(
		'do_action'                => true,
		'do_action_ref_array'      => true,
		'do_action_deprecated'     => true,
		'apply_filters'            => true,
		'apply_filters_ref_array'  => true,
		'apply_filters_deprecated' => true,
	);

	/**
	 * A list of functions that are used to interact with the WP plugins API.
	 *
	 * @since 0.10.0
	 *
	 * @var array <string function name> => <int position of the hook name argument in function signature>
	 */
	public static $hookFunctions = array(
		'has_filter'         => 1,
		'add_filter'         => 1,
		'remove_filter'      => 1,
		'remove_all_filters' => 1,
		'doing_filter'       => 1, // Hook name optional.
		'has_action'         => 1,
		'add_action'         => 1,
		'doing_action'       => 1, // Hook name optional.
		'did_action'         => 1,
		'remove_action'      => 1,
		'remove_all_actions' => 1,
		'current_filter'     => 0, // No hook name argument.
	);

	/**
	 * The current file being sniffed.
	 *
	 * @since 0.4.0
	 *
	 * @var PHP_CodeSniffer_File
	 */
	protected $phpcsFile;

	/**
	 * The list of tokens in the current file being sniffed.
	 *
	 * @since 0.4.0
	 *
	 * @var array
	 */
	protected $tokens;

	/**
	 * A list of superglobals that incorporate user input.
	 *
	 * @since 0.5.0
	 *
	 * @var string[]
	 */
	protected static $input_superglobals = array(
		'$_COOKIE',
		'$_GET',
		'$_FILES',
		'$_POST',
		'$_REQUEST',
		'$_SERVER',
	);

	/**
	 * Initialize the class for the current process.
	 *
	 * This method must be called by child classes before using many of the methods
	 * below.
	 *
	 * @since 0.4.0
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file currently being processed.
	 */
	protected function init( PHP_CodeSniffer_File $phpcsFile ) {
		$this->phpcsFile = $phpcsFile;
		$this->tokens    = $phpcsFile->getTokens();
	}

	/**
	 * Get the last pointer in a line.
	 *
	 * @since 0.4.0
	 *
	 * @param integer $stackPtr The position of the current token in the stack passed
	 *                          in $tokens.
	 *
	 * @return integer Position of the last pointer on that line.
	 */
	protected function get_last_ptr_on_line( $stackPtr ) {

		$tokens      = $this->tokens;
		$currentLine = $tokens[ $stackPtr ]['line'];
		$nextPtr     = ( $stackPtr + 1 );

		while ( isset( $tokens[ $nextPtr ] ) && $tokens[ $nextPtr ]['line'] === $currentLine ) {
			$nextPtr++;
			// Do nothing, we just want the last token of the line.
		}

		// We've made it to the next line, back up one to the last in the previous line.
		// We do this for micro-optimization of the above loop.
		$lastPtr = ( $nextPtr - 1 );

		return $lastPtr;
	}

	/**
	 * Find whitelisting comment.
	 *
	 * Comment must be at the end of the line, and use // format.
	 * It can be prefixed or suffixed with anything e.g. "foobar" will match:
	 * ... // foobar okay
	 * ... // WPCS: foobar whitelist.
	 *
	 * There is an exception, and that is when PHP is being interspersed with HTML.
	 * In that case, the comment should come at the end of the statement (right
	 * before the closing tag, ?>). For example:
	 *
	 * <input type="text" id="<?php echo $id; // XSS OK ?>" />
	 *
	 * @since 0.4.0
	 *
	 * @param string  $comment  Comment to find.
	 * @param integer $stackPtr The position of the current token in the stack passed
	 *                          in $tokens.
	 *
	 * @return boolean True if whitelisting comment was found, false otherwise.
	 */
	protected function has_whitelist_comment( $comment, $stackPtr ) {

		$end_of_line = $lastPtr = $this->get_last_ptr_on_line( $stackPtr );

		// There is a findEndOfStatement() method, but it considers more tokens than
		// we need to here.
		$end_of_statement = $this->phpcsFile->findNext( array( T_CLOSE_TAG, T_SEMICOLON ), $stackPtr );

		// Check at the end of the statement if it comes before - or is - the end of the line.
		if ( $end_of_statement <= $end_of_line ) {

			// If the statement was ended by a semicolon, we find the next non-
			// whitespace token. If the semicolon was left out and it was terminated
			// by an ending tag, we need to look backwards.
			if ( T_SEMICOLON === $this->tokens[ $end_of_statement ]['code'] ) {
				$lastPtr = $this->phpcsFile->findNext( T_WHITESPACE, ( $end_of_statement + 1 ), null, true );
			} else {
				$lastPtr = $this->phpcsFile->findPrevious( T_WHITESPACE, ( $end_of_statement - 1 ), null, true );
			}
		}

		$last = $this->tokens[ $lastPtr ];

		if ( T_COMMENT === $last['code'] ) {
			return preg_match( '#' . preg_quote( $comment ) . '#i', $last['content'] );
		} else {
			return false;
		}
	}

	/**
	 * Check if this variable is being assigned a value.
	 *
	 * E.g., $var = 'foo';
	 *
	 * Also handles array assignments to arbitrary depth:
	 *
	 * $array['key'][ $foo ][ something() ] = $bar;
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack. This must points to
	 *                      either a T_VARIABLE or T_CLOSE_SQUARE_BRACKET token.
	 *
	 * @return bool Whether the token is a variable being assigned a value.
	 */
	protected function is_assignment( $stackPtr ) {

		$tokens = $this->phpcsFile->getTokens();

		// Must be a variable or closing square bracket (see below).
		if ( ! in_array( $tokens[ $stackPtr ]['code'], array( T_VARIABLE, T_CLOSE_SQUARE_BRACKET ), true ) ) {
			return false;
		}

		$next_non_empty = $this->phpcsFile->findNext(
			PHP_CodeSniffer_Tokens::$emptyTokens
			, ( $stackPtr + 1 )
			, null
			, true
			, null
			, true
		);

		// No token found.
		if ( false === $next_non_empty ) {
			return false;
		}

		// If the next token is an assignment, that's all we need to know.
		if ( in_array( $tokens[ $next_non_empty ]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens, true ) ) {
			return true;
		}

		// Check if this is an array assignment, e.g., `$var['key'] = 'val';` .
		if ( T_OPEN_SQUARE_BRACKET === $tokens[ $next_non_empty ]['code'] ) {
			return $this->is_assignment( $tokens[ $next_non_empty ]['bracket_closer'] );
		}

		return false;
	}

	/**
	 * Check if this token has an associated nonce check.
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The position of the current token in the stack of tokens.
	 *
	 * @return bool
	 */
	protected function has_nonce_check( $stackPtr ) {

		/**
		 * A cache of the scope that we last checked for nonce verification in.
		 *
		 * @var array {
		 *      @var string   $file        The name of the file.
		 *      @var int      $start       The index of the token where the scope started.
		 *      @var int      $end         The index of the token where the scope ended.
		 *      @var bool|int $nonce_check The index of the token where an nonce check
		 *                                 was found, or false if none was found.
		 * }
		 */
		static $last;

		$start = 0;
		$end   = $stackPtr;

		$tokens = $this->phpcsFile->getTokens();

		// If we're in a function, only look inside of it.
		$f = $this->phpcsFile->getCondition( $stackPtr, T_FUNCTION );
		if ( $f ) {
			$start = $tokens[ $f ]['scope_opener'];
		}

		$in_isset = $this->is_in_isset_or_empty( $stackPtr );

		// We allow for isset( $_POST['var'] ) checks to come before the nonce check.
		// If this is inside an isset(), check after it as well, all the way to the
		// end of the scope.
		if ( $in_isset ) {
			$end = ( 0 === $start ) ? count( $tokens ) : $tokens[ $start ]['scope_closer'];
		}

		// Check if we've looked here before.
		$filename = $this->phpcsFile->getFilename();

		if (
			$filename === $last['file']
			&& $start === $last['start']
		) {

			if ( false !== $last['nonce_check'] ) {
				// If we have already found an nonce check in this scope, we just
				// need to check whether it comes before this token. It is OK if the
				// check is after the token though, if this was only a isset() check.
				return ( $in_isset || $last['nonce_check'] < $stackPtr );
			} elseif ( $end <= $last['end'] ) {
				// If not, we can still go ahead and return false if we've already
				// checked to the end of the search area.
				return false;
			}

			// We haven't checked this far yet, but we can still save work by
			// skipping over the part we've already checked.
			$start = $last['end'];
		} else {
			$last = array(
				'file'  => $filename,
				'start' => $start,
				'end'   => $end,
			);
		}

		// Loop through the tokens looking for nonce verification functions.
		for ( $i = $start; $i < $end; $i++ ) {

			// If this isn't a function name, skip it.
			if ( T_STRING !== $tokens[ $i ]['code'] ) {
				continue;
			}

			// If this is one of the nonce verification functions, we can bail out.
			if ( isset( self::$nonceVerificationFunctions[ $tokens[ $i ]['content'] ] ) ) {
				$last['nonce_check'] = $i;
				return true;
			}
		}

		// We're still here, so no luck.
		$last['nonce_check'] = false;

		return false;
	}

	/**
	 * Check if a token is inside of an isset() or empty() statement.
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return bool Whether the token is inside an isset() or empty() statement.
	 */
	protected function is_in_isset_or_empty( $stackPtr ) {

		if ( ! isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			return false;
		}

		end( $this->tokens[ $stackPtr ]['nested_parenthesis'] );
		$open_parenthesis = key( $this->tokens[ $stackPtr ]['nested_parenthesis'] );
		reset( $this->tokens[ $stackPtr ]['nested_parenthesis'] );

		return in_array( $this->tokens[ ( $open_parenthesis - 1 ) ]['code'], array( T_ISSET, T_EMPTY ), true );
	}

	/**
	 * Check if something is only being sanitized.
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return bool Whether the token is only within a sanitization.
	 */
	protected function is_only_sanitized( $stackPtr ) {

		// If it isn't being sanitized at all.
		if ( ! $this->is_sanitized( $stackPtr ) ) {
			return false;
		}

		// If this isn't set, we know the value must have only been casted, because
		// is_sanitized() would have returned false otherwise.
		if ( ! isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			return true;
		}

		// At this point we're expecting the value to have not been casted. If it
		// was, it wasn't *only* casted, because it's also in a function.
		if ( $this->is_safe_casted( $stackPtr ) ) {
			return false;
		}

		// The only parentheses should belong to the sanitizing function. If there's
		// more than one set, this isn't *only* sanitization.
		return ( count( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) === 1 );
	}

	/**
	 * Check if something is being casted to a safe value.
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return bool Whether the token being casted.
	 */
	protected function is_safe_casted( $stackPtr ) {

		// Get the last non-empty token.
		$prev = $this->phpcsFile->findPrevious(
			PHP_CodeSniffer_Tokens::$emptyTokens
			, ( $stackPtr - 1 )
			, null
			, true
		);

		// Check if it is a safe cast.
		return in_array( $this->tokens[ $prev ]['code'], array( T_INT_CAST, T_DOUBLE_CAST, T_BOOL_CAST ), true );
	}

	/**
	 * Check if something is being sanitized.
	 *
	 * @since 0.5.0
	 *
	 * @param int  $stackPtr        The index of the token in the stack.
	 * @param bool $require_unslash Whether to give an error if wp_unslash() isn't
	 *                              used on the variable before sanitization.
	 *
	 * @return bool Whether the token being sanitized.
	 */
	protected function is_sanitized( $stackPtr, $require_unslash = false ) {

		// First we check if it is being casted to a safe value.
		if ( $this->is_safe_casted( $stackPtr ) ) {
			return true;
		}

		// If this isn't within a function call, we know already that it's not safe.
		if ( ! isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			if ( $require_unslash ) {
				$this->add_unslash_error( $stackPtr );
			}
			return false;
		}

		// Get the function that it's in.
		$function_closer = end( $this->tokens[ $stackPtr ]['nested_parenthesis'] );
		$function_opener = key( $this->tokens[ $stackPtr ]['nested_parenthesis'] );
		$function        = $this->tokens[ ( $function_opener - 1 ) ];

		// If it is just being unset, the value isn't used at all, so it's safe.
		if ( T_UNSET === $function['code'] ) {
			return true;
		}

		// If this isn't a call to a function, it sure isn't sanitizing function.
		if ( T_STRING !== $function['code'] ) {
			if ( $require_unslash ) {
				$this->add_unslash_error( $stackPtr );
			}
			return false;
		}

		$functionName = $function['content'];

		// Check if wp_unslash() is being used.
		if ( 'wp_unslash' === $functionName ) {

			$is_unslashed    = true;
			$function_closer = prev( $this->tokens[ $stackPtr ]['nested_parenthesis'] );

			// If there is no other function being used, this value is unsanitized.
			if ( ! $function_closer ) {
				return false;
			}

			$function_opener = key( $this->tokens[ $stackPtr ]['nested_parenthesis'] );
			$functionName    = $this->tokens[ ( $function_opener - 1 ) ]['content'];

		} else {

			$is_unslashed = false;
		}

		// Arrays might be sanitized via array_map().
		if ( 'array_map' === $functionName ) {

			// Get the first parameter (name of function being used on the array).
			$mapped_function = $this->phpcsFile->findNext(
				PHP_CodeSniffer_Tokens::$emptyTokens
				, ( $function_opener + 1 )
				, $function_closer
				, true
			);

			// If we're able to resolve the function name, do so.
			if ( $mapped_function && T_CONSTANT_ENCAPSED_STRING === $this->tokens[ $mapped_function ]['code'] ) {
				$functionName = trim( $this->tokens[ $mapped_function ]['content'], '\'' );
			}
		}

		// If slashing is required, give an error.
		if ( ! $is_unslashed && $require_unslash && ! isset( self::$unslashingSanitizingFunctions[ $functionName ] ) ) {
			$this->add_unslash_error( $stackPtr );
		}

		// Check if this is a sanitizing function.
		return isset( self::$sanitizingFunctions[ $functionName ] );
	}

	/**
	 * Add an error for missing use of wp_unslash().
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 */
	public function add_unslash_error( $stackPtr ) {

		$this->phpcsFile->addError(
			'Missing wp_unslash() before sanitization.',
			$stackPtr,
			'MissingUnslash',
			array( $this->tokens[ $stackPtr ]['content'] )
		);
	}

	/**
	 * Get the index key of an array variable.
	 *
	 * E.g., "bar" in $foo['bar'].
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of the token in the stack.
	 *
	 * @return string|false The array index key whose value is being accessed.
	 */
	protected function get_array_access_key( $stackPtr ) {

		// Find the next non-empty token.
		$open_bracket = $this->phpcsFile->findNext(
			PHP_CodeSniffer_Tokens::$emptyTokens,
			( $stackPtr + 1 ),
			null,
			true
		);

		// If it isn't a bracket, this isn't an array-access.
		if ( T_OPEN_SQUARE_BRACKET !== $this->tokens[ $open_bracket ]['code'] ) {
			return false;
		}

		$key = $this->phpcsFile->getTokensAsString(
			( $open_bracket + 1 )
			, ( $this->tokens[ $open_bracket ]['bracket_closer'] - $open_bracket - 1 )
		);

		return trim( $key );
	}

	/**
	 * Check if the existence of a variable is validated with isset() or empty().
	 *
	 * When $in_condition_only is false, (which is the default), this is considered
	 * valid:
	 *
	 * ```php
	 * if ( isset( $var ) ) {
	 *     // Do stuff, like maybe return or exit (but could be anything)
	 * }
	 *
	 * foo( $var );
	 * ```
	 *
	 * When it is true, that would be invalid, the use of the variable must be within
	 * the scope of the validating condition, like this:
	 *
	 * ```php
	 * if ( isset( $var ) ) {
	 *     foo( $var );
	 * }
	 * ```
	 *
	 * @since 0.5.0
	 *
	 * @param int    $stackPtr          The index of this token in the stack.
	 * @param string $array_key         An array key to check for ("bar" in $foo['bar']).
	 * @param bool   $in_condition_only Whether to require that this use of the
	 *                                  variable occur within the scope of the
	 *                                  validating condition, or just in the same
	 *                                  scope as it (default).
	 *
	 * @return bool Whether the var is validated.
	 */
	protected function is_validated( $stackPtr, $array_key = null, $in_condition_only = false ) {

		if ( $in_condition_only ) {
			/*
			   This is a stricter check, requiring the variable to be used only
			   within the validation condition.
			 */

			// If there are no conditions, there's no validation.
			if ( empty( $this->tokens[ $stackPtr ]['conditions'] ) ) {
				return false;
			}

			$conditions = $this->tokens[ $stackPtr ]['conditions'];
			end( $conditions ); // Get closest condition.
			$conditionPtr = key( $conditions );
			$condition    = $this->tokens[ $conditionPtr ];

			if ( ! isset( $condition['parenthesis_opener'] ) ) {

				$this->phpcsFile->addError(
					'Possible parse error, condition missing open parenthesis.',
					$conditionPtr,
					'IsValidatedMissingConditionOpener'
				);

				return false;
			}

			$scope_start = $condition['parenthesis_opener'];
			$scope_end   = $condition['parenthesis_closer'];

		} else {
			/*
			   We are are more loose, requiring only that the variable be validated
			   in the same function/file scope as it is used.
			 */

			// Check if we are in a function.
			$function = $this->phpcsFile->findPrevious( T_FUNCTION, $stackPtr );

			// If so, we check only within the function, otherwise the whole file.
			if ( false !== $function && $stackPtr < $this->tokens[ $function ]['scope_closer'] ) {
				$scope_start = $this->tokens[ $function ]['scope_opener'];
			} else {
				$scope_start = 0;
			}

			$scope_end = $stackPtr;
		}

		for ( $i = ( $scope_start + 1 ); $i < $scope_end; $i++ ) {

			if ( ! in_array( $this->tokens[ $i ]['code'], array( T_ISSET, T_EMPTY, T_UNSET ), true ) ) {
				continue;
			}

			$issetOpener = $this->phpcsFile->findNext( T_OPEN_PARENTHESIS, $i );
			$issetCloser = $this->tokens[ $issetOpener ]['parenthesis_closer'];

			// Look for this variable. We purposely stomp $i from the parent loop.
			for ( $i = ( $issetOpener + 1 ); $i < $issetCloser; $i++ ) {

				if ( T_VARIABLE !== $this->tokens[ $i ]['code'] ) {
					continue;
				}

				// If we're checking for a specific array key (ex: 'hello' in
				// $_POST['hello']), that mush match too.
				if ( $array_key && $this->get_array_access_key( $i ) !== $array_key ) {
					continue;
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * Check whether a variable is being compared to another value.
	 *
	 * E.g., $var === 'foo', 1 <= $var, etc.
	 *
	 * Also recognizes `switch ( $var )`.
	 *
	 * @since 0.5.0
	 *
	 * @param int $stackPtr The index of this token in the stack.
	 *
	 * @return bool Whether this is a comparison.
	 */
	protected function is_comparison( $stackPtr ) {

		// We first check if this is a switch statement (switch ( $var )).
		if ( isset( $this->tokens[ $stackPtr ]['nested_parenthesis'] ) ) {
			$close_parenthesis = end( $this->tokens[ $stackPtr ]['nested_parenthesis'] );

			if (
				isset( $this->tokens[ $close_parenthesis ]['parenthesis_owner'] )
				&& T_SWITCH === $this->tokens[ $this->tokens[ $close_parenthesis ]['parenthesis_owner'] ]['code']
			) {
				return true;
			}
		}

		// Find the previous non-empty token. We check before the var first because
		// yoda conditions are usually expected.
		$previous_token = $this->phpcsFile->findPrevious(
			PHP_CodeSniffer_Tokens::$emptyTokens,
			( $stackPtr - 1 ),
			null,
			true
		);

		if ( in_array( $this->tokens[ $previous_token ]['code'], PHP_CodeSniffer_Tokens::$comparisonTokens, true ) ) {
			return true;
		}

		// Maybe the comparison operator is after this.
		$next_token = $this->phpcsFile->findNext(
			PHP_CodeSniffer_Tokens::$emptyTokens,
			( $stackPtr + 1 ),
			null,
			true
		);

		// This might be an opening square bracket in the case of arrays ($var['a']).
		while ( T_OPEN_SQUARE_BRACKET === $this->tokens[ $next_token ]['code'] ) {

			$next_token = $this->phpcsFile->findNext(
				PHP_CodeSniffer_Tokens::$emptyTokens,
				( $this->tokens[ $next_token ]['bracket_closer'] + 1 ),
				null,
				true
			);
		}

		if ( in_array( $this->tokens[ $next_token ]['code'], PHP_CodeSniffer_Tokens::$comparisonTokens, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check what type of 'use' statement a token is part of.
	 *
	 * The T_USE token has multiple different uses:
	 *
	 * 1. In a closure: function () use ( $var ) {}
	 * 2. In a class, to import a trait: use Trait_Name
	 * 3. In a namespace, to import a class: use Some\Class;
	 *
	 * This function will check the token and return 'closure', 'trait', or 'class',
	 * based on which of these uses the use is being used for.
	 *
	 * @since 0.7.0
	 *
	 * @param int $stackPtr The position of the token to check.
	 *
	 * @return string The type of use.
	 */
	protected function get_use_type( $stackPtr ) {

		// USE keywords inside closures.
		$next = $this->phpcsFile->findNext( T_WHITESPACE, ( $stackPtr + 1 ), null, true );

		if ( T_OPEN_PARENTHESIS === $this->tokens[ $next ]['code'] ) {
			return 'closure';
		}

		// USE keywords for traits.
		if ( $this->phpcsFile->hasCondition( $stackPtr, array( T_CLASS, T_TRAIT ) ) ) {
			return 'trait';
		}

		// USE keywords for classes to import to a namespace.
		return 'class';
	}

	/**
	 * Get the interpolated variable names from a string.
	 *
	 * Check if '$' is followed by a valid variable name, and that it is not preceded by an escape sequence.
	 *
	 * @since 0.9.0
	 *
	 * @param string $string A T_DOUBLE_QUOTED_STRING token.
	 *
	 * @return array Variable names (without '$' sigil).
	 */
	protected function get_interpolated_variables( $string ) {
		$variables = array();
		if ( preg_match_all( '/(?P<backslashes>\\\\*)\$(?P<symbol>\w+)/', $string, $match_sets, PREG_SET_ORDER ) ) {
			foreach ( $match_sets as $matches ) {
				if ( ( strlen( $matches['backslashes'] ) % 2 ) === 0 ) {
					$variables[] = $matches['symbol'];
				}
			}
		}
		return $variables;
	} // end get_interpolated_variables()

}
