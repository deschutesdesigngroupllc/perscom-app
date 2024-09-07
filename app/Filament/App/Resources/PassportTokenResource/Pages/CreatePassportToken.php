<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PassportTokenResource\Pages;

use App\Actions\Passport\CreatePersonalAccessToken;
use App\Filament\App\Resources\PassportTokenResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreatePassportToken extends CreateRecord
{
    protected static string $resource = PassportTokenResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $action = new CreatePersonalAccessToken();

        $scopes = data_get($data, 'all_scopes')
            ? ['*']
            : data_get($data, 'scopes', []);

        $result = $action->handle(Auth::user(), data_get($data, 'name'), $scopes);

        return $result->token;
    }
}
