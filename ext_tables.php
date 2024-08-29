<?php

defined('TYPO3') || die();


if ((TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Core\Information\Typo3Version::class))->getMajorVersion() < 12) {

    if (!array_key_exists('nitsan', $GLOBALS['TBE_MODULES']) || $GLOBALS['TBE_MODULES']['nitsan'] == '') {
        if (!isset($GLOBALS['TBE_MODULES']['nitsan'])) {
            $temp_TBE_MODULES = [];
            foreach ($GLOBALS['TBE_MODULES'] as $key => $val) {
                if ($key == 'web') {
                    $temp_TBE_MODULES[$key] = $val;
                    $temp_TBE_MODULES['nitsan'] = '';
                } else {
                    $temp_TBE_MODULES[$key] = $val;
                }
            }
            $GLOBALS['TBE_MODULES'] = $temp_TBE_MODULES;
            $GLOBALS['TBE_MODULES']['_configuration']['nitsan'] = [
                'iconIdentifier' => 'module-nsgridtocontainer',
                'labels' => 'LLL:EXT:ns_gridtocontainer/Resources/Private/Language/BackendModule.xlf',
                'name' => 'nitsan'
            ];
        }
    }

    TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'NsGridtocontainer',
        'nitsan',
        'migration',
        '',
        [
            NITSAN\NsGridtocontainer\Controller\MigrationController::class => 'dashboard, executeMigration, specificGridMigrate, processMirgrate',

        ],
        [
            'access' => 'user,group',
            'icon'   => 'EXT:ns_gridtocontainer/Resources/Public/Icons/ns_gridtocontainer.svg',
            'labels' => 'LLL:EXT:ns_gridtocontainer/Resources/Private/Language/locallang_migration.xlf',
            'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
            'inheritNavigationComponentFromMainModule' => false
        ]
    );
}
