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
        $tenantId = tenant()->getTenantKey();

        return storage_path(optional($tenantId, fn ($id) => "app/backup-temp/tenant$id") ?? 'app/backup-temp').DIRECTORY_SEPARATOR.$this->name;
    }
}
