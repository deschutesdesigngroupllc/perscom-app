<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TrainingRecordCredential extends Pivot
{
    protected $table = 'records_training_credentials';
}
