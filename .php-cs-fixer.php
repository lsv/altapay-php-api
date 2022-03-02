<?php

$config = new PhpCsFixer\Config();
return $config
    ->setRules([
        '@PSR1' => true,
        '@PSR2' => true,
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->exclude(['vendor'])
        ->in(__DIR__)
    )
    ;
