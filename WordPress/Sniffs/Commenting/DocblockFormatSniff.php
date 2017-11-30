<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPress\Sniffs\Commenting;

use WordPress\Sniff;
use PHP_CodeSniffer_Tokens as Tokens;

/**
 * Verifies the formatting of a docblock comment.
 *
 * - A docblock comment should not be empty.
 * - Single-line docblocks are allowed by default. This can be turned off using the
 *   `allow_short` property.
 * - Single-line docblocks should have one space after the opener and before the closer.
 * - For multi-line docblocks:
 *   - The docblock opener and closer should each be the only content on their own line
 *     for multi-line docblocks.
 *   - The first content of the docblock should start on the line below the opener.
 *   - The last content of the docblock should be on the line above the closer.
 *   - Each line in the docblock should start with a star.
 *   - Stars should be aligned with the first star in the docblock opener.
 *   - There should be a minimum of one space after each star (unless it is an empty comment line).
 *   - If the docblock has tags, there should be an empty line before the first tag.
 *   - There should be no consecutive blank lines in a docblock.
 *
 * This sniff doesn't concern itself with the indentation of the docblock opener.
 * That is the concern of the `ScopeIndent` sniff.
 * Once that sniff has done it's work, however, this sniff will ensure that the indentation
 * of subsequent lines of the docblock is aligned when compared to the indentation of the
 * docblock opener on the first line.
 * The sniff works independently of tabs vs spaces and will base the resulting indentation
 * on the indentation found on the first line. Tabs vs spaces is the concern of another sniff.
 *
 * @link    https://make.wordpress.org/core/handbook/best-practices/inline-documentation-standards/php/
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.16.0
 */
class DocblockFormatSniff extends Sniff {

	/**
	 * Whether or not to allow one-line short form docblocks.
	 *
	 * @var bool
	 */
	public $allow_short = true;

	/**
	 * Tokens which can be considered as empty when determining whether a docblock
	 * has content.
	 *
	 * @var array
	 */
	private $empty_doc_tokens = array(
		T_DOC_COMMENT_WHITESPACE => T_DOC_COMMENT_WHITESPACE,
		T_DOC_COMMENT_STAR       => T_DOC_COMMENT_STAR,
	);

