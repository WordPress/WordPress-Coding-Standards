<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\Utils;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\Utils\FilePath;
use PHPCSUtils\Utils\GetTokensAsString;
use PHPCSUtils\Utils\PassedParameters;
use PHPCSUtils\Utils\TextStrings;
use WordPressCS\WordPress\AbstractFunctionParameterSniff;
use WordPressCS\WordPress\Helpers\RulesetPropertyHelper;

/**
 * Comprehensive I18n text domain fixer tool.
 *
 * This sniff can:
 * - Add missing text domains.
 * - Replace text domains based on an array of `old` values to a `new` value.
 *
 * Note: Without a user-defined configuration in a custom ruleset, this sniff will be ignored.
 *
 * @since 1.2.0
 */
final class I18nTextDomainFixerSniff extends AbstractFunctionParameterSniff {

	/**
	 * A list of tokenizers this sniff supports.
	 *
	 * @since 1.2.0
	 *
	 * @var array
	 */
	public $supportedTokenizers = array(
		'PHP',
		'CSS',
	);

	/**
	 * Old text domain(s) to replace.
	 *
	 * @since 1.2.0
	 *
	 * @var string[]
	 */
	public $old_text_domain;

	/**
	 * New text domain.
	 *
	 * @since 1.2.0
	 *
	 * @var string
	 */
	public $new_text_domain = '';

	/**
	 * The group name for this group of functions.
	 *
	 * @since 1.2.0
	 *
	 * @var string
	 */
	protected $group_name = 'i18nfixer';

	/**
	 * The WP Internationalization related functions to target for the replacements.
	 *
	 * @since 1.2.0
	 * @since 3.0.0 The format of the value has changed from a numerically indexed
	 *              array containing parameter positions to an array with the parameter
	 *              position as the index and the parameter name as value.
	 *
	 * @var array<string, array<string, int|string>> Function name as key, array with target
	 *                                               parameter and name as value.
	 */
	protected $target_functions = array(
		'load_textdomain' => array(
			'position' => 1,
			'name'     => 'domain',
		),
		'load_plugin_textdomain' => array(
			'position' => 1,
			'name'     => 'domain',
		),
		'load_muplugin_textdomain' => array(
			'position' => 1,
			'name'     => 'domain',
		),
		'load_theme_textdomain' => array(
			'position' => 1,
			'name'     => 'domain',
		),
		'load_child_theme_textdomain' => array(
			'position' => 1,
			'name'     => 'domain',
		),
		'load_script_textdomain' => array(
			'position' => 2,
			'name'     => 'domain',
		),
		'unload_textdomain' => array(
			'position' => 1,
			'name'     => 'domain',
		),

		'__' => array(
			'position' => 2,
			'name'     => 'domain',
		),
		'_e' => array(
			'position' => 2,
			'name'     => 'domain',
		),
		'_x' => array(
			'position' => 3,
			'name'     => 'domain',
		),
		'_ex' => array(
			'position' => 3,
			'name'     => 'domain',
		),
		'_n' => array(
			'position' => 4,
			'name'     => 'domain',
		),
		'_nx' => array(
			'position' => 5,
			'name'     => 'domain',
		),
		'_n_noop' => array(
			'position' => 3,
			'name'     => 'domain',
		),
		'_nx_noop' => array(
			'position' => 4,
			'name'     => 'domain',
		),
		'translate_nooped_plural' => array(
			'position' => 3,
			'name'     => 'domain',
		),

		'esc_html__' => array(
			'position' => 2,
			'name'     => 'domain',
		),
		'esc_html_e' => array(
			'position' => 2,
			'name'     => 'domain',
		),
		'esc_html_x' => array(
			'position' => 3,
			'name'     => 'domain',
		),
		'esc_attr__' => array(
			'position' => 2,
			'name'     => 'domain',
		),
		'esc_attr_e' => array(
			'position' => 2,
			'name'     => 'domain',
		),
		'esc_attr_x' => array(
			'position' => 3,
			'name'     => 'domain',
		),

		'is_textdomain_loaded' => array(
			'position' => 1,
			'name'     => 'domain',
		),
		'get_translations_for_domain' => array(
			'position' => 1,
			'name'     => 'domain',
		),

		// Deprecated functions.
		'_c' => array(
			'position' => 2,
			'name'     => 'domain',
		),
		'_nc' => array(
			'position' => 4,
			'name'     => 'domain',
		),
		'__ngettext' => array(
			'position' => 4,
			'name'     => 'domain',
		),
		'__ngettext_noop' => array(
			'position' => 3,
			'name'     => 'domain',
		),
		'translate_with_context' => array(
			'position' => 2,
			'name'     => 'domain',
		),

		// Shouldn't be used by plugins/themes.
		'translate' => array(
			'position' => 2,
			'name'     => 'domain',
		),
		'translate_with_gettext_context' => array(
			'position' => 3,
			'name'     => 'domain',
		),

		// WP private functions. Shouldn't be used by plugins/themes.
		'_load_textdomain_just_in_time' => array(
			'position' => 1,
			'name'     => 'domain',
		),
		'_get_path_to_translation_from_lang_dir' => array(
			'position' => 1,
			'name'     => 'domain',
		),
		'_get_path_to_translation' => array(
			'position' => 1,
			'name'     => 'domain',
		),
	);

