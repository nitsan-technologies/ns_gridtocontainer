<?php

declare(strict_types=1);

namespace NITSAN\NsGridtocontainer\Controller;

use NITSAN\NsGridtocontainer\Domain\Repository\MigrationRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * This file is part of the "Migration of Gridelements to Container" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2022 Nilesh Malankiya <nilesh@nitsantech.com>, NITSAN Technologies
 */

/**
 * V12MigrationController
 */
class V12MigrationController extends ActionController
{
    /**
     * migrationRepository
     *
     * @var ?MigrationRepository
     */
    protected ?MigrationRepository $migrationRepository;

    /**
     * @param MigrationRepository $migrationRepository
     */

    public function __construct(
        MigrationRepository $migrationRepository
    ) {
        $this->migrationRepository = $migrationRepository;
    }


    public function dashboardAction(): ResponseInterface
    {
        $grids = $this->migrationRepository->getGridsV12();
        $assign = [];
        if($grids) {
            $assign = [
                'action' => 'dashboard',
            ];
        }

        $assign['version'] = 12;
        return $this->getViewAndTemplate($this->request, $assign);
    }

    public function executeMigrationAction(): ResponseInterface
    {
        $grids = $this->migrationRepository->getGridsV12();
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
        $assign['version'] = 12;
        return $this->getViewAndTemplate($this->request, $assign);
    }

    public function specificGridMigrateAction(): ResponseInterface
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

        $assign['version'] = 12;
        return $this->getViewAndTemplate($this->request, $assign);
    }

    public function processMirgrateAction(): ResponseInterface
    {
        $arguments = $this->request->getArguments();
        $migrateAllElements = $this->migrationRepository->updateAllElements($arguments['migrategeneral']['elements']);
        $assign = [
            'arguments' => $arguments,
            'migrateAllElements' => $migrateAllElements,
        ];

        $assign['version'] = 12;
        return $this->getViewAndTemplate($this->request, $assign);
    }

    protected function getViewAndTemplate(ServerRequestInterface $request, array $assign): ResponseInterface
    {
        $view = $this->initializeModuleTemplate($request);
        $view->assignMultiple($assign);
        return $view->renderResponse();
    }

    protected function initializeModuleTemplate(ServerRequestInterface $request): ModuleTemplate
    {
        return GeneralUtility::makeInstance(ModuleTemplateFactory::class)->create($request);
    }
}
