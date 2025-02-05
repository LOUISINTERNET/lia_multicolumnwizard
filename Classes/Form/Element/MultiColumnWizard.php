<?php

declare(strict_types=1);

/*
* This file is part of the "lia_multicolumnwizard" Extension for TYPO3 CMS.
*
* For the full copyright and license information, please read the
* LICENSE.txt file that was distributed with this source code.
*/

namespace LIA\LiaMulticolumnwizard\Form\Element;

use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Form\Event\ModifyLinkExplanationEvent;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\LinkHandling\Exception\UnknownLinkHandlerException;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\LinkHandling\TypoLinkCodecService;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\Exception\FolderDoesNotExistException;
use TYPO3\CMS\Core\Resource\Exception\InvalidPathException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Service\DependencyOrderingService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Core\View\ViewFactoryData;

/**
 * This class defines the MultiColumnWizard for rendering and managing form elements with multiple columns.
 */
class MultiColumnWizard extends AbstractFormElement
{
    /*########################*/
    /*####PUBLIC FUNCTIONS####*/
    /*########################*/

    public function __construct(
        private ViewFactoryInterface $viewFactory,
        private IconFactory $iconFactory,
    ) {}

    /**
     * Renders the form element and returns the result array.
     *
     * Gets a view and sets the template, saved values, and link explanations
     * to be rendered as part of the form element. This method also loads necessary stylesheets
     * and JavaScript modules for the MultiColumnWizard.
     *
     * @return array The rendered form element in array format.
     */
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();
        $view = $this->createCustomView();

        $savedValues = json_decode($this->data['parameterArray']['itemFormElValue'], true);

        $linkFields = $this->getLinkFields();
        $linkExplanations = $this->getLinkExplanations($savedValues, $linkFields);

        $view->assignMultiple([
            'data' => $this->data,
            'linkExplanation' => json_encode($linkExplanations),
        ]);

        $resultArray['html'] = $view->render();
        $resultArray['stylesheetFiles'][] = 'EXT:lia_multicolumnwizard/Resources/Public/Stylesheets/Multicolumnwizard.css';
        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create('@lia_multicolumnwizard/backend/Multicolumnwizard.js');

