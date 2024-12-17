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
 * Check if the given MulticolumnWizard JSON string is emtpy.
 *
 * HOW TO USE:
 *
 * <f:if condition="!{mcw:isEmpty(json: data.tx_lia_multicolumnwizard)}"> ... YOUR CODE ... </f:if>
 *
 * @authro Johannes Delesky, Louis INTERNET <delesky@louis.info>
 * @extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
 */
class IsEmptyViewHelper extends AbstractViewHelper
{
    /**
     * Initialize ViewHelper Arguments.
     */
    public function initializeArguments()
    {
        $this->registerArgument('json', 'string', 'The multicolumn wizard json string.', true);
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

        if (empty($mcwArray)) {
            return true;
        }

        return false;
    }
}
