<?php

/*
* This file is part of the "lia_multicolumnwizard" Extension for TYPO3 CMS.
*
* For the full copyright and license information, please read the
* LICENSE.txt file that was distributed with this source code.
*/

namespace LIA\LiaMulticolumnwizard\Utilities;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ReferenceFactory
{
    /**
     * getReference
     *
     * @param string $databaseName
     * @param string $valueField
     * @param string $outputNameField
     *
     * @return array items
     */
    public static function getReference(string $databaseName, string $valueField, string $outputNameField): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($databaseName);
        $pages = $queryBuilder->select('*')->from($databaseName)->execute()->fetchAll();
        $items = ['' => '---'];

        if (!empty($pages)) {
            foreach ($pages as $page) {
                $items[$page[$valueField]] = $page[$outputNameField];
            }
        }

        return $items;
    }
}
