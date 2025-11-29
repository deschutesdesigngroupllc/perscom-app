<?php

declare(strict_types=1);

namespace App\Support\Tenancy\Bootstrappers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class ConfigBootstrapper implements TenancyBootstrapper
{
    protected ?string $mailFromName = null;

    public function bootstrap(Tenant $tenant): void
    {
        $this->mailFromName = config('mail.from.name', config('app.name'));

        App::forgetInstance('mail.manager');

        Config::set('mail.from.name', 'PERSCOM - '.$tenant->getAttribute('name'));
        Config::set('responsecache.cache_tag', 'tenant'.$tenant->getKey());
    }

    public function revert(): void
    {
        App::forgetInstance('mail.manager');

        Config::set('mail.from.name', $this->mailFromName);
        Config::set('responsecache.cache_tag', '');
    }
}
