<?php

declare(strict_types=1);

namespace NITSAN\NsGridtocontainer\Controller;

/**
 * This file is part of the "Migration of Gridelements to Container" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2022 Nilesh Malankiya <nilesh@nitsantech.com>, NITSAN Technologies
 */

/**
 * MigrationController
 */
class MigrationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * migrationRepository
     *
     * @var \NITSAN\NsGridtocontainer\Domain\Repository\MigrationRepository
     */
    protected $migrationRepository = null;

    /**
     * @param \NITSAN\NsGridtocontainer\Domain\Repository\MigrationRepository $migrationRepository
     */
    public function injectMigrationRepository(\NITSAN\NsGridtocontainer\Domain\Repository\MigrationRepository $migrationRepository)
    {
        $this->migrationRepository = $migrationRepository;
    }

    public function dashboardAction()
    {
        $grids = $this->migrationRepository->getGrids();
        if($grids) {
            $assign = [
                'action' => 'dashboard',
            ];
            $this->view->assignMultiple($assign);
        }
    }

    public function executeMigrationAction()
    {
        $grids = $this->migrationRepository->getGrids();
        $assign = [];
        if($grids) {
            $result = $this->migrationRepository->executeUpdate();
            if($result) {
                $assign = [
                    'action' => 'executeMigration',
                    'result' => $result,
                ];
            }
        } else {
            $assign = [
                'action' => 'executeMigration',
                'result' => '',
            ];

        }
        $this->view->assignMultiple($assign);
    }

    public function specificGridMigrateAction()
    {
        $gridelementsElements = $this->migrationRepository->findGridelements();
        if(empty($gridelementsElements)) {
            $assign = [
                'action' => 'executeMigration',
                'grid' => '',
            ];
        } else {
            $gridElementsArray = [];
            $layoutColumns = [];
            foreach ($gridelementsElements as $gridElement) {
                $columnElement = $this->migrationRepository->findContentfromGridElements($gridElement['uid']);
                if($columnElement) {
                    $columnElementFlip = array_fill_keys(array_column($columnElement, 'tx_gridelements_columns'), '1');
                    if(!isset($layoutColumns[$gridElement['tx_gridelements_backend_layout']])) {
                        $layoutColumns[$gridElement['tx_gridelements_backend_layout']] = [];
                    }
                    if(array_diff_assoc($columnElementFlip, $layoutColumns[$gridElement['tx_gridelements_backend_layout']])) {
                        $gridElementsArray[$gridElement['tx_gridelements_backend_layout']] = $gridElement;
                        $layoutColumns[$gridElement['tx_gridelements_backend_layout']] += $columnElementFlip;
                    }
                }
            }
            $assign = [
                "gridelementsElements" => $gridElementsArray,
                "layoutColumns" => $layoutColumns,
                "grid" => "find",
            ];
        }
        $this->view->assignMultiple($assign);
    }

    public function processMirgrateAction()
    {
        $arguments = $this->request->getArguments();
        $migrateAllElements = $this->migrationRepository->updateAllElements($arguments['migrategeneral']['elements']);
        $this->view->assignMultiple(
            array(
                "arguments" => $arguments,
                "migrateAllElements" => $migrateAllElements
            )
        );
    }
}
