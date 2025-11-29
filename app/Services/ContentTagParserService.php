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

class ContentTagParserService
{
    public function parse(string $content, ?User $user = null, ?Model $attachedModel = null): ?string
    {
        $result = Str::replaceMatches('/\{(.*?)}/', fn (array $matches) => $this->resolveTag($matches[0], $user, $attachedModel) ?? $matches[0], $content);

        if (is_string($result)) {
            return $result;
        }

        return null;
    }

    protected function resolveTag(string $tag, ?User $user = null, ?Model $attachedModel = null): mixed
    {
        if (blank($user) && (filled($attachedModel) && in_array(HasUser::class, class_uses_recursive($attachedModel::class)))) {
            /** @phpstan-ignore property.notFound */
            $user = $attachedModel->user;
        }

        $user ??= Auth::user();

        return value(match (true) {
            $tag === '{user_name}' => data_get($user, 'name'),
            $tag === '{user_email}' => data_get($user, 'email'),
            $tag === '{user_email_verified_at}' => data_get($user, 'email_verified_at') ? Carbon::parse($user?->email_verified_at)->toDayDateTimeString() : null,
            $tag === '{user_status}' => data_get($user, 'status.name'),
            $tag === '{user_online}' => optional($user)->online ? 'True' : 'False',
            $tag === '{user_assignment_position}' => data_get($user, 'position.name'),
            $tag === '{user_assignment_specialty}' => data_get($user, 'specialty.name'),
            $tag === '{user_assignment_unit}' => data_get($user, 'unit.name'),
            $tag === '{user_rank}' => data_get($user, 'rank.name'),
            $tag === '{user_rank_abbreviation}' => data_get($user, 'rank.abbreviation'),
            $tag === '{user_discord_tag}' => filled($user->discord_user_id) ? sprintf('<@%s>', $user->discord_user_id) : null,
            $tag === '{assignment_record_status}' => data_get($attachedModel, 'status.name'),
            $tag === '{assignment_record_unit}' => data_get($attachedModel, 'unit.name'),
            $tag === '{assignment_record_position}' => data_get($attachedModel, 'position.name'),
            $tag === '{assignment_record_speciality}' => data_get($attachedModel, 'specialty.name'),
            $tag === '{assignment_record_type}' => optional($attachedModel->type ?? null)->getLabel() ?? null,
            $tag === '{assignment_record_text}', $tag === '{award_record_text}', $tag === '{combat_record_text}', $tag === '{qualification_record_text}', $tag === '{service_record_text}', $tag === '{rank_record_text}', $tag === '{training_record_text}' => data_get($attachedModel, 'text'),
            $tag === '{assignment_record_date}', $tag === '{award_record_date}', $tag === '{combat_record_date}', $tag === '{qualification_record_date}', $tag === '{service_record_date}', $tag === '{rank_record_date}', $tag === '{training_record_date}' => function (bool $user, $attachedModel): ?string {
                $createdAt = data_get($attachedModel, 'created_at');

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
            $tag === '{award_record_award}' => data_get($attachedModel, 'award.name'),
            $tag === '{award_record_award_image}' => function ($user, $attachedModel): ?HtmlString {
                $imageUrl = data_get($attachedModel, 'award.image.image_url');
                $imageName = data_get($attachedModel, 'award.name') ?? 'Image';

                if (blank($imageUrl)) {
                    return null;
                }

                return new HtmlString(sprintf("<img src='%s' alt='%s' style='height: 40px;' />", $imageUrl, $imageName));
            },
            $tag === '{qualification_record_qualification}' => data_get($attachedModel, 'qualification.name'),
            $tag === '{qualification_record_qualification_image}' => function ($user, $attachedModel): ?HtmlString {
                $imageUrl = data_get($attachedModel, 'qualification.image.image_url');
                $imageName = data_get($attachedModel, 'qualification.name') ?? 'Image';

                if (blank($imageUrl)) {
                    return null;
                }

                return new HtmlString(sprintf("<img src='%s' alt='%s' style='height: 40px;' />", $imageUrl, $imageName));
            },
            $tag === '{rank_record_rank}' => data_get($attachedModel, 'rank.name'),
            $tag === '{rank_record_rank_abbreviation}' => data_get($attachedModel, 'rank.abbreviation'),
            $tag === '{rank_record_rank_image}' => function ($user, $attachedModel): ?HtmlString {
                $imageUrl = data_get($attachedModel, 'rank.image.image_url');
                $imageName = data_get($attachedModel, 'rank.name') ?? 'Image';

                if (blank($imageUrl)) {
                    return null;
                }

                return new HtmlString(sprintf("<img src='%s' alt='%s' style='height: 40px;' />", $imageUrl, $imageName));
            },
            $tag === '{rank_record_type}' => data_get($attachedModel, 'type')->getLabel() ?? null,
            $tag === '{training_record_credentials}' => collect(data_get($attachedModel, 'credentials'))->map(fn (Credential $credential) => $credential->name)->implode(','),
            $tag === '{training_record_instructor_name}' => data_get($attachedModel, 'instructor.name'),
            $tag === '{author_resource_name}' => data_get($attachedModel, 'author.name'),
            default => null
        }, $user, $attachedModel);
    }
}
