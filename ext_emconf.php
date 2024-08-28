<?php

$EM_CONF['ns_gridtocontainer'] = [
    'title' => 'Gridelements to Container',
    'description' => "Are you tired of waiting for a public version of Gridelements for the current TYPO3 version? With EXT:container, there's a public solution available for creating grids and containers in TYPO3. We've developed a developer-friendly TYPO3 extension called EXT:ns_gridtocontainer to help you migrate from EXT:gridelements to this new extension. By using our TYPO3 extension, you can easily migrate and save yourself time and effort. 
    
    *** Live-Demo: https://demo.t3planet.com/t3-extensions/typo3-gridelements-container *** Documentation & Free Support: https://t3planet.com/typo3-gridelements-container",
    'category' => 'plugin',
    'author' => 'T3: Nilesh Malankiya, T3: Jay Bhalgamiya, QA: Krishna Dhapa',
    'author_email' => 'sanjay@nitsan.in',
    'author_company' => 'T3Planet // NITSAN',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.0.0-12.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
