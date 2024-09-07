<?php

declare(strict_types=1);

namespace App\Support\IdeHelper;

use App\Models\Tenant;
use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use Barryvdh\LaravelIdeHelper\Contracts\ModelHookInterface;
use Illuminate\Database\Eloquent\Model;

class TenantHook implements ModelHookInterface
{
    public function run(ModelsCommand $command, Model $model): void
    {
        if (! $model instanceof Tenant) {
            return;
        }

        $command->setProperty('tenancy_db_name', 'string', true, true, '', true);
    }
}
