<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\ServiceProfiles\ServiceProfileData;
use App\Models\AwardRecord;
use App\Models\Enums\RankRecordType;
use App\Models\QualificationRecord;
use App\Models\Rank;
use App\Models\RankRecord;
use App\Models\Tenant;
use App\Models\User;
use App\Settings\DashboardSettings;
use App\Settings\OrganizationSettings;
use Carbon\CarbonInterval;
use Illuminate\Support\Str;

class ServiceProfileService
{
    public static function isEnabled(): bool
    {
        return (bool) SettingsService::get(DashboardSettings::class, 'public_profiles_enabled', false);
    }

    /**
     * A member's service profile is public when the organization has turned public profiles on,
     * the member has not hidden theirs, and their account has been approved.
     */
    public static function isPublic(User $user): bool
    {
        return ServiceProfileService::isEnabled()
            && ! $user->hide_public_profile
            && $user->approved;
    }

    public static function url(User $user): string
    {
        return ServiceProfileService::absoluteUrl(route('app.service-profiles.show', ['handle' => ServiceProfileService::handle($user)], absolute: false));
    }

    /**
     * The readable part of the handle is decoration; members are only ever looked up by the random
     * key so profiles cannot be found by guessing IDs or names.
     */
    public static function handle(User $user): string
    {
        return collect([Str::slug($user->name), ServiceProfileService::key($user)])->filter()->implode('-');
    }

    public static function key(User $user): string
    {
        if (blank($user->public_profile_key)) {
            $user->forceFill(['public_profile_key' => Str::lower(Str::random(10))])->saveQuietly();
        }

        return (string) $user->public_profile_key;
    }

    public static function findByHandle(string $handle): ?User
    {
        $key = Str::afterLast($handle, '-');

        if (blank($key)) {
            return null;
        }

        return User::query()->where('public_profile_key', $key)->first();
    }

    /**
     * The version parameter changes whenever the member changes, so share previews refresh rather
     * than serving a stale cached image.
     */
    public static function imageUrl(User $user): string
    {
        return ServiceProfileService::absoluteUrl(route('app.service-profiles.image', [
            'handle' => ServiceProfileService::handle($user),
            'v' => $user->updated_at?->getTimestamp(),
        ], absolute: false));
    }

    /**
     * @return string[]
     */
    public static function hiddenFields(): array
    {
        return SettingsService::get(DashboardSettings::class, 'user_hidden_fields', []) ?? [];
    }

    public static function build(User $user): ServiceProfileData
    {
        $user->loadMissing(['rank.image', 'position', 'specialty', 'unit', 'status']);

        $hidden = ServiceProfileService::hiddenFields();

        $awards = in_array('award_records', $hidden, true)
            ? collect()
            : AwardRecord::query()
                ->whereBelongsTo($user)
                ->whereHas('award')
                ->with('award.image')
                ->latest()
                ->get()
                ->toBase();

        $promotions = in_array('rank_records', $hidden, true)
            ? collect()
            : RankRecord::query()
                ->whereBelongsTo($user)
                ->whereIn('rank_id', Rank::query()->select('id'))
                ->where('type', RankRecordType::PROMOTION)
                ->latest()
                ->get()
                ->load(['rank.image'])
                ->toBase();

        $qualifications = in_array('qualification_records', $hidden, true)
            ? collect()
            : QualificationRecord::query()
                ->whereBelongsTo($user)
                ->whereHas('qualification')
                ->with('qualification.image')
                ->latest()
                ->get()
                ->unique('qualification_id')
                ->values()
                ->toBase();

        return new ServiceProfileData(
            user: $user,
            organization: (string) SettingsService::get(OrganizationSettings::class, 'name', config('app.name')),
            timeInService: in_array('time_in_service', $hidden, true) ? null : ServiceProfileService::humanize($user->time_in_service),
            timeInGrade: in_array('time_in_grade', $hidden, true) ? null : ServiceProfileService::humanize($user->time_in_grade),
            awards: $awards,
            promotions: $promotions,
            qualifications: $qualifications,
            showsRank: ! in_array('rank_id', $hidden, true) && filled($user->rank),
            showsAssignment: ! in_array('assignment', $hidden, true),
            url: ServiceProfileService::url($user),
            imageUrl: ServiceProfileService::imageUrl($user),
        );
    }

    protected static function humanize(?CarbonInterval $interval): ?string
    {
        return $interval?->forHumans(['parts' => 2]);
    }

    /**
     * Profiles are shared from Discord and Slack, where the request is not on the organization's
     * domain, so links are always built from the organization's own URL.
     */
    protected static function absoluteUrl(string $path): string
    {
        $tenant = tenant();

        if ($tenant instanceof Tenant && filled($tenant->url)) {
            return rtrim((string) $tenant->url, '/').$path;
        }

        return url($path);
    }
}
