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
 * Check if the given MultiColumnWizard field has any values.
 * Checks every filed in every row and returns true if every value is empty.
 *
 * Examples
 * ========
 *
 * Usage in a condition like this: 
 * 
 * .. code-block:: html
 * 
 *   <f:if condition="!{mcw:isEmpty(json: data.multicolumnwizard)}"> ... YOUR CODE ... </f:if>
 *
 */
class IsEmptyViewHelper extends AbstractViewHelper
{
    /**
     * Initialize ViewHelper Arguments.
     */
    public function initializeArguments(): void   
    {
        $this->registerArgument('json', 'string', 'The MultiColumnWizard-json string.', true);
    }

    /**
     * Check if the given JSON string is empty.
     *
     * @return bool
     */
    public function render(): bool
    {
        $array = json_decode($this->arguments['json'], true);
        if (empty($array)) {
            return true;
        }

        $mcwArray = array_filter(
            array_map(
                fn($value) => array_filter($value, fn($val) => !empty($val)),
                $array
            ),
            fn($arr) => !empty($arr)
        );

        return empty($mcwArray);
    }
}
