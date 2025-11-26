<?php

require_once __DIR__ . '/../../src/Plugin.php';

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/ajax',
        __DIR__ . '/front',
        __DIR__ . '/inc',
        __DIR__ . '/tests',
    ])
    ->withPhpVersion(PhpVersion::PHP_82)
    ->withCache(
        cacheDirectory: __DIR__ . '/var/rector',
        cacheClass: FileCacheStorage::class,
    )
    ->withRootFiles()
    ->withParallel(timeoutSeconds: 300)
    ->withImportNames(removeUnusedImports: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
    )
    ->withPhpSets(php82: true) // apply PHP sets up to PHP 8.2
;
