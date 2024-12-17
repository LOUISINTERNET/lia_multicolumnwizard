<?php

/*
* This file is part of the "lia_multicolumnwizard" Extension for TYPO3 CMS.
*
* For the full copyright and license information, please read the
* LICENSE.txt file that was distributed with this source code.
*/

namespace LIA\LiaMulticolumnwizard\Utilities;

use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TCAFactory
{
    /**
     * getLanguages
     *
     * @return array items
     */
    public static function getAvailableLanguagesForAllSites(): array
    {
        $siteLanguages = [-2 => '', -1 => 'All'];
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $sites = $siteFinder->getAllSites();

        foreach ($sites as $site) {
            $allLanguages = $site->getAllLanguages();
            foreach ($allLanguages as $languageId => $language) {
                $languageTitle = $language->getTitle();
                $siteLanguages[$languageId] = $languageTitle;
            }
        }

        ksort($siteLanguages);

        return $siteLanguages;
    }
}
