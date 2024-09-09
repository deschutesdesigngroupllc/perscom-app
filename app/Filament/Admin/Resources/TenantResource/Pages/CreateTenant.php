<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TenantResource\Pages;

use App\Filament\Admin\Resources\TenantResource;
use App\Models\Tenant;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        /** @var Tenant $tenant */
        $tenant = parent::handleRecordCreation($data);

        if ($domain = data_get($data, 'domain')) {
            $tenant->domains()->create([
                'domain' => $domain,
            ]);

            unset($data['domain']);
        }

        return $tenant;
    }
}
