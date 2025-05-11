<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Credential;
use App\Models\User;
use App\Settings\OrganizationSettings;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ModelTextParserService
{
    public function __construct(
        protected string $content,
        protected ?User $user = null,
        protected ?Model $attachedModel = null
    ) {}

    public static function parse(string $content, ?User $user = null, ?Model $attachedModel = null): ?string
    {
        $result = with(new ModelTextParserService($content, $user, $attachedModel), fn (ModelTextParserService $service) => Str::replaceMatches('/\{(.*?)}/', fn (array $matches) => $service->resolveTag($matches[0]) ?? $matches[0], $service->content));

        if (is_string($result)) {
            return $result;
        }

        return null;
    }

    protected function resolveTag(string $tag): mixed
    {
        if (blank($this->user) && (filled($this->attachedModel) && in_array(HasUser::class, class_uses_recursive($this->attachedModel::class)))) {
            $this->user = $this->attachedModel->user;
        }

        $this->user ??= Auth::user();

        return value(match (true) {
            $tag === '{user_name}' => data_get($this->user, 'name'),
            $tag === '{user_email}' => data_get($this->user, 'email'),
            $tag === '{user_email_verified_at}' => optional($this->user)->email_verified_at ? Carbon::parse($this->user?->email_verified_at)->toDayDateTimeString() : null,
            $tag === '{user_status}' => data_get($this->user, 'status.name'),
            $tag === '{user_online}' => optional($this->user)->online ? 'True' : 'False',
            $tag === '{user_assignment_position}' => data_get($this->user, 'position.name'),
            $tag === '{user_assignment_specialty}' => data_get($this->user, 'specialty.name'),
            $tag === '{user_assignment_unit}' => data_get($this->user, 'unit.name'),
            $tag === '{user_rank}' => data_get($this->user, 'rank.name'),
            $tag === '{user_rank_abbreviation}' => data_get($this->user, 'rank.abbreviation'),
            $tag === '{assignment_record_status}' => data_get($this->attachedModel, 'status.name'),
            $tag === '{assignment_record_unit}' => data_get($this->attachedModel, 'unit.name'),
            $tag === '{assignment_record_position}' => data_get($this->attachedModel, 'position.name'),
            $tag === '{assignment_record_speciality}' => data_get($this->attachedModel, 'specialty.name'),
            $tag === '{assignment_record_type}' => optional($this->attachedModel->type ?? null)->getLabel() ?? null,
            $tag === '{assignment_record_text}', $tag === '{award_record_text}', $tag === '{combat_record_text}', $tag === '{qualification_record_text}', $tag === '{service_record_text}', $tag === '{rank_record_text}', $tag === '{training_record_text}' => data_get($this->attachedModel, 'text'),
            $tag === '{assignment_record_date}', $tag === '{award_record_date}', $tag === '{combat_record_date}', $tag === '{qualification_record_date}', $tag === '{service_record_date}', $tag === '{rank_record_date}', $tag === '{training_record_date}' => function ($user, $attachedModel): ?string {
                $createdAt = data_get($this->attachedModel, 'created_at');

                if (blank($createdAt)) {
                    return null;
                }

                $timezone = UserSettingsService::get(
                    key: 'timezone',
                    default: function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    },
                    user: $user
                );

                return Carbon::parse($createdAt)
                    ->setTimezone($timezone)
                    ->toDayDateTimeString();
            },
            $tag === '{award_record_award}' => data_get($this->attachedModel, 'award.name'),
            $tag === '{award_record_award_image}' => function ($user, $attachedModel): ?HtmlString {
                $imageUrl = data_get($attachedModel, 'award.image.image_url');
                $imageName = data_get($attachedModel, 'award.name') ?? 'Image';

                if (blank($imageUrl)) {
                    return null;
                }

                return new HtmlString("<img src='$imageUrl' alt='$imageName' style='height: 40px;' />");
            },
            $tag === '{qualification_record_qualification}' => data_get($this->attachedModel, 'qualification.name'),
            $tag === '{qualification_record_qualification_image}' => function ($user, $attachedModel): ?HtmlString {
                $imageUrl = data_get($attachedModel, 'qualification.image.image_url');
                $imageName = data_get($attachedModel, 'qualification.name') ?? 'Image';

                if (blank($imageUrl)) {
                    return null;
                }

                return new HtmlString("<img src='$imageUrl' alt='$imageName' style='height: 40px;' />");
            },
            $tag === '{rank_record_rank}' => data_get($this->attachedModel, 'rank.name'),
            $tag === '{rank_record_rank_abbreviation}' => data_get($this->attachedModel, 'rank.abbreviation'),
            $tag === '{rank_record_rank_image}' => function ($user, $attachedModel): ?HtmlString {
                $imageUrl = data_get($attachedModel, 'rank.image.image_url');
                $imageName = data_get($attachedModel, 'rank.name') ?? 'Image';

                if (blank($imageUrl)) {
                    return null;
                }

                return new HtmlString("<img src='$imageUrl' alt='$imageName' style='height: 40px;' />");
            },
            $tag === '{rank_record_type}' => data_get($this->attachedModel, 'type')->getLabel() ?? null,
            $tag === '{training_record_credentials}' => collect(data_get($this->attachedModel, 'credentials'))->map(fn (Credential $credential) => $credential->name)->implode(','),
            $tag === '{training_record_instructor_name}' => data_get($this->attachedModel, 'instructor.name'),
            $tag === '{author_resource_name}' => data_get($this->attachedModel, 'author.name'),
            default => null
        }, $this->user, $this->attachedModel);
    }
}
