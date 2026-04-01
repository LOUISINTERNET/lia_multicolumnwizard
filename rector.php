<?php

/**
 * Rector configuration for TYPO3 v14 extension upgrade
 *
 * Usage:
 *   ./vendor/bin/rector process --dry-run  # Preview changes
 *   ./vendor/bin/rector process            # Apply changes
 *
 * Requires: composer require --dev ssch/typo3-rector:^3.11
 */

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\StaticCall\RemoveParentCallWithoutParentRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\PostRector\Rector\NameImportingPostRector;
use Rector\Set\ValueObject\LevelSetList;
use Ssch\TYPO3Rector\Set\Typo3LevelSetList;
use Ssch\TYPO3Rector\Set\Typo3SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/Classes',
        __DIR__ . '/Configuration',
        __DIR__ . '/ext_localconf.php',
    ]);
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);
    $rectorConfig->removeUnusedImports();

    // Define what rule sets will be applied
    $rectorConfig->sets([
        // PHP level upgrades (TYPO3 v14 minimum is PHP 8.2)
        LevelSetList::UP_TO_PHP_82,

        // v13+v14 dual compatibility: use v13 rules only
        Typo3LevelSetList::UP_TO_TYPO3_13,

        // TYPO3 code quality and general improvements
        Typo3SetList::CODE_QUALITY,
        Typo3SetList::GENERAL,
    ]);

    $rectorConfig->skip([
        __DIR__ . '/ext_emconf.php',
        __DIR__ . '/.Build',
        __DIR__ . '/vendor',

        // Skip constructor promotion - keep explicit property declarations for clarity
        ClassPropertyAssignToConstructorPromotionRector::class,

        // Skip removing parent calls - may be needed for TYPO3 hooks
        RemoveParentCallWithoutParentRector::class,

        // Don't import names in ext_localconf.php — conditionally-loaded classes
        // (e.g. Solr) must stay as FQCN inside isLoaded() guards
        NameImportingPostRector::class => [__DIR__ . '/ext_localconf.php'],
    ]);
};
