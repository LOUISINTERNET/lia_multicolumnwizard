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
 * ViewHelper to retrieve select values from a MultiColumnWizard configuration or a callable function.
 *
 * This ViewHelper can either return the configuration array directly or call a specified method
 * to retrieve the select values dynamically.
 *
 * Example usage:
 *
 * <lia:getSelectValues configuration="{data.configuration}" optionsFunction="{data.function}" />
 */
class GetSelectValuesViewHelper extends AbstractViewHelper
{
    /*########################*/
    /*####PUBLIC FUNCTIONS####*/
    /*########################*/

    /**
     * Initialize the ViewHelper arguments.
     *
     * Registers two arguments:
     *  - 'configuration': Either a JSON string or an array containing the select options.
     *  - 'optionsFunction': A string representing a callable (class, method, params) to retrieve the options.
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('configuration', 'string', 'The json string of the MultiColumnWizard field.');
        $this->registerArgument('optionsFunction', 'string', 'This array has to contain the full classname, the method to call and all the parameter that are needed to call this method.');
    }

    /**
     * Returns select values either from the configuration or by calling a specified function.
     *
     * If 'optionsFunction' is empty, the 'configuration' argument is returned (if it's an array).
     * If 'optionsFunction' is provided, it will be used to call a class method with parameters to get the select options.
     *
     * @return array The select values, either from the configuration or the result of the called function.
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
