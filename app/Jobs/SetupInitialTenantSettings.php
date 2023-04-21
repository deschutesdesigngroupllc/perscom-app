<?php

namespace App\Jobs;

use App\Models\Role;
use App\Models\Settings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Outl1ne\NovaSettings\NovaSettings;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class SetupInitialTenantSettings implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected TenantWithDatabase $tenant)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->tenant->run(function ($tenant) {
            Settings::withoutEvents(static function () use ($tenant) {
                NovaSettings::setSettingValue('organization', $tenant->name);
                NovaSettings::setSettingValue('email', $tenant->email);
                NovaSettings::setSettingValue('timezone', config('app.timezone'));
                NovaSettings::setSettingValue('dashboard_title', $tenant->name);
                NovaSettings::setSettingValue('registration_enabled', true);
                NovaSettings::setSettingValue('default_roles', Role::query()->where('name', 'User')->pluck('id')->toArray());
            });
        });
    }
}
