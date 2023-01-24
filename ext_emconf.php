<?php

$EM_CONF[$_EXTKEY] = [
    'title' => '[NITSAN] Migration Gridelements to Container',
    'description' => 'This Extension is use for who want to switch from EXT:gridelements to EXT:container.',
    'category' => 'plugin',
    'author' => 'Team NITSAN',
    'author_email' => 'sanjay@nitsan.in',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.0.0-11.5.99',
            'container' => '2.1',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
