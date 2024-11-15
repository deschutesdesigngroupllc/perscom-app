<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Settings\OrganizationSettings;
use App\Traits\HasAuthor;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ModelTextParserService
{
    public static function parse(string $content, ?User $user = null, mixed $attachedModel = null): ?string
    {
        $result = with(new ModelTextParserService, function (ModelTextParserService $service) use ($content, $user, $attachedModel) {
            return Str::replaceMatches('/\{(.*?)}/', function (array $matches) use ($service, $user, $attachedModel) {
                return $service->resolveTag($matches[0], $user, $attachedModel) ?? $matches[0];
            }, $content);
        });

        if (is_string($result)) {
            return $result;
        }

        return null;
    }

    protected function resolveTag(string $tag, ?User $user = null, mixed $attachedModel = null): mixed
    {
        if (blank($user)) {
            if (filled($attachedModel) && in_array(HasUser::class, class_uses_recursive(get_class($attachedModel)))) {
                $user = $attachedModel->user;
            }
        }

        $user ??= Auth::user();

        return value(match (true) {
            $tag === '{user_name}' => data_get($user, 'name'),
            $tag === '{user_email}' => $user->email ?? null,
            $tag === '{user_email_verified_at}' => optional($user)->email_verified_at ? Carbon::parse($user?->email_verified_at)->toDayDateTimeString() : null,
            $tag === '{user_status}' => $user->status->name ?? null,
            $tag === '{user_online}' => optional($user)->online ? 'True' : 'False',
            $tag === '{user_assignment_position}' => $user->position->name ?? null,
            $tag === '{user_assignment_specialty}' => $user->specialty->name ?? null,
            $tag === '{user_assignment_unit}' => $user->unit->name ?? null,
            $tag === '{user_rank}' => $user->rank->name ?? null,
            $tag === '{assignment_record_status}' => $attachedModel->status->name ?? null,
            $tag === '{assignment_record_unit}' => $attachedModel->unit->name ?? null,
            $tag === '{assignment_record_position}' => $attachedModel->position->name ?? null,
            $tag === '{assignment_record_speciality}' => $attachedModel->specialty->name ?? null,
            $tag === '{assignment_record_text}', $tag === '{award_record_text}', $tag === '{combat_record_text}', $tag === '{qualification_record_text}', $tag === '{service_record_text}', $tag === '{rank_record_text}' => $attachedModel->text ?? null,
            $tag === '{assignment_record_date}', $tag === '{award_record_date}', $tag === '{combat_record_date}', $tag === '{qualification_record_date}', $tag === '{service_record_date}', $tag === '{rank_record_date}' => function ($user, $attachedModel) {
                $createdAt = $attachedModel->created_at ?? null;

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
            $tag === '{award_record_award}' => $attachedModel->award->name ?? null,
            $tag === '{award_record_award_image}' => function ($user, $attachedModel) {
                $imageUrl = $attachedModel->award->image->image_url ?? null;
                $imageName = $attachedModel->award->name ?? 'Image';

                if (blank($imageUrl)) {
                    return null;
                }

                return new HtmlString("<img src='$imageUrl' alt='$imageName' style='height: 40px;' />");
            },
            $tag === '{qualification_record_qualification}' => $attachedModel->qualification->name ?? null,
            $tag === '{qualification_record_qualification_image}' => function ($user, $attachedModel) {
                $imageUrl = $attachedModel->qualification->image->image_url ?? null;
                $imageName = $attachedModel->qualification->name ?? 'Image';

                if (blank($imageUrl)) {
                    return null;
                }

                return new HtmlString("<img src='$imageUrl' alt='$imageName' style='height: 40px;' />");
            },
            $tag === '{rank_record_rank}' => $attachedModel->rank->name ?? null,
            $tag === '{rank_record_rank_image}' => function ($user, $attachedModel) {
                $imageUrl = $attachedModel->rank->image->image_url ?? null;
                $imageName = $attachedModel->rank->name ?? 'Image';

                if (blank($imageUrl)) {
                    return null;
                }

                return new HtmlString("<img src='$imageUrl' alt='$imageName' style='height: 40px;' />");
            },
            $tag === '{rank_record_type}' => optional($attachedModel->type ?? null)->getLabel() ?? null,
            $tag === '{author_resource_name}' => filled($attachedModel) && in_array(HasAuthor::class, class_uses_recursive(get_class($attachedModel))) ? optional($attachedModel->author)->name : null,
            default => null
        }, $user, $attachedModel);
    }
}
