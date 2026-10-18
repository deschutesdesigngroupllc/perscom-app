<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Credential;
use App\Models\TrainingRecord;
use App\Models\TrainingRecordCredential;
use Illuminate\Support\Facades\Date;

class TrainingRecordCredentialObserver
{
    public function creating(TrainingRecordCredential $pivot): void
    {
        $issuedAt = filled($pivot->getAttribute('issued_at'))
            ? Date::parse($pivot->getAttribute('issued_at'))
            : (TrainingRecord::query()->find($pivot->getAttribute('training_record_id'))->created_at ?? now());

        $pivot->setAttribute('issued_at', $issuedAt);

        if (filled($pivot->getAttribute('expires_at'))) {
            return;
        }

        $validityDays = Credential::query()->whereKey($pivot->getAttribute('credential_id'))->value('validity_days');

        if (filled($validityDays)) {
            $pivot->setAttribute('expires_at', $issuedAt->copy()->addDays((int) $validityDays));
        }
    }
}
