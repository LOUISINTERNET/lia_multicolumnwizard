<?php

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
use TYPO3\CMS\Core\Imaging\Icon;
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
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * MultiColumnWizard
 */
class MultiColumnWizard extends AbstractFormElement
{
    /**
     * @var StandaloneView $standaloneView
     */
    private $standaloneView;

    /*########################*/
    /*####PUBLIC FUNCTIONS####*/
    /*########################*/

    /**
     * Handler for single nodes
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();
        $this->initStandaloneView();
        $this->standaloneView->setTemplate('Multicolumnwizard');
        $savedValues = json_decode($this->data['parameterArray']['itemFormElValue'], true);
        $linkFields = [];
        foreach ($this->data['parameterArray']['fieldConf']['config']['columnFields'] as $columnName => $columnField) {
            if ($columnField['type'] == 'link') {
                $linkFields[$columnName] = $columnName;
            }
        }
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
        $this->standaloneView->assignMultiple([
            'data' => $this->data,
            'linkExplanation' => json_encode($linkExplanations),
        ]);

        $resultArray['html'] = $this->standaloneView->render();

        $resultArray['stylesheetFiles'][] = 'EXT:lia_multicolumnwizard/Resources/Public/Stylesheets/Multicolumnwizard.css';

        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create('@lia_multicolumnwizard/backend/Multicolumnwizard.js');
        return $resultArray;
    }

    /**
     * @return array
     */
    protected function getLinkExplanation(string $itemValue): array
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
        } catch (FileDoesNotExistException|FolderDoesNotExistException|UnknownLinkHandlerException|InvalidPathException $e) {
            return $data;
        }

        // Resolving the TypoLink parts (class, title, params)
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

        // Resolve the actual link
        switch ($linkData['type']) {
            case LinkService::TYPE_PAGE:
                $pageRecord = BackendUtility::readPageAccess($linkData['pageuid'] ?? null, '1=1');
                // Is this a real page
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
                        'icon' => $this->iconFactory->getIconForRecord('pages', $pageRecord, Icon::SIZE_SMALL)->render(),
                    ];
                }
                break;
            case LinkService::TYPE_EMAIL:
                $data = [
                    'text' => $linkData['email'] ?? '',
                    'icon' => $this->iconFactory->getIcon('content-elements-mailform', Icon::SIZE_SMALL)->render(),
                ];
                break;
            case LinkService::TYPE_URL:
                $data = [
                    'text' => $linkData['url'] ?? '',
                    'icon' => $this->iconFactory->getIcon('apps-pagetree-page-shortcut-external', Icon::SIZE_SMALL)->render(),

                ];
                break;
            case LinkService::TYPE_FILE:
                $file = $linkData['file'] ?? null;
                if ($file instanceof File) {
                    $data = [
                        'text' => $file->getPublicUrl(),
                        'icon' => $this->iconFactory->getIconForFileExtension($file->getExtension(), Icon::SIZE_SMALL)->render(),
                    ];
                }
                break;
            case LinkService::TYPE_FOLDER:
                $folder = $linkData['folder'] ?? null;
                if ($folder instanceof Folder) {
                    $data = [
                        'text' => $folder->getPublicUrl(),
                        'icon' => $this->iconFactory->getIcon('apps-filetree-folder-default', Icon::SIZE_SMALL)->render(),
                    ];
                }
                break;
            case LinkService::TYPE_RECORD:
                $table = $this->data['pageTsConfig']['TCEMAIN.']['linkHandler.'][$linkData['identifier'] . '.']['configuration.']['table'] ?? '';
                $record = BackendUtility::getRecord($table, $linkData['uid']);
                if ($record) {
                    $recordTitle = BackendUtility::getRecordTitle($table, $record);
                    $tableTitle = $this->getLanguageService()->sL($GLOBALS['TCA'][$table]['ctrl']['title']);
                    $data = [
                        'text' => sprintf('%s [%s:%d]', $recordTitle, $tableTitle, $linkData['uid']),
                        'icon' => $this->iconFactory->getIconForRecord($table, $record, Icon::SIZE_SMALL)->render(),
                    ];
                } else {
                    $data = [
                        'text' => sprintf('%s', $linkData['uid']),
                        'icon' => $this->iconFactory->getIcon('tcarecords-' . $table . '-default', Icon::SIZE_SMALL, 'overlay-missing')->render(),
                    ];
                }
                break;
            case LinkService::TYPE_TELEPHONE:
                $telephone = $linkData['telephone'];
                if ($telephone) {
                    $data = [
                        'text' => $telephone,
                        'icon' => $this->iconFactory->getIcon('actions-device-mobile', Icon::SIZE_SMALL)->render(),
                    ];
                }
                break;
            case LinkService::TYPE_UNKNOWN:
                $data = [
                    'text' => $linkData['file'] ?? $linkData['url'] ?? '',
                    'icon' => $this->iconFactory->getIcon('actions-link', Icon::SIZE_SMALL)->render(),
                ];
                break;
            default:
                $data = [
                    'text' => 'not implemented type ' . $linkData['type'],
                    'icon' => '',
                ];
        }

        $data['additionalAttributes'] = '<div class="form-text">' . implode(' - ', $additionalAttributes) . '</div>';

        return GeneralUtility::makeInstance(EventDispatcherInterface::class)->dispatch(
            new ModifyLinkExplanationEvent($data, $linkData, $linkParts, $this->data)
        )->getLinkExplanation();
    }

    /*#######################*/
    /*###PRIVATE FUNCTIONS###*/
    /*#######################*/

    /**
     * Initialize a StandaloneView Object
     */
    private function initStandaloneView(): void
    {
        $templatePaths = [
            'templateRootPaths' => [
                GeneralUtility::getFileAbsFileName('EXT:lia_multicolumnwizard/Resources/Private/Backend/Templates'),
            ],
            'partialRootPaths' => [
                GeneralUtility::getFileAbsFileName('EXT:lia_multicolumnwizard/Resources/Private/Backend/Partials'),
            ],
        ];
        $templatePaths = $this->appendTemplateOverridesFromPagets($this->data['pageTsConfig'], $templatePaths);

        $this->standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $this->standaloneView->setTemplateRootPaths($templatePaths['templateRootPaths']);
        $this->standaloneView->setPartialRootPaths($templatePaths['partialRootPaths']);
        $this->standaloneView->setFormat('html');
    }

    /**
     * Append Template Overrides from PageTS (if any)
     *
     * @param array $pageTs
     * @param array $templatePaths
     * @throws \RuntimeException
     *
     * @return array
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