	/**
	 * Whether a valid new text domain was found.
	 *
	 * @since 1.2.0
	 *
	 * @var bool
	 */
	private $is_valid = false;

	/**
	 * The new text domain as validated.
	 *
	 * @since 1.2.0
	 *
	 * @var string
	 */
	private $validated_textdomain = '';

	/**
	 * Whether the plugin/theme header has been seen and fixed yet.
	 *
	 * @since 1.2.0
	 *
	 * @var bool
	 */
	private $header_found = false;

	/**
	 * Possible headers for a theme.
	 *
	 * @link https://developer.wordpress.org/themes/basics/main-stylesheet-style-css/
	 *
	 * @since 1.2.0
	 *
	 * @var array<string, bool> Array key is the header name, the value indicated whether it is a
	 *                          required (true) or optional (false) header.
	 */
	private $theme_headers = array(
		'Theme Name'        => true,
		'Theme URI'         => false,
		'Author'            => true,
		'Author URI'        => false,
		'Description'       => true,
		'Version'           => true,
		'Requires at least' => true,
		'Tested up to'      => true,
		'Requires PHP'      => true,
		'License'           => true,
		'License URI'       => true,
		'Text Domain'       => true,
		'Tags'              => false,
		'Domain Path'       => false,
	);

	/**
	 * Possible headers for a plugin.
	 *
	 * @link https://developer.wordpress.org/plugins/plugin-basics/header-requirements/
	 *
	 * @since 1.2.0
	 *
	 * @var array<string, bool> Array key is the header name, the value indicated whether it is a
	 *                          required (true) or optional (false) header.
	 */
	private $plugin_headers = array(
		'Plugin Name'       => true,
		'Plugin URI'        => false,
		'Description'       => false,
		'Version'           => false,
		'Requires at least' => false,
		'Requires PHP'      => false,
		'Author'            => false,
		'Author URI'        => false,
		'License'           => false,
		'License URI'       => false,
		'Text Domain'       => false,
		'Domain Path'       => false,
		'Network'           => false,
		'Update URI'        => false,
	);

	/**
	 * Regex template to match theme/plugin headers.
	 *
	 * @since 1.2.0
	 *
	 * @var string
	 */
	private $header_regex_template = '`^(?:\s*(?:(?:\*|//)\s*)?)?(%s)\s*:\s*([^\r\n]+)`';

	/**
	 * Regex to match theme headers.
	 *
	 * Set from within the register() method.
	 *
	 * @since 1.2.0
	 *
	 * @var string
	 */
	private $theme_header_regex;

	/**
	 * Regex to match plugin headers.
	 *
	 * Set from within the register() method.
	 *
	 * @since 1.2.0
	 *
	 * @var string
	 */
	private $plugin_header_regex;

