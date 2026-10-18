<?php

declare(strict_types=1);

namespace App\Data\Organization;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

/**
 * Describes the full structure of an organization. List order is significant:
 * ranks are listed from most senior to most junior, and every other list is
 * listed in the order it should be displayed.
 */
class OrganizationBlueprintData extends Data
{
    /**
     * @param  array<int, PositionData>  $positions
     * @param  array<int, SpecialtyData>  $specialties
     * @param  array<int, RankData>  $ranks
     * @param  array<int, StatusData>  $statuses
     * @param  array<int, GroupData>  $groups
     * @param  array<int, AwardData>  $awards
     * @param  array<int, QualificationData>  $qualifications
     * @param  array<int, CredentialData>  $credentials
     * @param  array<int, FormData>  $forms
     */
    public function __construct(
        public ?string $name = null,
        public ?string $description = null,
        #[DataCollectionOf(PositionData::class)]
        public array $positions = [],
        #[DataCollectionOf(SpecialtyData::class)]
        public array $specialties = [],
        #[DataCollectionOf(RankData::class)]
        public array $ranks = [],
        #[DataCollectionOf(StatusData::class)]
        public array $statuses = [],
        #[DataCollectionOf(GroupData::class)]
        public array $groups = [],
        #[DataCollectionOf(AwardData::class)]
        public array $awards = [],
        #[DataCollectionOf(QualificationData::class)]
        public array $qualifications = [],
        #[DataCollectionOf(CredentialData::class)]
        public array $credentials = [],
        #[DataCollectionOf(FormData::class)]
        public array $forms = [],
    ) {}

    /**
     * The presets that ship with the application, keyed by preset name.
     *
     * @return array<string, string>
     */
    public static function presets(): array
    {
        return collect(File::glob(self::presetPath('*')))
            ->mapWithKeys(fn (string $path): array => [
                $key = pathinfo($path, PATHINFO_FILENAME) => Str::headline($key),
            ])
            ->sort()
            ->all();
    }

    public static function fromPreset(string $preset): self
    {
        if (! array_key_exists($preset, self::presets())) {
            throw new InvalidArgumentException(sprintf('The organization blueprint preset [%s] does not exist.', $preset));
        }

        return self::fromJson(File::get(self::presetPath($preset)));
    }

    public static function fromJson(string $json): self
    {
        return self::from(json_decode($json, true, flags: JSON_THROW_ON_ERROR));
    }

    protected static function presetPath(string $preset): string
    {
        return resource_path(sprintf('blueprints/%s.json', $preset));
    }
}