	/**
	 * Special marker used within WP core to signal start/end of automated
	 * code replacement. These should not be touched.
	 *
	 * @var string
	 */
	private $special_markers = array(
		'#@+' => true,
		'#@-' => true,
	);

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_DOC_COMMENT_OPEN_TAG,
		);
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process_token( $stackPtr ) {

		if ( ! isset( $this->tokens[ $stackPtr ]['comment_closer'] ) ) {
			return;
		}

		$comment_closer  = $this->tokens[ $stackPtr ]['comment_closer'];
		$is_one_liner    = ( $this->tokens[ $stackPtr ]['line'] === $this->tokens[ $comment_closer ]['line'] );
		$before_docblock = $this->phpcsFile->findPrevious( T_WHITESPACE, ( $stackPtr - 1 ), null, true );

		$allow_short = true;
		if ( false === $this->allow_short || false === $is_one_liner ) {
			$allow_short = false;
		}

		/**
		 * Check for empty docblocks.
		 */
		$is_empty          = false;
		$first_doc_content = $this->phpcsFile->findNext( $this->empty_doc_tokens, ( $stackPtr + 1 ), $comment_closer, true );
		if ( false === $first_doc_content ) {
			$this->phpcsFile->addError( 'A docblock should not be empty.', $stackPtr, 'Empty' );

			$is_empty = true; // Prevent reporting on empty lines when docblock is completely empty.
		}

		/*
		 * Examine the docblock closer.
		 */
		if ( false === $allow_short ) {
			if ( false === $is_empty ) {
				$prev_doc_content = $this->phpcsFile->findPrevious( $this->empty_doc_tokens, ( $comment_closer - 1 ), null, true );
				if ( false !== $prev_doc_content
					&& $this->tokens[ $comment_closer ]['line'] > ( $this->tokens[ $prev_doc_content ]['line'] + 1 )
				) {
					for ( $next_line = ( $prev_doc_content + 1 ); $next_line < $comment_closer; $next_line++ ) {
						if ( $this->tokens[ $prev_doc_content ]['line'] === $this->tokens[ $next_line ]['line'] ) {
							continue;
						}
						break;
					}

					$fix = $this->phpcsFile->addFixableError(
						'Blank line(s) found at end of docblock.',
						$next_line,
						'BlankLineBeforeCloser'
					);

					if ( true === $fix ) {
						$this->phpcsFile->fixer->beginChangeset();

						for ( $i = $next_line; $i < $comment_closer; $i++ ) {
							if ( $this->tokens[ $comment_closer ]['line'] === $this->tokens[ $i ]['line'] ) {
								break;
							}

							$this->phpcsFile->fixer->replaceToken( $i, '' );
						}

						$this->phpcsFile->fixer->endChangeset();
					}
				}
				unset( $prev_doc_content, $next_line );
			}

			$prev = $this->phpcsFile->findPrevious( T_DOC_COMMENT_WHITESPACE, ( $comment_closer - 1 ), null, true );
			if ( false !== $prev
				&& $this->tokens[ $comment_closer ]['line'] === $this->tokens[ $prev ]['line']
			) {
				$fix = $this->phpcsFile->addFixableError(
					'The docblock closer should be on a line by itself. Content found before closer.',
					$prev,
					'ContentBeforeCloser'
				);

				if ( true === $fix ) {
					$trimmed = rtrim( $this->tokens[ $prev ]['content'], ' ' );
					$this->phpcsFile->fixer->replaceToken( $prev, $trimmed . $this->phpcsFile->eolChar );
				}
			}
			unset( $prev );
		}

		$next = $this->phpcsFile->findNext( T_WHITESPACE, ( $comment_closer + 1 ), null, true );
		if ( false !== $next ) {
			if ( $this->tokens[ $comment_closer ]['line'] === $this->tokens[ $next ]['line'] ) {
				$fix = $this->phpcsFile->addFixableError(
					'The docblock closer should be on a line by itself. Content found after closer.',
					$next,
					'ContentAfterCloser'
				);

				if ( true === $fix ) {
					$this->phpcsFile->fixer->addNewline( $comment_closer );
				}
			}
		}
		unset( $next );

		/*
		 * Check docblock closer style.
		 */
		if ( '*/' !== $this->tokens[ $comment_closer ]['content'] ) {
			$fix = $this->phpcsFile->addFixableError(
				'Block comments must be ended with */',
				$comment_closer,
				'WrongEnd'
			);

			if ( true === $fix ) {
				$this->phpcsFile->fixer->replaceToken( $comment_closer, '*/' );
			}
		}

		/*
		 * Examine the docblock opener.
		 */
		if ( false === $is_empty && false === $allow_short ) {
			/*
			 * Check for blank lines at the top of the docblock.
			 */
			if ( false !== $first_doc_content
				&& $this->tokens[ $first_doc_content ]['line'] > ( $this->tokens[ $stackPtr ]['line'] + 1 )
			) {
				for ( $next_line = ( $stackPtr + 1 ); $next_line < $first_doc_content; $next_line++ ) {
					if ( $this->tokens[ $stackPtr ]['line'] === $this->tokens[ $next_line ]['line'] ) {
						continue;
					}
					break;
				}

				$fix = $this->phpcsFile->addFixableError(
					'Blank line(s) found at start of docblock.',
					$next_line,
					'BlankLineAfterOpener'
				);

				if ( true === $fix ) {
					$this->phpcsFile->fixer->beginChangeset();

					for ( $i = $next_line; $i < $first_doc_content; $i++ ) {
						if ( $this->tokens[ $first_doc_content ]['line'] === $this->tokens[ $i ]['line'] ) {
							break;
						}

						$this->phpcsFile->fixer->replaceToken( $i, '' );
					}

					$this->phpcsFile->fixer->endChangeset();
				}
			}
		}

		if ( false === $allow_short ) {
			$next = $this->phpcsFile->findNext( T_DOC_COMMENT_WHITESPACE, ( $stackPtr + 1 ), null, true );
			if ( false !== $next
				&& $this->tokens[ $stackPtr ]['line'] === $this->tokens[ $next ]['line']
				// Ignore expansion/replacement markers.
				&& ! isset( $this->special_markers[ $this->tokens[ $next ]['content'] ] )
			) {
				$fix = $this->phpcsFile->addFixableError(
					'The docblock opener should be on a line by itself. Content found after opener.',
					$next,
					'ContentAfterOpener'
				);

				if ( true === $fix ) {
					$this->phpcsFile->fixer->addNewline( $stackPtr );
				}
			}
			unset( $next );
		}

		$content_before_opener = false;
		if ( false !== $before_docblock ) {
			if ( $this->tokens[ $stackPtr ]['line'] === $this->tokens[ $before_docblock ]['line'] ) {
				$fix = $this->phpcsFile->addFixableError(
					'The docblock opener should be on a line by itself. Content found before opener.',
					$before_docblock,
					'ContentBeforeOpener'
				);

				if ( true === $fix ) {
					$this->phpcsFile->fixer->addNewline( $before_docblock );
				}

				// If there is content before the opener, the alignment will be off anyway, so stop here.
				$content_before_opener = true;
			}
		}
		unset( $before_docblock );

		if ( true === $is_one_liner && true === $allow_short ) {
			/*
			 * Check spacing within a one-liner.
			 */
			if ( T_DOC_COMMENT_WHITESPACE !== $this->tokens[ ( $stackPtr + 1 ) ]['code'] ) {
				if ( ! isset( $this->special_markers[ $this->tokens[ ( $stackPtr + 1 ) ]['content'] ] ) ) {
					$fix = $this->phpcsFile->addFixableError(
						'There should be one space between the comment opener and the content.',
						$stackPtr,
						'NoSpaceAfterOpener'
					);

					if ( true === $fix ) {
						$this->phpcsFile->fixer->addContent( $stackPtr, ' ' );
					}
				}
			} elseif ( ' ' !== $this->tokens[ ( $stackPtr + 1 ) ]['content'] ) {
				$fix = $this->phpcsFile->addFixableError(
					'There should be one space between the comment opener and the content.',
					$stackPtr,
					'SpaceAfterOpener'
				);

				if ( true === $fix ) {
					$this->phpcsFile->fixer->replaceToken( ( $stackPtr + 1 ), ' ' );
				}
			}

			$pre_closer_content = $this->tokens[ ( $comment_closer - 1 ) ]['content'];
			if ( ! isset( $this->special_markers[ $pre_closer_content ] ) ) {
				$trimmed_pre_closer_content = rtrim( $pre_closer_content, ' ' );
				$whitespace_before_closer   = str_replace( $trimmed_pre_closer_content, '', $pre_closer_content );
				if ( ' ' !== $whitespace_before_closer ) {
					$length     = strlen( $whitespace_before_closer );
					$error_code = ( 0 === $length ) ? 'NoSpaceBeforeCloser' : 'SpaceBeforeCloser';

					$fix = $this->phpcsFile->addFixableError(
						'There should be one space between the comment content and the closer. Found %s',
						$comment_closer,
						$error_code,
						array( $length )
					);

					if ( true === $fix ) {
						if ( 0 === $length ) {
							$this->phpcsFile->fixer->addContent( ( $comment_closer - 1 ), ' ' );
						} else {
							$this->phpcsFile->fixer->replaceToken( ( $comment_closer - 1 ), $trimmed_pre_closer_content . ' ' );
						}
					}
				}
			}

			// No need to check the rest for one-liners.
			return;
		}

		/*
		 * Determine the required indentation based on the docblock opener.
		 */
		$required_indent = '';
		$required_column = $this->tokens[ $stackPtr ]['column'] + 1;

		if ( T_WHITESPACE === $this->tokens[ ( $stackPtr - 1 ) ]['code']
			&& $this->tokens[ ( $stackPtr - 1 ) ]['line'] === $this->tokens[ $stackPtr ]['line']
		) {
			// If tabs are being converted to spaces by the tokeniser, the
			// original content should be used instead of the converted content.
			if ( isset( $this->tokens[ ( $stackPtr - 1 ) ]['orig_content'] ) ) {
				$required_indent = $this->tokens[ ( $stackPtr - 1 ) ]['orig_content'];
			} else {
				$required_indent = $this->tokens[ ( $stackPtr - 1 ) ]['content'];
			}
		}

		$required_indent .= ' ';

		/*
		 * Check that there are no consecutive blank lines.
		 */
		if ( false !== $first_doc_content ) {
			$prev_doc_content = $first_doc_content;
			while ( $next_doc_content = $this->phpcsFile->findNext( $this->empty_doc_tokens, ( $prev_doc_content + 1 ), $comment_closer, true ) ) {

				if ( $this->tokens[ $next_doc_content ]['line'] > ( $this->tokens[ $prev_doc_content ]['line'] + 2 ) ) {
					$fix = $this->phpcsFile->addFixableError(
						'Multiple consecutive blank lines are not allowed in a docblock.',
						( $prev_doc_content + 2 ),
						'SuperfluousBlankLines'
					);

					if ( true === $fix ) {
						$this->phpcsFile->fixer->beginChangeset();
						for ( $i = ( $prev_doc_content + 1 ); $i < $next_doc_content; $i++ ) {
							if ( $this->tokens[ $i ]['line'] === $this->tokens[ $next_doc_content ]['line'] ) {
								break;
							}

							$this->phpcsFile->fixer->replaceToken( $i, '' );
						}

						$this->phpcsFile->fixer->addContent(
							$prev_doc_content,
							$this->phpcsFile->eolChar . $required_indent . '*' . $this->phpcsFile->eolChar
						);
						$this->phpcsFile->fixer->endChangeset();
					}
				}

				$prev_doc_content = $next_doc_content;
			}
		}
		unset( $first_doc_content );

		/*
		 * Check that there is exactly one blank line before the first tag.
		 */
		if ( isset( $this->tokens[ $stackPtr ]['comment_tags'][0] ) ) {
			$first_tag        = $this->tokens[ $stackPtr ]['comment_tags'][0];
			$prev_doc_content = $this->phpcsFile->findPrevious(
				$this->empty_doc_tokens,
				( $first_tag - 1 ),
				( $stackPtr + 1 ), // The open tag should not be considered content.
				true
			);
			if ( false !== $prev_doc_content
				&& $this->tokens[ $first_tag ]['line'] < ( $this->tokens[ $prev_doc_content ]['line'] + 2 )
			) {
				$fix = $this->phpcsFile->addFixableError(
					'There must be exactly one blank line before the tags in a doc comment.',
					$first_tag,
					'NoBlankLineBeforeTags'
				);

				if ( true === $fix ) {
					$this->phpcsFile->fixer->addContent(
						$prev_doc_content,
						$this->phpcsFile->eolChar . $required_indent . '*' . $this->phpcsFile->eolChar
					);
				}
			}
			unset( $first_tag, $prev_doc_content );
		}

		/*
		 * Check that each line starts with a star, that the stars are correctly aligned.
		 */
		for ( $i = ( $stackPtr + 1 ); $i <= $comment_closer; $i++ ) {
			if ( 1 !== $this->tokens[ $i ]['column'] ) {
				continue;
			}

			if ( T_DOC_COMMENT_WHITESPACE !== $this->tokens[ $i ]['code'] ) {
				if ( T_DOC_COMMENT_STAR !== $this->tokens[ $i ]['code']
					&& T_DOC_COMMENT_CLOSE_TAG !== $this->tokens[ $i ]['code']
				) {
					$fix = $this->phpcsFile->addFixableError(
						'Each line in a docblock should start with an asterisk.',
						$i,
						'NoStar'
					);
					if ( true === $fix ) {
						$insert = $required_indent . '*';
						if ( isset( $this->tokens[ ( $i + 2 ) ] )
							&& $this->tokens[ $i ]['line'] === $this->tokens[ ( $i + 2 ) ]['line']
						) {
							// Not a blank line.
							$insert .= ' ';
						}

						$this->phpcsFile->fixer->addContentBefore( $i, $insert );
					}
				} elseif ( false === $content_before_opener ) {
					$error = 'Expected %s space(s) before the asterisk; %s found';
					$data  = array(
						( $required_column - 1 ),
						( $this->tokens[ $i ]['column'] - 1 ),
					);

					$fix = $this->phpcsFile->addFixableError( $error, $i, 'NoSpaceBeforeStar', $data );
					if ( true === $fix ) {
						$this->phpcsFile->fixer->addContentBefore( $i, $required_indent );
					}

					$this->verify_space_after_star( $i );
				}
			} elseif ( T_DOC_COMMENT_WHITESPACE === $this->tokens[ $i ]['code'] ) {

				if ( isset( $this->tokens[ ( $i + 1 ) ] ) ) {

					if ( T_DOC_COMMENT_STAR !== $this->tokens[ ( $i + 1 ) ]['code']
						&& T_DOC_COMMENT_CLOSE_TAG !== $this->tokens[ ( $i + 1 ) ]['code']
					) {
						$fix = $this->phpcsFile->addFixableError(
							'Each line in a docblock should start with an asterisk.',
							$i,
							'NoStar'
						);
						if ( true === $fix ) {
							$replacement = $required_indent . '*';
							if ( isset( $this->tokens[ ( $i + 2 ) ] )
								&& $this->tokens[ $i ]['line'] === $this->tokens[ ( $i + 2 ) ]['line']
							) {
								// Not a blank line.
								$replacement .= ' ';
							} else {
								$replacement .= $this->phpcsFile->eolChar;
							}

							$this->phpcsFile->fixer->replaceToken( $i, $replacement );
						}
					} elseif ( false === $content_before_opener ) {

						if ( $this->tokens[ ( $i + 1 ) ]['column'] !== $required_column ) {
							$error = 'Expected %s space(s) before the asterisk; %s found';
							$data  = array(
								( $required_column - 1 ),
								( $this->tokens[ ( $i + 1 ) ]['column'] - 1 ),
							);

							$fix = $this->phpcsFile->addFixableError( $error, $i, 'SpaceBeforeStar', $data );
							if ( true === $fix ) {
								$this->phpcsFile->fixer->replaceToken( $i, $required_indent );
							}
						}

						$this->verify_space_after_star( $i + 1 );
					}
				}
			}
		}
	}

	/**
	 * Verify the spacing after a comment star.
	 *
	 * There should be - at least - one space between the asterix and the comment
	 * content. More is accepted to allow for, for instance, param comment alignment.
	 *
	 * @param int $stackPtr Stackpointer to the comment star.
	 */
	protected function verify_space_after_star( $stackPtr ) {
		if ( T_DOC_COMMENT_STAR !== $this->tokens[ $stackPtr ]['code'] ) {
			return;
		}

		if ( ! isset( $this->tokens[ ( $stackPtr + 2 ) ] )
			|| $this->tokens[ $stackPtr ]['line'] !== $this->tokens[ ( $stackPtr + 2 ) ]['line']
		) {
			// Line is empty.
			return;
		}

		if ( T_DOC_COMMENT_WHITESPACE !== $this->tokens[ ( $stackPtr + 1 ) ]['code'] ) {
			$fix = $this->phpcsFile->addFixableError(
				'Expected 1 space after the asterisk; 0 found',
				$stackPtr,
				'NoSpaceAfterStar'
			);

			if ( true === $fix ) {
				$this->phpcsFile->fixer->addContent( $stackPtr, ' ' );
			}
		} elseif ( T_DOC_COMMENT_TAG === $this->tokens[ ( $stackPtr + 2 ) ]['code']
			&& '@type' !== $this->tokens[ ( $stackPtr + 2 ) ]['content']
			&& ' ' !== $this->tokens[ ( $stackPtr + 1 ) ]['content']
		) {
			$error = 'Expected 1 space after the asterisk; %s found';
			$data  = array( strlen( $this->tokens[ ( $stackPtr + 1 ) ]['content'] ) );
			$fix   = $this->phpcsFile->addFixableError( $error, $stackPtr, 'SpaceAfterStar', $data );
			if ( true === $fix ) {
				$this->phpcsFile->fixer->replaceToken( ( $stackPtr + 1 ), ' ' );
			}
		}
	}

}//end class
