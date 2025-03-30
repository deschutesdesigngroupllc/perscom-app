<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasCategories;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Competency extends Model implements HasLabel
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasCategories;
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @return BelongsToMany<TrainingRecord, $this>
     */
    public function training_records(): BelongsToMany
    {
        return $this->belongsToMany(TrainingRecord::class, 'records_trainings_competencies')
            ->using(TrainingRecordCompetency::class);
    }
}
