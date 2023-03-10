<?php

$EM_CONF[$_EXTKEY] = [
    'title' => '[NITSAN] Migrate Gridelements to Container',
    'description' => 'Easily migrate from EXT:gridelements to EXT:container; Get premium support at https://t3planet.com/typo3-gridelements-container',
    'category' => 'plugin',
	'author' => 'T3: Jay Bhalgamiya, QA: Krishna Dhapa',
	'author_email' => 'sanjay@nitsan.in',
	'author_company' => 'NITSAN Technologies Pvt Ltd',
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
