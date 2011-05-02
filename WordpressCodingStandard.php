<?php
/**
 * WordPress Coding Standard
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    John Godley
 */

if (class_exists( 'PHP_CodeSniffer_Standards_CodingStandard', true ) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');
}

/**
 * Wordpress Coding Standard
 *
 * Return a selection of default sniffs, followed by everything in the WordPress directory
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    John Godley
 */
class PHP_CodeSniffer_Standards_WordPress_WordPressCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{
    public function getExcludedSniffs()
    {
        return array(
            'Squiz/Sniffs/WhiteSpace/ScopeIndentSniff.php',
            'Squiz/Sniffs/WhiteSpace/ControlStructureSpacingSniff.php',
            'Squiz/Sniffs/WhiteSpace/ScopeClosingBraceSniff.php',
            'Squiz/Sniffs/WhiteSpace/LanguageConstructSpacingSniff.php',
            'Squiz/Sniffs/WhiteSpace/FunctionSpacingSniff.php',
            'Squiz/Sniffs/WhiteSpace/FunctionClosingBraceSpaceSniff.php',
			'Squiz/Sniffs/WhiteSpace/OperatorSpacingSniff.php',
			'Squiz/Sniffs/WhiteSpace/MemberVarSpacingSniff.php'
        );
    }

    public function getIncludedSniffs()
    {
        return array(
//                'Generic/Sniffs/Classes/DuplicateClassNameSniff.php',
                'Generic/Sniffs/Functions/OpeningFunctionBraceKernighanRitchieSniff.php',
                'Generic/Sniffs/PHP/DisallowShortOpenTagSniff.php',
                'Generic/Sniffs/PHP/ForbiddenFunctionsSniff.php',
                'Generic/Sniffs/PHP/LowerCaseConstantSniff.php',
                'Generic/Sniffs/CodeAnalysis',
                'Generic/Sniffs/Metrics',
//                'Generic/Sniffs/Strings',
                'Generic/Sniffs/Formatting/NoSpaceAfterCastSniff.php',
                'Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff.php',

                'PEAR/Sniffs/Files/IncludingFileSniff.php',
                'PEAR/Sniffs/NamingConventions/ValidClassNameSniff.php',
//                'PEAR/Sniffs/Formatting',

                'Squiz/Sniffs/Strings/EchoedStringsSniff.php',
                'Squiz/Sniffs/WhiteSpace',
                'Squiz/Sniffs/PHP/DisallowObEndFlushSniff.php',
                'Squiz/Sniffs/PHP/LowercasePHPFunctionsSniff.php',
                'Squiz/Sniffs/PHP/EvalSniff.php',
//                'Squiz/Sniffs/PHP/DiscouragedFunctionsSniff.php',
                'Squiz/Sniffs/PHP/ForbiddenFunctionsSniff.php',
                'Squiz/Sniffs/Operators/ValidLogicalOperatorsSniff.php',
                'Squiz/Sniffs/Functions/LowercaseFunctionKeywordsSniff.php',
                'Squiz/Sniffs/Functions/FunctionDuplicateArgumentSniff.php',

                'PEAR/Sniffs/Classes/ClassDeclarationSniff.php',

                'Wordpress/Sniffs'
                );
    }
}

?>