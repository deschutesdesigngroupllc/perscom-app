<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PassportClientResource\Pages;

use App\Filament\App\Resources\PassportClientResource;
use App\Models\Enums\PassportClientType;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;

class CreatePassportClient extends CreateRecord
{
    protected static string $resource = PassportClientResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        /** @var ClientRepository $clients */
        $clients = app(ClientRepository::class);

        $client = match (data_get($data, 'type', PassportClientType::AUTHORIZATION_CODE->value)) {
            PassportClientType::AUTHORIZATION_CODE->value, PassportClientType::IMPLICIT->value => $clients->create(Auth::user()->getAuthIdentifier(), data_get($data, 'name'), data_get($data, 'redirect'), confidential: false),
            PassportClientType::CLIENT_CREDENTIALS->value => $clients->create(null, data_get($data, 'name'), ''),
            PassportClientType::PASSWORD->value => $clients->createPasswordGrantClient(null, data_get($data, 'name'), 'http://localhost', 'users'),
        };

        $client->update([
            'description' => data_get($data, 'description'),
            'scopes' => data_get($data, 'scopes', []),
            'secret' => Str::random(40),
        ]);

        return $client;
    }
}
