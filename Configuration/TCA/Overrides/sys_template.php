<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

ExtensionManagementUtility::addStaticFile(
    'ns_gridtocontainer',
    'Configuration/TypoScript',
    'Migration Gridelements to Container'
);
