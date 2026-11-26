<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\System\RemoveInactiveAccounts;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Description('Remove any inactive accounts.')]
#[Signature('perscom:inactive-accounts')]
class RemoveInactiveAccountsCommand extends Command
{
    public function handle(): int
    {
        dispatch(new RemoveInactiveAccounts);

        $this->components->info('The remove inactive accounts job has been dispatched to the queue.');

        return static::SUCCESS;
    }
}
