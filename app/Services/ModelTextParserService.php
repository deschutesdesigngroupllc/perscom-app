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
    public function __construct(
        protected string $content,
        protected ?User $user = null,
        protected mixed $attachedModel = null
    ) {}

    public static function parse(string $content, ?User $user = null, mixed $attachedModel = null): ?string
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
            $tag === '{user_email}' => $this->user->email ?? null,
            $tag === '{user_email_verified_at}' => optional($this->user)->email_verified_at ? Carbon::parse($this->user?->email_verified_at)->toDayDateTimeString() : null,
            $tag === '{user_status}' => $this->user->status->name ?? null,
            $tag === '{user_online}' => optional($this->user)->online ? 'True' : 'False',
            $tag === '{user_assignment_position}' => $this->user->position->name ?? null,
            $tag === '{user_assignment_specialty}' => $this->user->specialty->name ?? null,
            $tag === '{user_assignment_unit}' => $this->user->unit->name ?? null,
            $tag === '{user_rank}' => $this->user->rank->name ?? null,
            $tag === '{assignment_record_status}' => $this->attachedModel->status->name ?? null,
            $tag === '{assignment_record_unit}' => $this->attachedModel->unit->name ?? null,
            $tag === '{assignment_record_position}' => $this->attachedModel->position->name ?? null,
            $tag === '{assignment_record_speciality}' => $this->attachedModel->specialty->name ?? null,
            $tag === '{assignment_record_text}', $tag === '{award_record_text}', $tag === '{combat_record_text}', $tag === '{qualification_record_text}', $tag === '{service_record_text}', $tag === '{rank_record_text}' => $this->attachedModel->text ?? null,
            $tag === '{assignment_record_date}', $tag === '{award_record_date}', $tag === '{combat_record_date}', $tag === '{qualification_record_date}', $tag === '{service_record_date}', $tag === '{rank_record_date}' => function ($user, $attachedModel): ?string {
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
            $tag === '{award_record_award}' => $this->attachedModel->award->name ?? null,
            $tag === '{award_record_award_image}' => function ($user, $attachedModel): ?HtmlString {
                $imageUrl = $attachedModel->award->image->image_url ?? null;
                $imageName = $attachedModel->award->name ?? 'Image';

                if (blank($imageUrl)) {
                    return null;
                }

                return new HtmlString("<img src='$imageUrl' alt='$imageName' style='height: 40px;' />");
            },
            $tag === '{qualification_record_qualification}' => $this->attachedModel->qualification->name ?? null,
            $tag === '{qualification_record_qualification_image}' => function ($user, $attachedModel): ?HtmlString {
                $imageUrl = $attachedModel->qualification->image->image_url ?? null;
                $imageName = $attachedModel->qualification->name ?? 'Image';

                if (blank($imageUrl)) {
                    return null;
                }

                return new HtmlString("<img src='$imageUrl' alt='$imageName' style='height: 40px;' />");
            },
            $tag === '{rank_record_rank}' => $this->attachedModel->rank->name ?? null,
            $tag === '{rank_record_rank_image}' => function ($user, $attachedModel): ?HtmlString {
                $imageUrl = $attachedModel->rank->image->image_url ?? null;
                $imageName = $attachedModel->rank->name ?? 'Image';

                if (blank($imageUrl)) {
                    return null;
                }

                return new HtmlString("<img src='$imageUrl' alt='$imageName' style='height: 40px;' />");
            },
            $tag === '{rank_record_type}' => optional($this->attachedModel->type ?? null)->getLabel() ?? null,
            $tag === '{author_resource_name}' => filled($this->attachedModel) && in_array(HasAuthor::class, class_uses_recursive($this->attachedModel::class)) ? optional($this->attachedModel->author)->name : null,
            default => null
        }, $this->user, $this->attachedModel);
    }
}
