<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Tenant;
use App\Rules\SubdomainRule;
use App\Settings\DashboardSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class UpdateTenantSubdomain
{
    /**
     * @throws Throwable
     */
    public function handle(Tenant $tenant, string $subdomain): bool
    {
        $validator = Validator::make([
            'subdomain' => $subdomain,
        ], [
            'subdomain' => [Rule::unique('mysql.domains', 'domain')->ignore($tenant->custom_domain?->id), new SubdomainRule],
        ]);

        if ($validator->fails()) {
            return false;
        }

        return DB::transaction(function () use ($tenant, $validator): true {
            $action = new ResetTenantSubdomain;
            $action->handle($tenant);

            $tenant->domains()->create([
                'domain' => $subdomain = $validator->getValue('subdomain'),
                'is_custom_subdomain' => true,
            ]);

            /** @var DashboardSettings $settings */
            $settings = app()->make(DashboardSettings::class);
            $settings->subdomain = $subdomain;
            $settings->save();

            return true;
        });
    }
}
