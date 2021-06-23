<?php

$header = <<<'EOF'
This file is part of the ElaoConsent bundle.

Copyright (C) Elao

@author Elao <contact@elao.com>
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in([
        // App
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->exclude([
        'fixtures',
        'expected',
    ])
;

return (new PhpCsFixer\Config())
    ->setUsingCache(true)
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules([
        '@Symfony' => true,
        'concat_space' => ['spacing' => 'one'],
        'phpdoc_summary' => false,
        'phpdoc_annotation_without_dot' => false,
        'phpdoc_order' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
        'simplified_null_return' => false,
        'header_comment' => ['header' => $header],
        'yoda_style' => false,
        'native_function_invocation' => ['include' => ['@compiler_optimized']],
        'no_superfluous_phpdoc_tags' => true,
    ])
;
