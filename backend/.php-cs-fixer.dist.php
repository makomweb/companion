<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src')
    ->in('tests')
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'declare_strict_types' => true,
        'php_unit_method_casing' => ['case' => 'snake_case'],
        'native_function_invocation' => [
            'include' => ['@compiler_optimized'],
            'exclude' => ['assert', 'is_string', 'sprintf'],
        ],
    ])
    ->setFinder($finder)
;
