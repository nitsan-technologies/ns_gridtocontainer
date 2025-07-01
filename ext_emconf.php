<?php

$EM_CONF['ns_gridtocontainer'] = [
    'title' => 'Migrate Gridelements to Container',
    'description' => "Quickly migrate from EXT:gridelements to EXT:container in TYPO3 with this developer-friendly tool. Save time and ensure compatibility with the latest TYPO3 versions using this streamlined migration extension.", 
    
    'category' => 'plugin',
    'author' => 'Team T3Planet',
    'author_email' => 'info@t3planet.de',
    'author_company' => 'T3Planet',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '2.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '10.0.0-12.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
