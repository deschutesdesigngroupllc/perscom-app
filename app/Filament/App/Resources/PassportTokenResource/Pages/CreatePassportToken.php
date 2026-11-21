<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PassportTokenResource\Pages;

use App\Actions\Passport\CreatePersonalAccessToken;
use App\Filament\App\Resources\PassportTokenResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreatePassportToken extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = PassportTokenResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $scopes = data_get($data, 'all_scopes')
            ? ['*']
            : data_get($data, 'scopes', []);

        $result = CreatePersonalAccessToken::handle(Auth::user(), data_get($data, 'name'), $scopes);

        return $result->getToken();
    }
}
