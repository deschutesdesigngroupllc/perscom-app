<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Traits\Conditionable;

class RemoveTenantAccount
{
    use Conditionable;

    protected Tenant $tenant;

    protected bool $shouldDeleteFiles = true;

    public function handle(Tenant $tenant): void
    {
        $this->tenant = $tenant;

        $this->tenant->run(function (): void {
            $this->when($this->shouldDeleteFiles, fn () => $this->deleteFiles());
        });
    }

    public function shouldDeleteFiles(bool $deleteFiles = true): static
    {
        $this->shouldDeleteFiles = $deleteFiles;

        return $this;
    }

    protected function deleteFiles(): void
    {
        Storage::disk('s3')->deleteDirectory('/');
    }
}
