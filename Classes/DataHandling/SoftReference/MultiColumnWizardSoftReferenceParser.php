<?php

declare(strict_types=1);

/*
* This file is part of the "lia_multicolumnwizard" Extension for TYPO3 CMS.
*
* For the full copyright and license information, please read the
* LICENSE.txt file that was distributed with this source code.
*/

namespace LIA\LiaMulticolumnwizard\DataHandling\SoftReference;

use TYPO3\CMS\Core\DataHandling\SoftReference\AbstractSoftReferenceParser;
use TYPO3\CMS\Core\DataHandling\SoftReference\SoftReferenceParserResult;
use TYPO3\CMS\Core\DataHandling\SoftReference\TypolinkSoftReferenceParser;

/**
 * Makes the links of a multiColumnWizard field visible to the reference index.
 *
 * A multiColumnWizard field stores its rows as a JSON string, which the typolink parser
 * cannot read. This parser walks the rows, hands every value of a "link" column to the
 * typolink parser and merges the results, so the links end up in sys_refindex like the
 * ones of a native link field.
 *
 * Enable it with the softref key on the field:
 *
 * .. code-block:: php
 *
 *    'config' => [
 *        'type' => 'user',
 *        'renderType' => 'multiColumnWizard',
 *        'softref' => 'multiColumnWizardTypolink',
 *        'columnFields' => [...],
 *    ],
 */
final class MultiColumnWizardSoftReferenceParser extends AbstractSoftReferenceParser
{
    public function __construct(
        private readonly TypolinkSoftReferenceParser $typolinkSoftReferenceParser,
    ) {}

    /*########################*/
    /*####PUBLIC FUNCTIONS####*/
    /*########################*/

    /**
     * Parses the links of every row of a multiColumnWizard field.
     *
     * @param string $table The table the field belongs to.
     * @param string $field The field name.
     * @param int $uid The uid of the record.
     * @param string $content The JSON string stored by the wizard.
     * @param string $structurePath Path prefix of the value inside the record.
     * @return SoftReferenceParserResult The matched elements and the tokenized JSON.
     */
    public function parse(string $table, string $field, int $uid, string $content, string $structurePath = ''): SoftReferenceParserResult
    {
        $linkColumns = $this->getLinkColumns($table, $field);
        if ($linkColumns === []) {
            return SoftReferenceParserResult::createWithoutMatches();
        }

        // Rows are written by the wizard's JavaScript. Malformed content is skipped instead
        // of throwing: a single broken record must not abort a reference index run.
        $rows = json_decode($content, true);
        if (!is_array($rows)) {
            return SoftReferenceParserResult::createWithoutMatches();
        }

        $elements = [];
        foreach ($rows as $rowIndex => $row) {
            if (!is_array($row)) {
                continue;
            }

            foreach ($linkColumns as $columnName) {
                $link = (string)($row[$columnName] ?? '');
                if ($link === '') {
                    continue;
                }

                // The structure path makes the token id of every link unique, because
                // AbstractSoftReferenceParser::makeTokenID() hashes it along with table,
                // uid and field. The parser key of the delegate is deliberately left
                // untouched: the parsers are shared services, and the factory resets the
                // key on every retrieval anyway.
                $result = $this->typolinkSoftReferenceParser->parse(
                    $table,
                    $field,
                    $uid,
                    $link,
                    $structurePath . $rowIndex . '/' . $columnName
                );

                if (!$result->hasMatched()) {
                    continue;
                }

                $elements += $result->getMatchedElements();

                if ($result->hasContent()) {
                    $rows[$rowIndex][$columnName] = $result->getContent();
                }
            }
        }

        if ($elements === []) {
            return SoftReferenceParserResult::createWithoutMatches();
        }

        return SoftReferenceParserResult::create((string)json_encode($rows), $elements);
    }

    /*#######################*/
    /*###PRIVATE FUNCTIONS###*/
    /*#######################*/

    /**
     * Collects the names of all columns of type "link" of a multiColumnWizard field.
     *
     * Column definitions may also come from the columnsOverrides of a record type, which
     * the reference index does not resolve because it works on the raw record. All of them
     * are therefore unioned. A column name used as a link in one record type and as
     * something else in another is consequently parsed as a link everywhere.
     *
     * @param string $table The table the field belongs to.
     * @param string $field The field name.
     * @return string[] The column names.
     */
    private function getLinkColumns(string $table, string $field): array
    {
        $columnFieldSets = [$GLOBALS['TCA'][$table]['columns'][$field]['config']['columnFields'] ?? []];

        foreach ($GLOBALS['TCA'][$table]['types'] ?? [] as $type) {
            $columnFieldSets[] = $type['columnsOverrides'][$field]['config']['columnFields'] ?? [];
        }

        $linkColumns = [];
        foreach ($columnFieldSets as $columnFields) {
            foreach ($columnFields as $columnName => $columnField) {
                if (($columnField['type'] ?? '') === 'link') {
                    $linkColumns[$columnName] = $columnName;
                }
            }
        }

        return array_values($linkColumns);
    }
}
