<?php

declare(strict_types=1);

use NITSAN\NsGridtocontainer\Controller\V12MigrationController;

/**
 * Definitions for modules provided by EXT:ns_wp_migration
 */
return [
    'nitsan_module' => [
        'labels' => 'LLL:EXT:ns_gridtocontainer/Resources/Private/Language/BackendModule.xlf',
        'iconIdentifier'   => 'module-nsgridtocontainer',
        'position' => ['after' => 'web'],
    ],
    'gridToContainer' => [
        'parent' => 'nitsan_module',
        'position' => ['before' => 'top'],
        'access' => 'user',
        'icon'   => 'EXT:ns_gridtocontainer/Resources/Public/Icons/ns_gridtocontainer.svg',
        'labels' => 'LLL:EXT:ns_gridtocontainer/Resources/Private/Language/locallang_migration.xlf',
        'path' => '/module/web/gridToContainer',
        'inheritNavigationComponentFromMainModule' => false,
        'extensionName' => 'ns_wp_migration',
        'navigationComponent' => '@typo3/backend/page-tree/page-tree-element',
        'controllerActions' => [
            V12MigrationController::class => 'dashboard, executeMigration, specificGridMigrate, processMirgrate',
        ],
    ],
];
