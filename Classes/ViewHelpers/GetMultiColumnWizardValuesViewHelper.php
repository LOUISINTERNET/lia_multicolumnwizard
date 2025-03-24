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
 * ViewHelper for parsing the multicolumnwizard json string.
 * Use this to prepare the data from a multicolumnwizard-field into an array.
 *
 * Examples
 * ========
 *
 * Field-data is found in: `data.multicolumnwizard`
 *
 * .. code-block:: html
 *
 *    <f:variable name="mcwArray" value="{mcw:getMultiColumnWizardValues(json: '{data.multicolumnwizard}', associative: true)}" />`
 *
 * Content of `mcwArray`:
 *
 * .. code-block::
 *
 *    array(3) {
 *      'field1' => 'value',
 *      'field2' => 'value',
 *      'field3' => 'value',
 *    }
 *
 * So it can be used like:
 *
 * .. code-block:: html
 *    <span>{mcwArray.field1}</span>
 *
 * Depending on the current value of `{data.multicolumnwizard}` and the `associative` argument.
 */
final class GetMultiColumnWizardValuesViewHelper extends AbstractViewHelper
{
    /*########################*/
    /*####PUBLIC FUNCTIONS####*/
    /*########################*/

    /**
     * Initialize the ViewHelper arguments.
     *
     * Registers two arguments:
     *  - 'json': A JSON string to decode.
     *  - 'associative': A boolean flag to return the decoded JSON as an associative array (default: false).
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('json', 'string', 'The multicolumn wizard json string.', true);
        $this->registerArgument('associative', 'bool', 'If the array should be associative', false, false);
    }

    /**
     * Decodes the provided JSON string.
     *
     * If the JSON string is valid, it returns the decoded data either as an associative array or an object.
     * If the JSON string is empty or invalid, it returns null.
     *
     * @return array|null The decoded JSON data, or null if the JSON is empty or invalid.
     */
    public function render(): ?array
    {
        $json = $this->arguments['json'] ?? '';
        $associative = (bool)($this->arguments['associative'] ?? false);

        if (empty($json)) {
            return null;
        }

        $decoded = json_decode((string)$json, $associative);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $decoded;
    }
}
