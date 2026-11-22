<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Actions\Copilot\ApplyChangeSet;
use App\Actions\Invites\SendInvite;
use App\Data\Copilot\ProposedChangeSetData;
use App\Data\Copilot\ProposedOperationData;
use App\Filament\App\Resources\UserResource;
use App\Filament\Concerns\HasContainedWizard;
use App\Models\Enums\AssignmentRecordType;
use App\Models\Enums\CopilotOperationType;
use App\Models\Enums\InviteType;
use App\Models\Enums\RankRecordType;
use App\Models\Invite;
use App\Models\Position;
use App\Models\Rank;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

/**
 * Adding a member is two short steps: who they are, then where they serve. Their starting rank,
 * assignment and status are saved as records through the audited change set, so their service history
 * and time in grade start on day one instead of being written straight onto the profile.
 */
class CreateUser extends CreateRecord
{
    use HasContainedWizard;

    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Add Member';

    /**
     * @return array<int, Step>
     */
    public function getSteps(): array
    {
        return [
            Step::make('Identity')
                ->description('Who they are')
                ->icon(Heroicon::OutlinedIdentification)
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(User::class, 'email'),
                    ]),
                    Toggle::make('send_invite')
                        ->label('Send them an invitation')
                        ->helperText('Emails a link to set their password and sign in. You can send one later from their profile.')
                        ->default(true)
                        ->visible(fn (): bool => Gate::check('create', Invite::class))
                        ->dehydrated(false),
                ]),
            Step::make('Placement')
                ->description('Where they serve')
                ->icon(Heroicon::OutlinedRectangleGroup)
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('unit_id')
                            ->label('Unit')
                            ->options(fn (): array => Unit::query()->orderBy('order')->pluck('name', 'id')->all())
                            ->searchable(),
                        Select::make('position_id')
                            ->label('Position')
                            ->options(fn (): array => Position::query()->orderBy('order')->pluck('name', 'id')->all())
                            ->searchable(),
                        Select::make('specialty_id')
                            ->label('Specialty')
                            ->options(fn (): array => Specialty::query()->orderBy('order')->pluck('name', 'id')->all())
                            ->searchable(),
                        Select::make('rank_id')
                            ->label('Rank')
                            ->options(fn (): array => Rank::query()->orderBy('order')->pluck('name', 'id')->all())
                            ->searchable(),
                        Select::make('status_id')
                            ->label('Status')
                            ->options(fn (): array => Status::query()->orderBy('order')->pluck('name', 'id')->all())
                            ->searchable(),
                    ]),
                ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'approved' => true,
        ]);

        $operations = collect([
            filled($data['unit_id'] ?? null) ? new ProposedOperationData(
                type: CopilotOperationType::ASSIGNMENT_RECORD,
                userIds: [$user->getKey()],
                targetId: (int) $data['unit_id'],
                attributes: array_filter([
                    'type' => AssignmentRecordType::PRIMARY->value,
                    'position_id' => $data['position_id'] ?? null,
                    'specialty_id' => $data['specialty_id'] ?? null,
                ], filled(...)),
                summary: sprintf('Assign %s', $user->name),
            ) : null,
            filled($data['rank_id'] ?? null) ? new ProposedOperationData(
                type: CopilotOperationType::RANK_RECORD,
                userIds: [$user->getKey()],
                targetId: (int) $data['rank_id'],
                attributes: ['type' => RankRecordType::PROMOTION->value],
                summary: sprintf('Set starting rank for %s', $user->name),
            ) : null,
            filled($data['status_id'] ?? null) ? new ProposedOperationData(
                type: CopilotOperationType::STATUS_RECORD,
                userIds: [$user->getKey()],
                targetId: (int) $data['status_id'],
                summary: sprintf('Set starting status for %s', $user->name),
            ) : null,
        ])->filter()->values()->all();

        if (filled($operations)) {
            ApplyChangeSet::handle(new ProposedChangeSetData($operations), Auth::user(), 'add member');
        }

        if (data_get($this->form->getRawState(), 'send_invite') && Gate::check('create', Invite::class)) {
            try {
                SendInvite::handle(type: InviteType::CLAIM, email: $user->email, user: $user, invitedBy: Auth::user());
            } catch (ValidationException $validationException) {
                Notification::make()
                    ->warning()
                    ->title('Invitation Not Sent')
                    ->body(collect($validationException->errors())->flatten()->first())
                    ->send();
            }
        }

        return $user->refresh();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Member Added';
    }

    protected function getRedirectUrl(): string
    {
        return UserResource::getUrl('view', ['record' => $this->getRecord()]);
    }
}
