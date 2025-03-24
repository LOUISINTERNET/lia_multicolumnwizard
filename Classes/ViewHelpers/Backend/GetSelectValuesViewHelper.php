<?php

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
 * This ViewHelper is used to generate the select options for the configured select field.
 * It should only be used in the backend context.
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
     * Render
     *
     * @return array
     *
     * @throws WrongOptionsReturnTypeException
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

        $userFuncReturn = GeneralUtility::callUserFunction(
            $funcName,
            $params ?? []
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
