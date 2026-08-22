<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\Use_\SeparateMultiUseImportsRector;
use Rector\Config\RectorConfig;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ParamTypeByMethodCallTypeRector;
use RectorLaravel\Set\LaravelLevelSetList;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withComposerBased(laravel: true)
    ->withCache(
        cacheDirectory: __DIR__.'/.rector.cache',
    )
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/bootstrap/app.php',
        __DIR__.'/config',
        __DIR__.'/operations',
        __DIR__.'/public',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->withSkip([
        ParamTypeByMethodCallTypeRector::class => [
            __DIR__.'/app/Http/Controllers/Api/AuthorizesRequests.php',
        ],
        SeparateMultiUseImportsRector::class => [
            __DIR__.'/app/Models/User.php',
        ],
        StringClassNameToClassConstantRector::class,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        privatization: true,
        earlyReturn: true,
        carbon: true,
    )
    ->withImportNames(
        importDocBlockNames: false,
        removeUnusedImports: true,
    )
    ->withPhpSets()
    ->withSets([
        LaravelLevelSetList::UP_TO_LARAVEL_120,
        LaravelSetList::ARRAY_STR_FUNCTIONS_TO_STATIC_CALL,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,
        LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
    ])
    ->withParallel(
        timeoutSeconds: 600,
        maxNumberOfProcess: 16,
        jobSize: 16,
    );
