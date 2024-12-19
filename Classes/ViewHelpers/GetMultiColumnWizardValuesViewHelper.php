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
class GetMultiColumnWizardValuesViewHelper extends AbstractViewHelper
{
    /*########################*/
    /*####PUBLIC FUNCTIONS####*/
    /*########################*/

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('json', 'string', 'Json');
        $this->registerArgument('associative', 'bool', 'The return type of the json_decode', false, false);
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
