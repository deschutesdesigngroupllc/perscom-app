<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAttachments;
use App\Traits\HasAuthor;
use App\Traits\HasComments;
use App\Traits\HasDocument;
use App\Traits\HasEvent;
use App\Traits\HasLogs;
use App\Traits\HasModelNotifications;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrainingRecord extends Model
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasAttachments;
    use HasAuthor;
    use HasComments;
    use HasDocument;
    use HasEvent;
    use HasFactory;
    use HasLogs;
    use HasModelNotifications;
    use HasUser;

    protected $table = 'records_trainings';

    protected $fillable = [
        'instructor_id',
        'text',
    ];

    /**
     * @return BelongsToMany<Competency, $this>
     */
    public function competencies(): BelongsToMany
    {
        return $this->belongsToMany(Competency::class, 'records_trainings_competencies')
            ->using(TrainingRecordCompetency::class);
    }

    /**
     * @return BelongsToMany<Credential, $this>
     */
    public function credentials(): BelongsToMany
    {
        return $this->belongsToMany(Credential::class, 'records_trainings_credentials')
            ->using(TrainingRecordCredential::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
