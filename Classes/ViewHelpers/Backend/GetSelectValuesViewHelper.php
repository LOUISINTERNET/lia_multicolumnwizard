<?php

declare(strict_types=1);

/*
 * This file is part of the "lia_multicolumnwizard" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace LIA\LiaMulticolumnwizard\ViewHelpers\Backend;

use LIA\LiaMulticolumnwizard\Exceptions\Backend\WrongOptionsReturnTypeException;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to retrieve select values from a MultiColumnWizard configuration or a callable function.
 *
 * This ViewHelper can either return the configuration array directly or call a specified method
 * to retrieve the select values dynamically.
 * 
 * .. caution::
 *    This ViewHelper can be used in the backend context only. 
 *    In all other context it will just return the configuration array.
 * 
 * Example usage:
 * --------------
 *
 * .. code::
 *    :caption: Example ViewHelper usage
 * 
 *    <lia:getSelectValues configuration="{data.configuration}" optionsFunction="{data.function}" />
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
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'configuration',
            'string',
            'The json string of the MultiColumnWizard field.'
        );
        $this->registerArgument(
            'optionsFunction',
            'string',
            'This array has to contain the full classname, the method to call and all the parameter that are needed to call this method.'
        );
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
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if (empty($request) || !$request instanceof ServerRequestInterface) {
            return $this->arguments['configuration'];
        }

        // This ViewHelper should only used in the backend application context!
        if (!ApplicationType::fromRequest($request)->isBackend()) {
            return $this->arguments['configuration'];
        }

        if ($this->arguments['optionsFunction'] == '') {
            if (is_array($this->arguments['configuration'])) {
                return $this->arguments['configuration'];
            }

            return [];
        }

        [$funcName, $params] = $this->arguments['optionsFunction'];
        if (!is_array($params)) {
            $params = [];
        }

        $userFuncReturn = GeneralUtility::callUserFunction(
            $funcName,
            $params
        );

        if (is_array($userFuncReturn)) {
            return $userFuncReturn;
        } else {
            throw new WrongOptionsReturnTypeException(
                'The function you use has to return an array.',
                1740144629
            );
        }

        return [];
    }
}
