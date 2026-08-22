<?php

declare(strict_types=1);

namespace App\Support\Backup;

use Spatie\TemporaryDirectory\TemporaryDirectory;

class TenantTemporaryDirectory extends TemporaryDirectory
{
    protected function getFullPath(): string
    {
        $base = storage_path('app/backup-temp');

        $tenantId = tenant()?->getTenantKey();

        if (filled($tenantId)) {
            $base .= DIRECTORY_SEPARATOR.'tenant'.$tenantId;
        }

        return $base.(blank($this->name) ? '' : DIRECTORY_SEPARATOR.$this->name);
    }
}
