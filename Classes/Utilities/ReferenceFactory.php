<?php

declare(strict_types=1);

/*
* This file is part of the "lia_multicolumnwizard" Extension for TYPO3 CMS.
*
* For the full copyright and license information, please read the
* LICENSE.txt file that was distributed with this source code.
*/

namespace LIA\LiaMulticolumnwizard\Utilities;

use LIA\LiaMulticolumnwizard\Exceptions\Backend\WrongOptionsReturnTypeException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ReferenceFactory
{
    /*########################*/
    /*####PUBLIC FUNCTIONS####*/
    /*########################*/

    /**
     * Retrieves reference data from a specified database table.
     *
     * This method retrieves data from a specified database table using the provided field names
     * for the value and output. It returns an array where the keys are the values from the
     * `$valueField` and the corresponding values are from the `$outputNameField`.
     *
     * @param string $tableName The name of the database table to query.
     * @param string $valueField The field used for the keys in the returned array.
     * @param string $outputNameField The field used for the values in the returned array.
     *
     * @return array $items An array of reference data with values from the specified fields, or an empty array if no results are found.
     */
    public static function getReference(&$parameters, &$ref): array
    {
        $tableName = (string) $parameters['tableName'];
        $valueField = (string) $parameters['valueField'];
        $outputNameField = (string) $parameters['outputNameField'];

        if ($tableName == '' || $valueField == '' || $outputNameField == '') {
            throw new WrongOptionsReturnTypeException('Missing required parameters for getReference - Expecting: [\'tableName\' => \'pages\', \'valueField\' => \'uid\', \'outputNameField\' => \'title\']', 1742813775);
        }

        $items = ['' => '---'];

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);

        $results = $queryBuilder
            ->select($valueField, $outputNameField)
            ->from($tableName)
            ->executeQuery()
            ->fetchAllAssociative();

        if (!empty($results)) {
            foreach ($results as $resultRow) {
                if (isset($resultRow[$valueField], $resultRow[$outputNameField])) {
                    $items[$resultRow[$valueField]] = $resultRow[$outputNameField];
                }
            }
        }

        return $items;
    }
}
