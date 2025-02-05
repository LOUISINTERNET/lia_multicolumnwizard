<?php

declare(strict_types=1);

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
    /*########################*/
    /*####PUBLIC FUNCTIONS####*/
    /*########################*/

    /**
     * Retrieves a list of available languages for all configured sites.
     *
     * This method fetches all available sites in the TYPO3 installation and
     * iterates through each site's languages. It returns an array where the keys
     * are the language IDs and the values are the language titles.
     *
     * Predefined values include:
     * - `-2 => ''`: Represents an empty selection.
     * - `-1 => 'All'`: Represents a selection for all languages.
     *
     * The resulting array is sorted by language ID in ascending order.
     *
     * @return array $siteLanguages An array of available languages with language ID as key and language title as value.
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