	/**
	 * The --tab-width CLI value that is being used.
	 *
	 * @since 1.2.0
	 *
	 * @var int
	 */
	private $tab_width = null;

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @since 1.2.0
	 *
	 * @return array
	 */
	public function register() {
		$headers                  = array_map(
			'preg_quote',
			array_keys( $this->theme_headers ),
			array_fill( 0, \count( $this->theme_headers ), '`' )
		);
		$this->theme_header_regex = sprintf( $this->header_regex_template, implode( '|', $headers ) );

		$headers                   = array_map(
			'preg_quote',
			array_keys( $this->plugin_headers ),
			array_fill( 0, \count( $this->plugin_headers ), '`' )
		);
		$this->plugin_header_regex = sprintf( $this->header_regex_template, implode( '|', $headers ) );

		$targets = parent::register();

		$targets[] = \T_DOC_COMMENT_OPEN_TAG;
		$targets[] = \T_COMMENT;

		return $targets;
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @since 1.2.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {
		// Check if the old/new properties are correctly set. If not, bow out.
		if ( ! is_string( $this->new_text_domain )
			|| '' === $this->new_text_domain
		) {
			return $this->phpcsFile->numTokens;
		}

		if ( isset( $this->old_text_domain ) ) {
			$this->old_text_domain = RulesetPropertyHelper::merge_custom_array( $this->old_text_domain, array(), false );

			if ( ! is_array( $this->old_text_domain )
				|| array() === $this->old_text_domain
			) {
				return $this->phpcsFile->numTokens;
			}
		}

		// Only validate and throw warning about the text domain once.
		if ( $this->new_text_domain !== $this->validated_textdomain ) {
			$this->is_valid             = false;
			$this->validated_textdomain = $this->new_text_domain;
			$this->header_found         = false;

			if ( 'default' === $this->new_text_domain ) {
				$this->phpcsFile->addWarning(
					'The "default" text domain is reserved for WordPress core use and should not be used by plugins or themes',
					0,
					'ReservedNewDomain',
					array( $this->new_text_domain )
				);

				return $this->phpcsFile->numTokens;
			}

			if ( preg_match( '`^[a-z0-9-]+$`', $this->new_text_domain ) !== 1 ) {
				$this->phpcsFile->addWarning(
					'The text domain should be a simple lowercase text string with words separated by dashes. "%s" appears invalid',
					0,
					'InvalidNewDomain',
					array( $this->new_text_domain )
				);

				return $this->phpcsFile->numTokens;
			}

			// If the text domain passed both validations, it should be considered valid.
			$this->is_valid = true;

		} elseif ( false === $this->is_valid ) {
			return $this->phpcsFile->numTokens;
		}

		if ( isset( $this->tab_width ) === false ) {
			$this->tab_width = Helper::getTabWidth( $this->phpcsFile );
		}

		if ( \T_DOC_COMMENT_OPEN_TAG === $this->tokens[ $stackPtr ]['code']
			|| \T_COMMENT === $this->tokens[ $stackPtr ]['code']
		) {
			// Examine for plugin/theme file header.
			return $this->process_comments( $stackPtr );

		} elseif ( isset( $this->phpcsFile->tokenizerType ) === false || 'CSS' !== $this->phpcsFile->tokenizerType ) {
			// Examine a T_STRING token in a PHP file as a function call.
			return parent::process_token( $stackPtr );
		}
	}


	/**
	 * Process the parameters of a matched function.
	 *
	 * @since 1.2.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched
	 *                                in lowercase.
	 * @param array  $parameters      Array with information about the parameters.
	 *
	 * @return void
	 */
	public function process_parameters( $stackPtr, $group_name, $matched_content, $parameters ) {
		$target_param_specs = $this->target_functions[ $matched_content ];
		$found_param        = PassedParameters::getParameterFromStack( $parameters, $target_param_specs['position'], $target_param_specs['name'] );

		if ( false === $found_param && 1 !== $target_param_specs['position'] ) {
			$error_msg  = 'Missing $domain arg';
			$error_code = 'MissingArgDomain';

			$has_named_params = false;
			foreach ( $parameters as $param ) {
				if ( isset( $param['name'] ) ) {
					$has_named_params = true;
					break;
				}
			}

			if ( false === $has_named_params && isset( $parameters[ ( $target_param_specs['position'] - 1 ) ] ) ) {
				$fix = $this->phpcsFile->addFixableError( $error_msg, $stackPtr, $error_code );

				if ( true === $fix ) {
					$start_previous = $parameters[ ( $target_param_specs['position'] - 1 ) ]['start'];
					$end_previous   = $parameters[ ( $target_param_specs['position'] - 1 ) ]['end'];
					if ( \T_WHITESPACE === $this->tokens[ $start_previous ]['code']
						&& $this->tokens[ $start_previous ]['content'] === $this->phpcsFile->eolChar
					) {
						// Replicate the new line + indentation of the previous item.
						$replacement = ',';
						for ( $i = $start_previous; $i <= $end_previous; $i++ ) {
							if ( \T_WHITESPACE !== $this->tokens[ $i ]['code'] ) {
								break;
							}

							if ( isset( $this->tokens[ $i ]['orig_content'] ) ) {
								$replacement .= $this->tokens[ $i ]['orig_content'];
							} else {
								$replacement .= $this->tokens[ $i ]['content'];
							}
						}

						$replacement .= "'{$this->new_text_domain}'";
					} else {
						$replacement = ", '{$this->new_text_domain}'";
					}

					if ( \T_WHITESPACE === $this->tokens[ $end_previous ]['code'] ) {
						$this->phpcsFile->fixer->addContentBefore( $end_previous, $replacement );
					} else {
						$this->phpcsFile->fixer->addContent( $end_previous, $replacement );
					}
				}
			} elseif ( true === $has_named_params ) {
				/*
				 * Function call using named arguments. For now, we will not auto-fix this.
				 *
				 * {@internal If we don't bother with indentation and such, this can be made
				 * auto-fixable by getting the 'end' of the last seen parameter and adding the
				 * domain parameter, with the 'domain: ' parameter label, after the last
				 * seen parameter.}
				 */
				$this->phpcsFile->addError( $error_msg, $stackPtr, $error_code );
			} else {
				$error_msg .= ' and preceding argument(s)';
				$error_code = 'MissingArgs';

				// Expected preceding param also missing, just throw the warning.
				$this->phpcsFile->addWarning( $error_msg, $stackPtr, $error_code );
			}

			return;
		}

		// Target parameter found. Let's examine it.
		$domain_param_start = $found_param['start'];
		$domain_param_end   = $found_param['end'];
		$domain_token       = null;

		for ( $i = $domain_param_start; $i <= $domain_param_end; $i++ ) {
			if ( isset( Tokens::$emptyTokens[ $this->tokens[ $i ]['code'] ] ) ) {
				continue;
			}

			if ( \T_CONSTANT_ENCAPSED_STRING !== $this->tokens[ $i ]['code'] ) {
				// Unexpected token found, not our concern. This is handled by the I18n sniff.
				return;
			}

			if ( isset( $domain_token ) ) {
				// More than one T_CONSTANT_ENCAPSED_STRING found, not our concern. This is handled by the I18n sniff.
				return;
			}

			$domain_token = $i;
		}

		// If we're still here, this means only one T_CONSTANT_ENCAPSED_STRING was found.
		$old_domain = TextStrings::stripQuotes( $this->tokens[ $domain_token ]['content'] );

		if ( ! \in_array( $old_domain, $this->old_text_domain, true ) ) {
			// Not a text domain targeted for replacement, ignore.
			return;
		}

		$fix = $this->phpcsFile->addFixableError(
			'Mismatched text domain. Expected \'%s\' but found \'%s\'',
			$domain_token,
			'TextDomainMismatch',
			array( $this->new_text_domain, $old_domain )
		);

		if ( true === $fix ) {
			$replacement = str_replace( $old_domain, $this->new_text_domain, $this->tokens[ $domain_token ]['content'] );
			$this->phpcsFile->fixer->replaceToken( $domain_token, $replacement );
		}
	}

	/**
	 * Process the function if no parameters were found.
	 *
	 * @since 1.2.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $group_name      The name of the group which was matched.
	 * @param string $matched_content The token content (function name) which was matched
	 *                                in lowercase.
	 *
	 * @return void
	 */
	public function process_no_parameters( $stackPtr, $group_name, $matched_content ) {
		$target_param = $this->target_functions[ $matched_content ];

		if ( 1 !== $target_param['position'] ) {
			// Only process the no param case as fixable if the text domain is expected to be the first parameter.
			$this->phpcsFile->addWarning( 'Missing $domain arg and preceding argument(s)', $stackPtr, 'MissingArgs' );
			return;
		}

		$opener = $this->phpcsFile->findNext( Tokens::$emptyTokens, ( $stackPtr + 1 ), null, true );
		if ( \T_OPEN_PARENTHESIS !== $this->tokens[ $opener ]['code']
			|| isset( $this->tokens[ $opener ]['parenthesis_closer'] ) === false
		) {
			// Parse error or live coding.
			return;
		}

		$fix = $this->phpcsFile->addFixableError( 'Missing $domain arg', $stackPtr, 'MissingArgDomain' );
		if ( true === $fix ) {
			$closer      = $this->tokens[ $opener ]['parenthesis_closer'];
			$replacement = " '{$this->new_text_domain}' ";

			if ( $this->tokens[ $opener ]['line'] !== $this->tokens[ $closer ]['line'] ) {
				$replacement = trim( $replacement );
				$addBefore   = ( $closer - 1 );
				if ( \T_WHITESPACE === $this->tokens[ ( $closer - 1 ) ]['code']
					&& $this->tokens[ $closer - 1 ]['line'] === $this->tokens[ $closer ]['line']
				) {
					if ( isset( $this->tokens[ ( $closer - 1 ) ]['orig_content'] ) ) {
						$replacement = $this->tokens[ ( $closer - 1 ) ]['orig_content']
							. "\t"
							. $replacement;
					} else {
						$replacement = $this->tokens[ ( $closer - 1 ) ]['content']
							. str_repeat( ' ', $this->tab_width )
							. $replacement;
					}

					--$addBefore;
				} else {
					// We don't know whether the code uses tabs or spaces, so presume WPCS, i.e. tabs.
					$replacement = "\t" . $replacement;
				}

				$replacement = $this->phpcsFile->eolChar . $replacement;

				$this->phpcsFile->fixer->addContentBefore( $addBefore, $replacement );

			} elseif ( \T_WHITESPACE === $this->tokens[ ( $closer - 1 ) ]['code'] ) {
				$this->phpcsFile->fixer->replaceToken( ( $closer - 1 ), $replacement );
			} else {
				$this->phpcsFile->fixer->addContentBefore( $closer, $replacement );
			}
		}
	}


	/**
	 * Process comments to find the plugin/theme headers.
	 *
	 * @since 1.2.0
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_comments( $stackPtr ) {
		if ( true === $this->header_found && ! defined( 'PHP_CODESNIFFER_IN_TESTS' ) ) {
			return;
		}

		$regex   = $this->plugin_header_regex;
		$headers = $this->plugin_headers;
		$type    = 'plugin';

		$file = FilePath::getName( $this->phpcsFile );
		if ( 'STDIN' === $file ) {
			return;
		}

		$file_name = basename( $file );
		if ( isset( $this->phpcsFile->tokenizerType ) && 'CSS' === $this->phpcsFile->tokenizerType ) {
			if ( 'style.css' !== $file_name && ! defined( 'PHP_CODESNIFFER_IN_TESTS' ) ) {
				// CSS files only need to be examined for the file header.
				return $this->phpcsFile->numTokens;
			}

			$regex   = $this->theme_header_regex;
			$headers = $this->theme_headers;
			$type    = 'theme';
		}

		$comment_details = array(
			'required_header_found' => false,
			'headers_found'         => 0,
			'text_domain_ptr'       => false,
			'text_domain_found'     => '',
			'last_header_ptr'       => false,
			'last_header_matches'   => array(),
		);

		if ( \T_COMMENT === $this->tokens[ $stackPtr ]['code'] ) {
			$block_comment = false;
			if ( substr( $this->tokens[ $stackPtr ]['content'], 0, 2 ) === '/*' ) {
				$block_comment = true;
			}

			$current = $stackPtr;
			do {
				if ( false === $comment_details['text_domain_ptr']
					|| false === $comment_details['required_header_found']
					|| $comment_details['headers_found'] < 3
				) {
					$comment_details = $this->examine_comment_line( $current, $regex, $headers, $comment_details );
				}

				if ( true === $block_comment && substr( $this->tokens[ $current ]['content'], -2 ) === '*/' ) {
					++$current;
					break;
				}