        return $resultArray;
    }

    /*#######################*/
    /*###PRIVATE FUNCTIONS###*/
    /*#######################*/

    /**
     * Retrieves an explanation for a link based on the provided item value.
     *
     * This method decodes the link, resolves its target, and formats the result to
     * include any additional attributes. It handles exceptions and returns a default
     * structure if the link is invalid or cannot be resolved.
     *
     * @param string $itemValue The item value (link) to be explained.
     * @return array The explanation of the link.
     */
    private function getLinkExplanation(string $itemValue): array
    {
        if ($itemValue === '') {
            return [];
        }

        $data = ['text' => '', 'icon' => ''];
        $typolinkService = GeneralUtility::makeInstance(TypoLinkCodecService::class);
        $linkParts = $typolinkService->decode($itemValue);
        $linkService = GeneralUtility::makeInstance(LinkService::class);

        try {
            $linkData = $linkService->resolve($linkParts['url']);
        } catch (FileDoesNotExistException | FolderDoesNotExistException | UnknownLinkHandlerException | InvalidPathException $e) {
            return $data;
        }

        $additionalAttributes = [];
        foreach ($linkParts as $key => $value) {
            if ($key === 'url') {
                continue;
            }

            if ($value) {
                $label = match ((string)$key) {
                    'class' => $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_browse_links.xlf:class'),
                    'title' => $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_browse_links.xlf:title'),
                    'additionalParams' => $this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_browse_links.xlf:params'),
                    default => (string)$key
                };

                $additionalAttributes[] = '<span><strong>' . htmlspecialchars($label) . ': </strong> ' . htmlspecialchars($value) . '</span>';
            }
        }

        $data = match ($linkData['type']) {
            LinkService::TYPE_PAGE => $this->getPageLinkData($linkData),
            LinkService::TYPE_EMAIL => $this->getEmailLinkData($linkData),
            LinkService::TYPE_URL => $this->getUrlLinkData($linkData),
            LinkService::TYPE_FILE => $this->getFileLinkData($linkData),
            LinkService::TYPE_FOLDER => $this->getFolderLinkData($linkData),
            LinkService::TYPE_RECORD => $this->getRecordLinkData($linkData),
            LinkService::TYPE_TELEPHONE => $this->getTelephoneLinkData($linkData),
            LinkService::TYPE_UNKNOWN => $this->getUnknownLinkData($linkData),
            default => $this->getDefaultLinkData($linkData),
        };

        $data['additionalAttributes'] = '<div class="form-text">' . implode(' - ', $additionalAttributes) . '</div>';

        return GeneralUtility::makeInstance(EventDispatcherInterface::class)->dispatch(
            new ModifyLinkExplanationEvent($data, $linkData, $linkParts, $this->data)
        )->getLinkExplanation();
    }

    /**
     * Sets up a ViewFactory and create a View
     *
     * This method sets up the template and partial root paths required for rendering
     * the MultiColumnWizard.
     *
     * @return ViewInterface
     */
    private function createCustomView(): ViewInterface
    {
        $templatePaths = [
            'templateRootPaths' => [
                'EXT:lia_multicolumnwizard/Resources/Private/Backend/Templates'
            ],
            'partialRootPaths' => [
                'EXT:lia_multicolumnwizard/Resources/Private/Backend/Partials'
            ]
        ];
        $templatePaths = $this->appendTemplateOverridesFromPagets($this->data['pageTsConfig'], $templatePaths);

        $viewFactoryData = new ViewFactoryData(
            templateRootPaths: $templatePaths['templateRootPaths'],
            partialRootPaths: $templatePaths['partialRootPaths'],
            layoutRootPaths: ['EXT:lia_multicolumnwizard/Resources/Private/Backend/Layouts'],
        );

        $view = $this->viewFactory->create($viewFactoryData);
        return $view;
    }

    /**
     * Retrieves the list of link fields defined in the column configuration.
     *
     * @return array An array of link field names.
     */
    private function getLinkFields(): array
    {
        $linkFields = [];
        foreach ($this->data['parameterArray']['fieldConf']['config']['columnFields'] as $columnName => $columnField) {
            if ($columnField['type'] == 'link') {
                $linkFields[$columnName] = $columnName;
            }
        }
        return $linkFields;
    }

    /**
     * Retrieves link explanations for the saved values.
     *
     * @param ?array $savedValues The saved values.
     * @param ?array $linkFields The link fields.
     *
     * @return array The link explanations.
     */
    private function getLinkExplanations(?array $savedValues, ?array $linkFields): array
    {
        $linkExplanations = [];
        if ($savedValues && $linkFields) {
            foreach ($savedValues as $key => $value) {
                foreach ($linkFields as $linkField) {
                    if ($value[$linkField]) {
                        $linkExplanations[$key][$linkField] = $this->getLinkExplanation((string)$value[$linkField]);
                    }
                }
            }
        }
        return $linkExplanations;
    }

    /**
     * Retrieves data for a page link.
     *
     * This method returns the text and icon for a page link, including fragment titles if available.
     *
     * @param array $linkData The resolved link data.
     *
     * @return array The text and icon for the page link.
     */
    private function getPageLinkData(array $linkData): array
    {
        $pageRecord = BackendUtility::readPageAccess($linkData['pageuid'] ?? null, '1=1');
        $data = [];

        if ($pageRecord['uid'] ?? 0) {
            $fragmentTitle = '';
            if (isset($linkData['fragment'])) {
                if (MathUtility::canBeInterpretedAsInteger($linkData['fragment'])) {
                    $contentElement = BackendUtility::getRecord('tt_content', (int)$linkData['fragment'], '*', 'pid=' . $pageRecord['uid']);
                    if ($contentElement) {
                        $fragmentTitle = BackendUtility::getRecordTitle('tt_content', $contentElement, false, false);
                    }
                }
                $fragmentTitle = ' #' . ($fragmentTitle ?: $linkData['fragment']);
            }
            $data = [
                'text' => $pageRecord['_thePathFull'] . '[' . $pageRecord['uid'] . ']' . $fragmentTitle,
                'icon' => $this->iconFactory->getIconForRecord('pages', $pageRecord, IconSize::SMALL)->render(),
            ];
        }

        return $data;
    }

    /**
     * Retrieves data for an email link.
     *
     * @param array $linkData The resolved link data.
     *
     * @return array The text and icon for the email link.
     */
    private function getEmailLinkData(array $linkData): array
    {
        $data = [
            'text' => $linkData['email'] ?? '',
            'icon' => $this->iconFactory->getIcon('content-elements-mailform', IconSize::SMALL)->render(),
        ];
        return $data;
    }

    /**
     * Retrieves data for a URL link.
     *
     * @param array $linkData The resolved link data.
     *
     * @return array The text and icon for the URL link.
     */
    private function getUrlLinkData(array $linkData): array
    {
        $data = [
            'text' => $linkData['url'] ?? '',
            'icon' => $this->iconFactory->getIcon('apps-pagetree-page-shortcut-external', IconSize::SMALL)->render(),

        ];
        return $data;
    }

    /**
     * Retrieves data for a file link.
     *
     * @param array $linkData The resolved link data.
     *
     * @return array The text and icon for the file link.
     */
    private function getFileLinkData(array $linkData): array
    {
        $file = $linkData['file'] ?? null;
        $data = [];

        if ($file instanceof File) {
            $data = [
                'text' => $file->getPublicUrl(),
                'icon' => $this->iconFactory->getIconForFileExtension($file->getExtension(), IconSize::SMALL)->render(),
            ];
        }
        return $data;
    }

    /**
     * Retrieves data for a folder link.
     *
     * @param array $linkData The resolved link data.
     *
     * @return array The text and icon for the folder link.
     */
    private function getFolderLinkData(array $linkData): array
    {
        $folder = $linkData['folder'] ?? null;
        $data = [];

        if ($folder instanceof Folder) {
            $data = [
                'text' => $folder->getPublicUrl(),
                'icon' => $this->iconFactory->getIcon('apps-filetree-folder-default', IconSize::SMALL)->render(),
            ];
        }
        return $data;
    }

    /**
     * Retrieves data for a record link.
     *
     * @param array $linkData The resolved link data.
     *
     * @return array The text and icon for the record link.
     */
    private function getRecordLinkData(array $linkData): array
    {
        $table = $this->data['pageTsConfig']['TCEMAIN.']['linkHandler.'][$linkData['identifier'] . '.']['configuration.']['table'] ?? '';
        $record = BackendUtility::getRecord($table, $linkData['uid']);
        $data = [];

        if ($record) {
            $recordTitle = BackendUtility::getRecordTitle($table, $record);
            $tableTitle = $this->getLanguageService()->sL($GLOBALS['TCA'][$table]['ctrl']['title']);
            $data = [
                'text' => sprintf('%s [%s:%d]', $recordTitle, $tableTitle, $linkData['uid']),
                'icon' => $this->iconFactory->getIconForRecord($table, $record, IconSize::SMALL)->render(),
            ];
        } else {
            $data = [
                'text' => sprintf('%s', $linkData['uid']),
                'icon' => $this->iconFactory->getIcon('tcarecords-' . $table . '-default', IconSize::SMALL, 'overlay-missing')->render(),
            ];
        }
        return $data;
    }

    /**
     * Retrieves data for a telephone link.
     *
     * @param array $linkData The resolved link data.
     *
     * @return array The text and icon for the telephone link.
     */
    private function getTelephoneLinkData(array $linkData): array
    {
        $telephone = $linkData['telephone'];
        $data = [];

        if ($telephone) {
            $data = [
                'text' => $telephone,
                'icon' => $this->iconFactory->getIcon('actions-device-mobile', IconSize::SMALL)->render(),
            ];
        }
        return $data;
    }

    /**
     * Retrieves data for an unknown link type.
     *
     * @param array $linkData The resolved link data.
     *
     * @return array The text and icon for the unknown link type.
     */
    private function getUnknownLinkData(array $linkData): array
    {
        $data = [
            'text' => $linkData['file'] ?? $linkData['url'] ?? '',
            'icon' => $this->iconFactory->getIcon('actions-link', IconSize::SMALL)->render(),
        ];
        return $data;
    }

    /**
     * Retrieves data for the default link type (not implemented).
     *
     * @param array $linkData The resolved link data.
     *
     * @return array The default link data indicating the type is not implemented.
     */
    private function getDefaultLinkData(array $linkData): array
    {
        $data = [
            'text' => 'not implemented type ' . $linkData['type'],
            'icon' => '',
        ];
        return $data;
    }

    /*#######################*/
    /*##PROTECTED FUNCTIONS##*/
    /*#######################*/

    /**
     * Append template overrides from PageTS configuration (if any).
     *
     * This method appends template paths for overriding default templates based on
     * the PageTS configuration.
     *
     * @param array $pageTs The PageTS configuration.
     * @param array $templatePaths The default template paths.
     * @throws \RuntimeException If the override syntax is incorrect.
     * @return array The modified template paths with overrides applied.
     */
    protected function appendTemplateOverridesFromPagets(array $pageTs, array $templatePaths): array
    {
        if (empty($pageTs)) {
            return $templatePaths;
        }

        if (is_array($pageTs['templates.']['typo3_ext/lia_multicolumnwizard.'] ?? false)) {
            $overrides = $pageTs['templates.']['typo3_ext/lia_multicolumnwizard.'];
            ksort($overrides);
            $packageManager = GeneralUtility::makeInstance(PackageManager::class, new DependencyOrderingService());

            foreach ($overrides as $override) {
                $pathParts = GeneralUtility::trimExplode(':', $override, true);
                if (count($pathParts) < 2) {
                    throw new \RuntimeException(
                        'When overriding template paths, the syntax is "composer-package-name:path", example: "typo3/cms-seo:Resources/Private/TemplateOverrides/typo3/cms-backend"',
                        1643798660
                    );
                }
                $composerPackageName = $pathParts[0];
                $overridePackagePath = $packageManager->getPackage($composerPackageName)->getPackagePath();
                $overridePath = rtrim($pathParts[1], '/');
                $templatePaths['templateRootPaths'][] = $overridePackagePath . $overridePath . '/Templates';
                $templatePaths['layoutRootPaths'][] = $overridePackagePath . $overridePath . '/Layouts';
                $templatePaths['partialRootPaths'][] = $overridePackagePath . $overridePath . '/Partials';
            }
        }

        return $templatePaths;
    }
}
