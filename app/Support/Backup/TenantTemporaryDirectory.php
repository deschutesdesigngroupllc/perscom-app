<?php

declare(strict_types=1);

namespace App\Support\Backup;

use Spatie\TemporaryDirectory\TemporaryDirectory;

class TenantTemporaryDirectory extends TemporaryDirectory
{
    public function __construct()
    {
        parent::__construct(storage_path('app/backup-temp'));
    }

    protected function getFullPath(): string
    {
        $tenantId = tenant()?->getTenantKey() ?? null;

        if (blank($tenantId)) {
            return parent::getFullPath();
        }

        return storage_path("app/backup-temp/tenant$tenantId").(! empty($this->name) ? DIRECTORY_SEPARATOR.$this->name : '');
    }
}
