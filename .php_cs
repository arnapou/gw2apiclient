<?php

$header = <<<HEADER
This file is part of the Arnapou GW2 API Client package.

(c) Arnaud Buathier <arnaud@arnapou.net>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
HEADER;

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
    ])
    ->exclude([
        'Arnapou/GW2Skills/config',
    ]);

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2'                                 => true,
        'array_syntax'                          => ['syntax' => 'short'],
        'blank_line_after_opening_tag'          => true,
        'combine_consecutive_issets'            => true,
        'combine_consecutive_unsets'            => true,
        'concat_space'                          => ['spacing' => 'one'],
        'native_function_casing'                => true,
        'no_alias_functions'                    => true,
        'no_blank_lines_after_class_opening'    => true,
        'no_blank_lines_after_phpdoc'           => true,
        'no_empty_comment'                      => true,
        'no_empty_phpdoc'                       => true,
        'no_empty_statement'                    => true,
        'no_leading_import_slash'               => true,
        'no_leading_namespace_whitespace'       => true,
        'no_mixed_echo_print'                   => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_unused_imports'                     => true,
        'phpdoc_scalar'                         => true,
        'phpdoc_single_line_var_spacing'        => true,
        'short_scalar_cast'                     => true,
        'single_quote'                          => true,
        'standardize_not_equals'                => true,
        'ternary_to_null_coalescing'            => true,
        'trailing_comma_in_multiline_array'     => true,
        'native_function_invocation'            => ['include' => ['@compiler_optimized']],
        'ordered_imports'                       => ['sort_algorithm' => 'alpha'],
        'single_import_per_statement'           => true,
        'header_comment'                        => ['header' => $header],
    ])
    ->setFinder($finder);
