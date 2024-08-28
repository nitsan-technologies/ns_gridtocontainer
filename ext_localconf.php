<?php

defined('TYPO3') || die();

(static function () {
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

    $iconRegistry->registerIcon(
        'module-nsgridtocontainer',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:ns_gridtocontainer/Resources/Public/Icons/module-nitsan.svg']
    );
})();
