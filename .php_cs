<?php

$finder = PhpCsFixer\Finder::create()
            ->in(__DIR__.'/resources')
            ->in(__DIR__.'/src')
            ->in(__DIR__.'/tests');

return PhpCsFixer\Config::create()
            ->setRiskyAllowed(false)
            ->setRules([
                '@Symfony' => true,
                'binary_operator_spaces' => ['align_double_arrow' => false, 'align_equals' => false],
                'no_empty_comment' => false,
                'no_extra_consecutive_blank_lines' => false,
                'not_operator_with_successor_space' => true,
                'ordered_imports' => ['sortAlgorithm' => 'length'],
                'phpdoc_align' => false,
                'phpdoc_no_empty_return' => false,
                'phpdoc_order' => true,
                'pre_increment' => false,
                'yoda_style' => false,
            ])
            ->setFinder($finder);
