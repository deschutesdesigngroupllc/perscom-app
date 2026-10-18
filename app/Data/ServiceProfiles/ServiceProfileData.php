<?php

declare(strict_types=1);

namespace App\Data\ServiceProfiles;

use App\Models\AwardRecord;
use App\Models\QualificationRecord;
use App\Models\RankRecord;
use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class ServiceProfileData extends Data
{
    /**
     * @param  Collection<int, AwardRecord>  $awards
     * @param  Collection<int, RankRecord>  $promotions
     * @param  Collection<int, QualificationRecord>  $qualifications
     */
    public function __construct(
        public User $user,
        public string $organization,
        public ?string $timeInService,
        public ?string $timeInGrade,
        public Collection $awards,
        public Collection $promotions,
        public Collection $qualifications,
        public bool $showsRank,
        public bool $showsAssignment,
        public string $url,
        public string $imageUrl,
    ) {}
}
