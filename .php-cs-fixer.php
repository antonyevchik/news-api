<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@PSR2'                               => true,
    'array_syntax'                        => ['syntax' => 'short'],
    'ordered_imports'                     => ['sort_algorithm' => 'alpha'],
    'no_unused_imports'                   => true,
    'array_indentation'                   => true,
    'method_chaining_indentation'         => true,
    'no_useless_return'                   => true,
    'no_useless_else'                     => true,
    'phpdoc_order'                        => true,
    'phpdoc_separation'                   => true,
    'no_closing_tag'                      => true,
    'no_empty_phpdoc'                     => true,
    'phpdoc_add_missing_param_annotation' => true,
    'phpdoc_align'                        => ['align' => 'vertical'],
    'blank_line_after_opening_tag'        => true,
    'ternary_operator_spaces'             => true,
    'binary_operator_spaces'              => [
        'default' => 'align',
        'operators' => ['=' => 'align', '=>' => 'align_by_scope']
    ],
    'blank_line_before_statement' => [
        'statements' => ['return']
    ],
];


$finder = Finder::create()
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/resources',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new Config();
return $config->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
