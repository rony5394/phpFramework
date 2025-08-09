<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRules([
        // Use tabs for indentation
        'indentation_type' => true,

        // No automatic line length limit (you can add if needed)
        // 'line_ending' => "\n",  // optional, defaults to Unix style

        // Control structures (if, else, while, etc.) braces on the same line
//        'control_structure_braces' => [
//            'position' => 'same_line',
//        ],

        // Function and class braces on the same line (disable PSR12 brace fixer)
        'braces' => false,

        // No space after function keyword and before parentheses
        'function_declaration' => [
            'closure_function_spacing' => 'none',
        ],

        // Enforce one blank line after namespace and use statements (optional)
        'blank_line_after_namespace' => true,
        'blank_line_after_opening_tag' => true,

        // No extra spaces before/after control structures
        'no_extra_blank_lines' => [
            'tokens' => ['extra'],
        ],

        // Ensure there is a space after control structure keywords (if, while, etc)
//        'control_structure_keyword_spacing' => true,

        // No trailing spaces
        'no_trailing_whitespace' => true,

        // Trim trailing spaces inside lines
//        'trim_trailing_whitespace' => true,
    ])
    ->setIndent("\t")
    ->setFinder($finder);

