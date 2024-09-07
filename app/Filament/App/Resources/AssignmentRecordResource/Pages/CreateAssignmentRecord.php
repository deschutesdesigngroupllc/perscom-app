<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AssignmentRecordResource\Pages;

use App\Filament\App\Resources\AssignmentRecordResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAssignmentRecord extends CreateRecord
{
    protected static string $resource = AssignmentRecordResource::class;

    //    protected function handleRecordCreation(array $data): Model
    //    {
    //        $models = collect();
    //        dd($data);
    //        collect(data_get($data, 'user_id'))->each(function ($userId) use ($data, $models) {
    //            $data['user_id'] = $userId;
    //
    //            $models->add(parent::handleRecordCreation($data));
    //        });
    //
    //        dd($models);
    //
    //        return collect($models)->first();
    //    }
}
