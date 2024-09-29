<?php

declare(strict_types=1);

namespace App\Support\Backup;

use Spatie\TemporaryDirectory\TemporaryDirectory;

class TenantTemporaryDirectory extends TemporaryDirectory implements \Spatie\Backup\Contracts\TemporaryDirectory
{
    public function __construct()
    {
        parent::__construct(storage_path('app/backup-temp'));
    }

    protected function getFullPath(): string
    {
        return storage_path('app/backup-temp').DIRECTORY_SEPARATOR.$this->name;
    }
}
