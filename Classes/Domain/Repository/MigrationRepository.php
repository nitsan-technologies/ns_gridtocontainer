<?php

declare(strict_types=1);

namespace NITSAN\NsGridtocontainer\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;

/**
 * This file is part of the "Migration of Gridelements to Container" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2022 Nilesh Malankiya <nilesh@nitsantech.com>, NITSAN Technologies
 */

/**
 * The repository for Migration
 */
class MigrationRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    public function getGrids()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $elementCount = $queryBuilder->count('uid')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('gridelements_pi1', \PDO::PARAM_STR))
            )
            ->execute()->fetchColumn(0);
        return (bool)$elementCount && \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('container');
    }
    public function getGridsV12()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $elementCount = $queryBuilder->count('uid')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('gridelements_pi1', \PDO::PARAM_STR))
            )
            ->execute()->fetchOne();

        return (bool)$elementCount && \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('container');
    }

    public function executeUpdate(): bool
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tt_content');
        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $statement = $queryBuilder->select('uid', 'tx_gridelements_backend_layout')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('gridelements_pi1', \PDO::PARAM_STR)),
            )
            ->execute();
        while ($record = $statement->fetchAll()) {
            foreach ($record as $key => $value) {
                $cType = $value['tx_gridelements_backend_layout'];
                $queryBuilder = $connection->createQueryBuilder();
                $queryBuilder->update('tt_content')
                    ->where(
                        $queryBuilder->expr()->eq(
                            'uid',
                            $queryBuilder->createNamedParameter($value['uid'], \PDO::PARAM_INT)
                        )
                    )
                    ->set('CType', $cType);
                $queryBuilder->execute();
            }
            $queryBuilder = $connection->createQueryBuilder();
            $queryBuilder->getRestrictions()->removeAll();
            $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
            $statement = $queryBuilder->select('uid', 'tx_gridelements_columns', 'tx_gridelements_container')
                ->from('tt_content')
                ->where(
                    $queryBuilder->expr()->eq('colPos', $queryBuilder->createNamedParameter('-1', \PDO::PARAM_STR)),
                )
                ->execute();

            while ($record = $statement->fetchAll()) {
                foreach ($record as $key => $value) {
                    $colPos = $value['tx_gridelements_columns'] + 100;
                    $queryBuilder = $connection->createQueryBuilder();
                    $queryBuilder->update('tt_content')
                        ->where(
                            $queryBuilder->expr()->eq(
                                'uid',
                                $queryBuilder->createNamedParameter($value['uid'], \PDO::PARAM_INT)
                            )
                        )
                        ->set('colPos', $colPos)
                        ->set('tx_gridelements_container', 0)
                        ->set('tx_gridelements_columns', 0)
                        ->set('tx_container_parent', $value['tx_gridelements_container']);
                    $queryBuilder->execute();
                }
            }
        }
        return true;
    }

    /**
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findGridelements()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $results = $queryBuilder
            ->select('*')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('gridelements_pi1', \PDO::PARAM_STR))
            )
            ->execute()
            ->fetchAll(\Doctrine\DBAL\FetchMode::ASSOCIATIVE);

        return $results;
    }

    public function findContentfromGridElements($id)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $results = $queryBuilder
            ->select('*')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('tx_gridelements_container', $id)
            )
            ->execute()
            ->fetchAll(\Doctrine\DBAL\FetchMode::ASSOCIATIVE);
        return $results;
    }


    /**
      * @param $elementsArray
      * @return bool
      */
    public function updateAllElements($elementsArray)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        foreach ($elementsArray as $key => $element) {
            if ($elementsArray[$key]['active'] == 1) {
                $elementsArray[$key]['contentelements'] = $queryBuilder
                    ->select('*')
                    ->from('tt_content')
                    ->where(
                        $queryBuilder->expr()->like('CType', '"%gridelements_pi%"'),
                        $queryBuilder->expr()->eq(
                            'tx_gridelements_backend_layout',
                            $queryBuilder->createNamedParameter($key)
                        )
                    )
                    ->execute()
                    ->fetchAll(\Doctrine\DBAL\FetchMode::ASSOCIATIVE);
            } else {
                unset($elementsArray[$key]);
            }
        }

        $contentElementResults = [];
        foreach ($elementsArray as $key => $results) {
            foreach ($results as $key2 => $elements) {
                if ($key2 == 'contentelements') {
                    foreach ($results[$key2] as $element) {

                        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
                        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

                        // Find Content Elements uids in a Grid
                        $contentElements = $queryBuilder
                            ->select('*')
                            ->from('tt_content')
                            ->where(
                                $queryBuilder->expr()->eq('tx_gridelements_container', $element['uid'])
                            )
                            ->execute()
                            ->fetchAll(\Doctrine\DBAL\FetchMode::ASSOCIATIVE);
                        foreach ($contentElements as $contentElement) {
                            $contentElementResults['parents'][$contentElement['uid']] = $contentElement['tx_gridelements_container'];
                        }
                        $contentElementResults[$key]['elements'][$element['uid']] = $contentElements;
                    }
                }
            }
        }
        // $contentElementResults - All elements uids which is stored in Grid
        foreach ($contentElementResults as $grids) {
            foreach ($grids as $key => $contents) {
                if(isset($grids['elements'])) {
                    foreach ($grids['elements'] as $key3 => $elements) {
                        foreach ($elements as $element) {
                            if ($element['tx_gridelements_container'] > 0) {

                                $colPos = $element['tx_gridelements_columns'] + 100;

                                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
                                $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

                                $queryBuilder->update('tt_content')
                                ->where(
                                    $queryBuilder->expr()->eq(
                                        'uid',
                                        $queryBuilder->createNamedParameter($element['uid'], \PDO::PARAM_INT)
                                    )
                                )
                                ->set('colPos', $colPos)
                                ->set('tx_gridelements_container', 0)
                                ->set('tx_gridelements_columns', 0)
                                ->set('tx_gridelements_backend_layout', 0)
                                ->set('tx_container_parent', $element['tx_gridelements_container']);
                                $queryBuilder->execute();
                            }
                        }
                    }
                }
            }
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        foreach ($elementsArray as $results) {
            foreach ($results as $key => $elements) {
                if ($key == 'contentelements') {
                    foreach ($results[$key] as $element) {

                        $queryBuilder->update('tt_content')
                        ->where(
                            $queryBuilder->expr()->eq(
                                'uid',
                                $queryBuilder->createNamedParameter($element['uid'], \PDO::PARAM_INT)
                            )
                        )
                        ->set('CType', $results['containername'])
                        ->set('tx_gridelements_backend_layout', '');
                        $queryBuilder->execute();
                    }
                }
            }
        }
        return true;
    }
}
