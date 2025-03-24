<?php

declare(strict_types=1);

/*
* This file is part of the "lia_multicolumnwizard" Extension for TYPO3 CMS.
*
* For the full copyright and license information, please read the
* LICENSE.txt file that was distributed with this source code.
*/

namespace LIA\LiaMulticolumnwizard\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Check if the given MultiColumnWizard field has any values.
 * Checks every field in every row and returns true if every value is empty.
 *
 * Examples
 * ========
 *
 * Usage in a condition like this:
 *
 * .. code-block:: html
 *
 *    <f:if condition="!{mcw:isEmpty(json: data.tx_lia_multicolumnwizard)}">
 *        <!-- Your content -->
 *    </f:if>
 */
class IsEmptyViewHelper extends AbstractViewHelper
{
    /**
     * Initialize ViewHelper Arguments.
     *
     * Registers a 'json' argument, which is expected to be a JSON string representing
     * the data from the MulticolumnWizard.
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('json', 'string', 'The MultiColumnWizard-json string.', true);
    }

    /**
     * Determines if the provided JSON string is considered "empty."
     *
     * A JSON string is considered empty if:
     *  - The JSON string is empty.
     *  - The decoded JSON is invalid or results in an empty array.
     *  - After decoding, all values within the array are either empty or null.
     *
     * @return bool True if the JSON is empty or contains no significant data, otherwise false.
     */
    public function render(): bool
    {
        $jsonString = $this->arguments['json'];

        if (empty($jsonString)) {
            return true;
        }

        $array = json_decode((string)$jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($array)) {
            return true;
        }

        $mcwArray = array_filter(
            array_map(
                fn($value) => array_filter($value, fn($val) => !empty($val)),
                $array
            ),
            fn($arr) => $arr !== null && $arr !== []
        );

        return $mcwArray === [];
    }
}
