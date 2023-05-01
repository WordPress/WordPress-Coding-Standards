<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\DB;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the PreparedSQLPlaceholders sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.14.0
 *
 * @covers \WordPressCS\WordPress\Sniffs\DB\PreparedSQLPlaceholdersSniff
 */
final class PreparedSQLPlaceholdersUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			28  => 5,
			31  => 1,
			32  => 1,
			33  => 2,
			34  => 2,
			39  => 1,
			40  => 1,
			41  => 1,
			54  => 1,
			141 => 1,
			149 => 1,
			151 => 1,
			162 => 1,
			163 => 1,
			164 => 1,
			165 => 1,
			177 => 1,
			179 => 1,
			181 => 1,
			183 => 1,
			185 => 3,
			187 => 3,
			189 => 1,
			191 => 1,
			193 => 1,
			195 => 1,
			197 => 2,
			199 => 1,
			205 => 1,
			207 => 2,
			209 => 1,

			215 => 1, // UnsupportedPlaceholder.

			220 => 1, // QuotedSimplePlaceholder.
			221 => 1, // QuotedSimplePlaceholder.
			222 => 1, // QuotedSimplePlaceholder.
			223 => 1, // QuotedSimplePlaceholder.
			224 => 1, // QuotedSimplePlaceholder.
			225 => 1, // QuotedSimplePlaceholder.

			227 => 1, // QuotedIdentifierPlaceholder.
			228 => 1, // QuotedIdentifierPlaceholder.
			229 => 1, // QuotedIdentifierPlaceholder.
			230 => 1, // QuotedIdentifierPlaceholder.

			234 => 1, // UnescapedLiteral.
			238 => 1, // UnsupportedPlaceholder.

			244 => 1, // QuotedIdentifierPlaceholder.
			245 => 1, // QuotedIdentifierPlaceholder.
			246 => 1, // UnescapedLiteral.
			247 => 1, // QuotedIdentifierPlaceholder.
			248 => 1, // QuotedIdentifierPlaceholder.
			249 => 1, // QuotedIdentifierPlaceholder.
			250 => 2, // QuotedIdentifierPlaceholder.
			251 => 1, // QuotedIdentifierPlaceholder.
			252 => 1, // QuotedIdentifierPlaceholder.

			254 => 2, // QuotedIdentifierPlaceholder x2.
			261 => 1, // IdentifierWithinIN.
			267 => 1, // IdentifierWithinIN.
			272 => 1, // IdentifierWithinIN.
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array(
			12  => 1,
			16  => 1,
			17  => 1,
			23  => 1,
			30  => 2,
			31  => 2,
			32  => 2,
			33  => 1,
			34  => 1,
			44  => 1,
			45  => 1,
			46  => 1,
			55  => 1,
			56  => 1,
			57  => 1,
			58  => 1,
			61  => 1,
			62  => 1, // Old-style WPCS ignore comments are no longer supported.
			66  => 1,
			68  => 1, // Old-style WPCS ignore comments are no longer supported.
			126 => 1,
			139 => 1,
			160 => 2,
			161 => 2,
			177 => 1,
			214 => 1,
			215 => 1,
			216 => 1,
			217 => 1,
			218 => 1,
			234 => 1, // UnfinishedPrepare.
			246 => 1, // UnfinishedPrepare.
			258 => 1, // ReplacementsWrongNumber.
		);
	}

}
