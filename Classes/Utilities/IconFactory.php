<?php

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
    /**
     * getIcons
     *
     * @param string $iconPath
     *
     * @return array items
     */
    public static function getIcons(string $iconPath = 'EXT:lia_package/Resources/Public/Icons.json'): array
    {
        $file = GeneralUtility::getFileAbsFileName($iconPath);
        $items = ['' => '---'];

        if (!file_exists($file)) {
            return $items;
        }

        foreach (json_decode(file_get_contents($file)) as $icon) {
            if ($icon->name == 'icons') {
                continue;
            }
            $items[$icon->name] = $icon->name;
        }

        return $items;
    }
}
