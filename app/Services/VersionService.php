<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;

class VersionService
{
    public static function version()
    {
        return Cache::rememberForever('perscom_version', function () {
            return Process::run('git describe --tags --abbrev=0')->output();
        });
    }
}
