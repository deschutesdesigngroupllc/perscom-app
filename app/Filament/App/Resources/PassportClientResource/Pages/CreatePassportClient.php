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
use League\Csv\Exception;

class CreatePassportClient extends CreateRecord
{
    protected static string $resource = PassportClientResource::class;

    /**
     * @throws Exception
     */
    protected function handleRecordCreation(array $data): Model
    {
        /** @var ClientRepository $clients */
        $clients = app(ClientRepository::class);

        $client = match (data_get($data, 'type')) {
            PassportClientType::AUTHORIZATION_CODE, PassportClientType::IMPLICIT => $clients->create(Auth::user()->getAuthIdentifier(), data_get($data, 'name'), data_get($data, 'redirect'), confidential: false),
            PassportClientType::CLIENT_CREDENTIALS => $clients->create(null, data_get($data, 'name'), ''),
            PassportClientType::PASSWORD => $clients->createPasswordGrantClient(null, data_get($data, 'name'), 'http://localhost', 'users'),
            default => throw new Exception('The client type selected is not supported.')
        };

        $scopes = data_get($data, 'all_scopes')
            ? ['*']
            : data_get($data, 'scopes', []);

        $client->update([
            'type' => data_get($data, 'type'),
            'description' => data_get($data, 'description'),
            'scopes' => $scopes,
            'secret' => Str::random(40),
        ]);

        return $client;
    }
}