				++$current;
			} while ( isset( $this->tokens[ $current ] ) && \T_COMMENT === $this->tokens[ $current ]['code'] );

			$skip_to = $current;

		} else {
			if ( ! isset( $this->tokens[ $stackPtr ]['comment_closer'] ) ) {
				return;
			}

			$closer  = $this->tokens[ $stackPtr ]['comment_closer'];
			$current = $stackPtr;

			while ( ( $current = $this->phpcsFile->findNext( \T_DOC_COMMENT_STRING, ( $current + 1 ), $closer ) ) !== false ) {
				$comment_details = $this->examine_comment_line( $current, $regex, $headers, $comment_details );

				if ( false !== $comment_details['text_domain_ptr']
					&& true === $comment_details['required_header_found']
					&& $comment_details['headers_found'] >= 3
				) {
					// No need to look at the rest of the docblock.
					break;
				}
			}

			$skip_to = $closer;
		}

		// So, was this the plugin/theme header ?
		if ( true === $comment_details['required_header_found']
			&& $comment_details['headers_found'] >= 3
		) {
			$this->header_found = true;

			$text_domain_ptr   = $comment_details['text_domain_ptr'];
			$text_domain_found = $comment_details['text_domain_found'];

			if ( false !== $text_domain_ptr ) {
				if ( $this->new_text_domain !== $text_domain_found
					&& ( \in_array( $text_domain_found, $this->old_text_domain, true ) )
				) {
					$fix = $this->phpcsFile->addFixableError(
						'Mismatched text domain in %s header. Expected \'%s\' but found \'%s\'',
						$text_domain_ptr,
						'TextDomainHeaderMismatch',
						array(
							$type,
							$this->new_text_domain,
							$text_domain_found,
						)
					);

					if ( true === $fix ) {
						if ( isset( $this->tokens[ $text_domain_ptr ]['orig_content'] ) ) {
							$replacement = $this->tokens[ $text_domain_ptr ]['orig_content'];
						} else {
							$replacement = $this->tokens[ $text_domain_ptr ]['content'];
						}

						$replacement = str_replace( $text_domain_found, $this->new_text_domain, $replacement );

						$this->phpcsFile->fixer->replaceToken( $text_domain_ptr, $replacement );
					}
				}
			} else {
				$last_header_ptr     = $comment_details['last_header_ptr'];
				$last_header_matches = $comment_details['last_header_matches'];

				$fix = $this->phpcsFile->addFixableError(
					'Missing "Text Domain" in %s header',
					$last_header_ptr,
					'MissingTextDomainHeader',
					array( $type )
				);

				if ( true === $fix ) {
					if ( isset( $this->tokens[ $last_header_ptr ]['orig_content'] ) ) {
						$replacement = $this->tokens[ $last_header_ptr ]['orig_content'];
					} else {
						$replacement = $this->tokens[ $last_header_ptr ]['content'];
					}

					$replacement = str_replace( $last_header_matches[1], 'Text Domain', $replacement );
					$replacement = str_replace( $last_header_matches[2], $this->new_text_domain, $replacement );

					if ( \T_DOC_COMMENT_OPEN_TAG === $this->tokens[ $stackPtr ]['code'] ) {
						for ( $i = ( $last_header_ptr - 1 ); ; $i-- ) {
							if ( $this->tokens[ $i ]['line'] !== $this->tokens[ $last_header_ptr ]['line'] ) {
								++$i;
								break;
							}
						}

						$replacement = $this->phpcsFile->eolChar
							. GetTokensAsString::origContent( $this->phpcsFile, $i, ( $last_header_ptr - 1 ) )
							. $replacement;
					}

					$this->phpcsFile->fixer->addContent( $comment_details['last_header_ptr'], $replacement );
				}
			}
		}

		return $skip_to;
	}

	/**
	 * Examine an individual token in a larger comment for plugin/theme headers.
	 *
	 * @since 1.2.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $regex           The regex to use to examine the comment line.
	 * @param array  $headers         Valid headers for a plugin or theme.
	 * @param array  $comment_details The information collected so far.
	 *
	 * @return array Adjusted $comment_details array
	 */
	protected function examine_comment_line( $stackPtr, $regex, $headers, $comment_details ) {
		if ( preg_match( $regex, $this->tokens[ $stackPtr ]['content'], $matches ) === 1 ) {
			++$comment_details['headers_found'];

			if ( true === $headers[ $matches[1] ] ) {
				$comment_details['required_header_found'] = true;
			}

			if ( 'Text Domain' === $matches[1] ) {
				$comment_details['text_domain_ptr']   = $stackPtr;
				$comment_details['text_domain_found'] = trim( $matches[2] );
			}

			$comment_details['last_header_ptr']     = $stackPtr;
			$comment_details['last_header_matches'] = $matches;
		}

		return $comment_details;
	}
}
