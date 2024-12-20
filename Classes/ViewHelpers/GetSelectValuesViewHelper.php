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
 * This ViewHelper call a function with given parameter.
 *
 * 
 */
class GetSelectValuesViewHelper extends AbstractViewHelper
{
    /*########################*/
    /*####PUBLIC FUNCTIONS####*/
    /*########################*/

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('configuration', 'string', 'The json string of the MultiColumnWizard field.');
        $this->registerArgument('optionsFunction', 'string', 'This array has to contain the full classname, the method to call and all the parameter that are needed to call this method.');
    }

    /**
     * Render
     *
     * @return array
     */
    public function render(): array
    {
        if ($this->arguments['optionsFunction'] == '') {
            if (is_array($this->arguments['configuration'])) {
                return $this->arguments['configuration'];
            }
            return [];
        }

        [$className, $methodName, $params] = $this->arguments['optionsFunction'];
        return call_user_func_array([$className, $methodName], $params);
    }
}
