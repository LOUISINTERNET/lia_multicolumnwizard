<?php

declare(strict_types=1);

/*
* This file is part of the "lia_multicolumnwizard" Extension for TYPO3 CMS.
*
* For the full copyright and license information, please read the
* LICENSE.txt file that was distributed with this source code.
*/

namespace LIA\LiaMulticolumnwizard\Utilities;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class IconFactory
{
    /*########################*/
    /*####PUBLIC FUNCTIONS####*/
    /*########################*/

    /**
     * Retrieves a list of icons from a JSON file.
     *
     * This method reads a JSON file containing icon data and returns an associative array where the keys and values
     * are both the icon names. The method expects a path to the JSON file and attempts to read and parse it.
     *
     * If the file is not readable or if the JSON parsing fails, it returns an array with a default empty option.
     *
     * @param string $iconPath The path to the JSON file containing the icon data.
     *
     * @return array $items An array of icons, where the keys and values are the icon names, or an array with a default option if the file cannot be processed.
     */
    public static function getIcons(string $iconPath): array
    {
        $file = GeneralUtility::getFileAbsFileName($iconPath);
        $items = ['' => '---'];

        if (!is_readable($file)) {
            return $items;
        }

        $fileContents = file_get_contents($file);
        $icons = json_decode($fileContents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $items;
        }

        foreach ($icons as $icon) {
            if ($icon['name'] == 'icons') {
                continue;
            }
            $items[$icon['name']] = $icon['name'];
        }

        return $items;
    }
}
