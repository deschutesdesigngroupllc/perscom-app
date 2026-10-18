<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Events\OccurrenceData;
use App\Exceptions\EventRegistrationException;
use App\Filament\App\Pages\Roster;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Settings\IntegrationSettings;
use App\Settings\OrganizationSettings;
use App\Support\Discord\DiscordEmbed;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

/**
 * Answers Discord slash commands. Every command runs inside the organization that owns the server
 * it was used in, and acts as the PERSCOM member linked to the Discord account that used it, so the
 * same permissions apply as in the dashboard.
 */
class DiscordInteractionService
{
    public const int TYPE_PING = 1;

    public const int TYPE_COMMAND = 2;

    public const int TYPE_AUTOCOMPLETE = 4;

    public const int RESPONSE_PONG = 1;

    public const int RESPONSE_MESSAGE = 4;

    public const int RESPONSE_AUTOCOMPLETE = 8;

    public const int FLAG_EPHEMERAL = 64;

    protected const string BRAND_COLOR = '#2563eb';

    protected const int UPCOMING_DAYS = 30;

    /**
     * The slash commands registered with Discord.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function commands(): array
    {
        return [
            [
                'name' => 'profile',
                'description' => 'Show a member’s PERSCOM service profile.',
                'contexts' => [0],
                'options' => [
                    ['type' => 6, 'name' => 'member', 'description' => 'The member to show. Defaults to you.', 'required' => false],
                ],
            ],
            [
                'name' => 'roster',
                'description' => 'Show your organization’s roster, or the members of one unit.',
                'contexts' => [0],
                'options' => [
                    ['type' => 3, 'name' => 'unit', 'description' => 'The unit to show.', 'required' => false, 'autocomplete' => true],
                ],
            ],
            [
                'name' => 'events',
                'description' => 'List upcoming events and your RSVP status.',
                'contexts' => [0],
            ],
            [
                'name' => 'rsvp',
                'description' => 'RSVP to an upcoming event.',
                'contexts' => [0],
                'options' => [
                    ['type' => 3, 'name' => 'event', 'description' => 'The event to RSVP to.', 'required' => true, 'autocomplete' => true],
                    ['type' => 3, 'name' => 'status', 'description' => 'Your RSVP. Defaults to going.', 'required' => false, 'choices' => [
                        ['name' => 'Going', 'value' => EventRegistrationStatus::GOING->value],
                        ['name' => 'Maybe', 'value' => EventRegistrationStatus::TENTATIVE->value],
                        ['name' => 'Not going', 'value' => EventRegistrationStatus::NOT_GOING->value],
                    ]],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public static function handle(array $payload): array
    {
        $type = (int) data_get($payload, 'type');

        if ($type === self::TYPE_PING) {
            return ['type' => self::RESPONSE_PONG];
        }

        $guildId = (string) data_get($payload, 'guild_id');

        if (blank($guildId)) {
            return DiscordInteractionService::reply('PERSCOM commands only work inside your organization’s Discord server.');
        }

        $initialized = DiscordInteractionService::initializeOrganization($guildId);

        if ($initialized === null) {
            return DiscordInteractionService::reply('This Discord server is not connected to a PERSCOM organization. An administrator can connect it from Settings → Integrations.');
        }

        try {
            return match ($type) {
                self::TYPE_COMMAND => DiscordInteractionService::command($payload),
                self::TYPE_AUTOCOMPLETE => DiscordInteractionService::autocomplete($payload),
                default => DiscordInteractionService::reply('That interaction is not supported.'),
            };
        } finally {
            Auth::forgetGuards();

            if ($initialized && tenancy()->initialized) {
                tenancy()->end();
            }
        }
    }

    /**
     * Returns whether tenancy was initialized for the server, false when no initialization was needed,
     * or null when the server is not connected to an organization.
     */
    protected static function initializeOrganization(string $guildId): ?bool
    {
        if (config('tenancy.enabled')) {
            $tenant = Tenant::query()->where('discord_guild_id', $guildId)->first();

            if (! $tenant instanceof Tenant) {
                return null;
            }

            if (tenancy()->initialized && (string) tenant()?->getTenantKey() === (string) $tenant->getTenantKey()) {
                return false;
            }

            tenancy()->initialize($tenant);

            return true;
        }

        /** @var IntegrationSettings $settings */
        $settings = resolve(IntegrationSettings::class);

        return data_get($settings->discord_settings, 'discord_server') === $guildId ? false : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected static function command(array $payload): array
    {
        $invoker = DiscordInteractionService::invoker($payload);

        return match ((string) data_get($payload, 'data.name')) {
            'profile' => DiscordInteractionService::profile($payload, $invoker),
            'roster' => DiscordInteractionService::roster($payload, $invoker),
            'events' => DiscordInteractionService::events($invoker),
            'rsvp' => DiscordInteractionService::rsvp($payload, $invoker),
            default => DiscordInteractionService::reply('That command is not supported.'),
        };
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected static function profile(array $payload, ?User $invoker): array
    {
        $discordId = (string) (DiscordInteractionService::option($payload, 'member') ?? data_get($payload, 'member.user.id'));
        $member = User::query()->where('discord_user_id', $discordId)->first();

        if (! $member instanceof User) {
            return DiscordInteractionService::reply('That Discord account is not linked to a member. Members can link Discord from their PERSCOM profile.');
        }

        $isPublic = ServiceProfileService::isPublic($member);
        $canView = $isPublic || ($invoker instanceof User && ($invoker->is($member) || $invoker->can('view', $member)));

        if (! $canView) {
            return DiscordInteractionService::reply('That member’s service profile is private.');
        }

        $profile = ServiceProfileService::build($member);

        $embed = DiscordEmbed::make()
            ->title($member->name)
            ->url($isPublic ? $profile->url : null)
            ->color(DiscordInteractionService::BRAND_COLOR)
            ->author($profile->showsRank ? $member->rank?->name : null)
            ->field('Position', $profile->showsAssignment ? $member->position?->name : null)
            ->field('Unit', $profile->showsAssignment ? $member->unit?->name : null)
            ->field('Specialty', $member->specialty?->name)
            ->field('Time in Service', $profile->timeInService)
            ->field('Time in Grade', $profile->showsRank ? $profile->timeInGrade : null)
            ->field('Awards', $profile->awards->isNotEmpty() ? $profile->awards->pluck('award.name')->unique()->take(8)->implode(', ') : null, inline: false)
            ->image($isPublic ? $profile->imageUrl : null)
            ->footer($profile->organization);

        return DiscordInteractionService::reply(embeds: [$embed->toArray()], ephemeral: ! $isPublic);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected static function roster(array $payload, ?User $invoker): array
    {
        if (! $invoker instanceof User || ! DiscordInteractionService::canViewRoster($invoker)) {
            return DiscordInteractionService::notAllowed($invoker);
        }

        $unitId = DiscordInteractionService::option($payload, 'unit');
        $organization = (string) SettingsService::get(OrganizationSettings::class, 'name', config('app.name'));

        if (filled($unitId)) {
            $unit = Unit::query()->find($unitId);

            if (! $unit instanceof Unit) {
                return DiscordInteractionService::reply('That unit could not be found.');
            }

            $members = User::query()
                ->where('unit_id', $unit->getKey())
                ->orderForRoster()
                ->limit(25)
                ->get()
                ->load(['rank', 'position']);

            $lines = $members->map(fn (User $user): string => trim(sprintf(
                '**%s %s**%s',
                $user->rank?->abbreviation,
                $user->name,
                filled($user->position?->name) ? ' — '.$user->position->name : '',
            )));

            $embed = DiscordEmbed::make()
                ->title($unit->name)
                ->color(DiscordInteractionService::BRAND_COLOR)
                ->description($lines->isEmpty() ? 'No members are assigned to this unit.' : $lines->implode("\n"))
                ->footer(sprintf('%s · %d %s', $organization, $members->count(), Str::plural('member', $members->count())));

            return DiscordInteractionService::reply(embeds: [$embed->toArray()]);
        }

        $units = Unit::query()
            ->withCount('users')
            ->orderBy('order')
            ->limit(25)
            ->get();

        $embed = DiscordEmbed::make()
            ->title(sprintf('%s Roster', $organization))
            ->color(DiscordInteractionService::BRAND_COLOR)
            ->description($units->isEmpty()
                ? 'No units have been created yet.'
                : $units->map(fn (Unit $unit): string => sprintf('**%s** · %d %s', $unit->name, $unit->users_count, Str::plural('member', (int) $unit->users_count)))->implode("\n"))
            ->footer('Use /roster unit:<name> to see a unit’s members.');

        return DiscordInteractionService::reply(embeds: [$embed->toArray()]);
    }

    /**
     * @return array<string, mixed>
     */
    protected static function events(?User $invoker): array
    {
        if (! $invoker instanceof User || ! $invoker->can('viewAny', Event::class)) {
            return DiscordInteractionService::notAllowed($invoker);
        }

        $occurrences = DiscordInteractionService::upcomingOccurrences()
            ->filter(fn (OccurrenceData $occurrence): bool => $invoker->can('view', $occurrence->event))
            ->take(10)
            ->values();

        if ($occurrences->isEmpty()) {
            return DiscordInteractionService::reply('There are no upcoming events.');
        }

        $responses = EventCalendarService::responses($occurrences, $invoker);

        $embed = DiscordEmbed::make()
            ->title('Upcoming Events')
            ->color(DiscordInteractionService::BRAND_COLOR)
            ->footer('Use /rsvp to respond.');

        $occurrences->each(function (OccurrenceData $occurrence) use ($embed, $responses): void {
            $status = $responses->get($occurrence->key());

            $embed->field(
                $occurrence->event->name,
                collect([
                    sprintf('<t:%d:F>', $occurrence->starts->getTimestamp()),
                    $status instanceof EventRegistrationStatus ? 'Your RSVP: '.$status->getMemberLabel() : (EventRegistrationService::canRespond($occurrence) ? 'Not RSVP’d' : null),
                ])->filter()->implode("\n"),
                inline: false,
            );
        });

        return DiscordInteractionService::reply(embeds: [$embed->toArray()]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected static function rsvp(array $payload, ?User $invoker): array
    {
        if (! $invoker instanceof User || ! $invoker->can('viewAny', Event::class)) {
            return DiscordInteractionService::notAllowed($invoker);
        }

        [$eventId, $starts] = array_pad(explode('|', (string) DiscordInteractionService::option($payload, 'event'), 2), 2, null);

        $event = Event::query()->find($eventId);

        if (! $event instanceof Event || ! $invoker->can('view', $event)) {
            return DiscordInteractionService::reply('That event could not be found.');
        }

        $occurrence = DiscordInteractionService::rsvpOccurrence($event, $starts);

        if (! $occurrence instanceof OccurrenceData) {
            return DiscordInteractionService::reply(sprintf('%s has no upcoming dates to RSVP to.', $event->name));
        }

        $statusOption = DiscordInteractionService::option($payload, 'status');
        $status = filled($statusOption) ? EventRegistrationStatus::tryFrom((string) $statusOption) : EventRegistrationStatus::GOING;

        if (! $status instanceof EventRegistrationStatus) {
            return DiscordInteractionService::reply(EventRegistrationException::invalidResponse()->getMessage());
        }

        try {
            EventRegistrationService::respond($occurrence, $invoker, $status);
        } catch (EventRegistrationException $eventRegistrationException) {
            return DiscordInteractionService::reply($eventRegistrationException->getMessage());
        }

        return DiscordInteractionService::reply(sprintf(
            'You’re marked **%s** for **%s**%s.',
            Str::lower($status->getLabel()),
            $event->name,
            $event->repeats ? sprintf(' on <t:%d:F>', $occurrence->starts->getTimestamp()) : '',
        ));
    }

    /**
     * The occurrence an RSVP applies to: the one chosen from autocomplete, otherwise the event's
     * occurrence happening now or the next one to come.
     */
    protected static function rsvpOccurrence(Event $event, ?string $starts): ?OccurrenceData
    {
        if (filled($starts) && is_numeric($starts)) {
            return EventOccurrenceService::find($event, Date::createFromTimestamp((int) $starts, config('app.timezone')));
        }

        return EventOccurrenceService::around($event, now(), 1, 1)
            ->reject(fn (OccurrenceData $occurrence): bool => $occurrence->hasEnded())
            ->first();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected static function autocomplete(array $payload): array
    {
        $focused = collect((array) data_get($payload, 'data.options'))->firstWhere('focused', true);
        $search = (string) data_get($focused, 'value');

        $choices = match (data_get($focused, 'name')) {
            'unit' => Unit::query()
                ->when(filled($search), fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'))
                ->orderBy('order')
                ->limit(25)
                ->get()
                ->map(fn (Unit $unit): array => ['name' => Str::limit($unit->name, 97), 'value' => (string) $unit->getKey()]),
            'event' => DiscordInteractionService::upcomingOccurrences($search)
                ->take(25)
                ->map(fn (OccurrenceData $occurrence): array => [
                    'name' => Str::limit($occurrence->event->name.' · '.$occurrence->starts->format('M j'), 97),
                    'value' => $occurrence->key(),
                ]),
            default => collect(),
        };

        return [
            'type' => self::RESPONSE_AUTOCOMPLETE,
            'data' => ['choices' => $choices->values()->all()],
        ];
    }

    /**
     * The upcoming, uncancelled occurrences of one-time and repeating events, soonest first.
     *
     * @return Collection<int, OccurrenceData>
     */
    protected static function upcomingOccurrences(?string $search = null): Collection
    {
        $query = Event::query()->when(filled($search), fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'));

        return EventOccurrenceService::between(now(), now()->addDays(DiscordInteractionService::UPCOMING_DAYS), $query)
            ->reject(fn (OccurrenceData $occurrence): bool => $occurrence->isCancelled() || $occurrence->hasEnded())
            ->values();
    }

    /**
     * The PERSCOM member linked to the Discord account that used the command, signed in for the
     * duration of the interaction so policies and page permissions apply.
     *
     * @param  array<string, mixed>  $payload
     */
    protected static function invoker(array $payload): ?User
    {
        $discordId = (string) data_get($payload, 'member.user.id');

        $user = filled($discordId)
            ? User::query()->where('discord_user_id', $discordId)->where('approved', true)->first()
            : null;

        if ($user instanceof User) {
            Auth::guard('web')->setUser($user);
        }

        return $user;
    }

    protected static function canViewRoster(User $user): bool
    {
        Filament::setCurrentPanel('app');

        return Roster::canAccess();
    }

    /**
     * @return array<string, mixed>
     */
    protected static function notAllowed(?User $invoker): array
    {
        return DiscordInteractionService::reply($invoker instanceof User
            ? 'You do not have permission to do that in PERSCOM.'
            : 'Link your Discord account from your PERSCOM profile to use this command.');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected static function option(array $payload, string $name): mixed
    {
        return data_get(collect((array) data_get($payload, 'data.options'))->firstWhere('name', $name), 'value');
    }

    /**
     * Replies are only visible to the person who used the command unless they are meant to be shared,
     * and never ping anyone.
     *
     * @param  array<int, array<string, mixed>>  $embeds
     * @return array<string, mixed>
     */
    protected static function reply(?string $content = null, array $embeds = [], bool $ephemeral = true): array
    {
        return [
            'type' => self::RESPONSE_MESSAGE,
            'data' => array_filter([
                'content' => $content,
                'embeds' => $embeds,
                'flags' => $ephemeral ? self::FLAG_EPHEMERAL : null,
                'allowed_mentions' => ['parse' => []],
            ], fn (mixed $value): bool => $value !== null && $value !== []),
        ];
    }
}
