<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Resources\Roles\Pages;

use App\Filament\App\Clusters\Settings\Resources\Roles\RoleResource;
use App\Models\Permission;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CreateRole extends CreateRecord
{
    public Collection $permissions;

    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissions = collect($data)
            ->filter(fn (mixed $permission, string $key): bool => ! in_array($key, ['name', 'guard_name', 'select_all', Utils::getTenantModelForeignKey()]))
            ->values()
            ->flatten()
            ->unique();

        if (Utils::isTenancyEnabled() && Arr::has($data, Utils::getTenantModelForeignKey()) && filled($data[Utils::getTenantModelForeignKey()])) {
            return Arr::only($data, ['name', 'guard_name', Utils::getTenantModelForeignKey()]);
        }

        return Arr::only($data, ['name', 'guard_name']);
    }

    protected function afterCreate(): void
    {
        $permissionModels = collect();
        $this->permissions->each(function (string $permission) use ($permissionModels): void {
            /** @var class-string<Permission> $permissionModel */
            $permissionModel = Utils::getPermissionModel();
            $permissionModels->push($permissionModel::firstOrCreate([
                'name' => $permission,
            ]));
        });

        /** @phpstan-ignore-next-line method.notFound */
        $this->record->syncPermissions($permissionModels);
    }
}
