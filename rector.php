<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Use_\SeparateMultiUseImportsRector;
use Rector\Config\RectorConfig;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ParamTypeByMethodCallTypeRector;

return RectorConfig::configure()
    ->withCache(
        cacheDirectory: '/tmp/rector',
        cacheClass: FileCacheStorage::class
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
        AddOverrideAttributeToOverriddenMethodsRector::class,
        EncapsedStringsToSprintfRector::class => [
            __DIR__.'/app/Support/Orion/ComponentsResolver.php',
        ],
        ParamTypeByMethodCallTypeRector::class => [
            __DIR__.'/app/Http/Controllers/Api/AuthorizesRequests.php',
        ],
        SeparateMultiUseImportsRector::class => [
            __DIR__.'/app/Models/User.php',
        ],
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
    ->withPhpSets();
