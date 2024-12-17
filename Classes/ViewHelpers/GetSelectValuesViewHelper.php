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
 * GetMultiColumnWizardValues ViewHelper
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
        $this->registerArgument('configuration', 'string', 'Json');
        $this->registerArgument('optionsFunction', 'string', 'Json');
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
