<?php

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
 * Depending on the current value of `{data.multicolumnwizard}`.
 */
final class GetMultiColumnWizardValuesViewHelper extends AbstractViewHelper
{
    /*########################*/
    /*####PUBLIC FUNCTIONS####*/
    /*########################*/

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('json', 'string', 'The multicolumn wizard json string.', true);
        $this->registerArgument('associative', 'bool', 'If the array should be associative', false, false);
    }

    /**
     * Render
     *
     * @return ?array
     */
    public function render(): ?array
    {
        if ($this->arguments['json'] == '') {
            return null;
        }
        return json_decode($this->arguments['json'], $this->arguments['associative']);
    }
}
