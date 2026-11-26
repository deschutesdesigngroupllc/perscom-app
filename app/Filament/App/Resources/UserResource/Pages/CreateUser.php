<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Actions\Copilot\ApplyChangeSet;
use App\Actions\Invites\SendInvite;
use App\Data\Copilot\ProposedChangeSetData;
use App\Data\Copilot\ProposedOperationData;
use App\Filament\Actions\AssignUsersBulkAction;
use App\Filament\App\Resources\UserResource;
use App\Filament\Concerns\HasContainedWizard;
use App\Models\AssignmentRecord;
use App\Models\Enums\AssignmentRecordType;
use App\Models\Enums\CopilotOperationType;
use App\Models\Enums\InviteType;
use App\Models\Enums\RankRecordType;
use App\Models\Invite;
use App\Models\Position;
use App\Models\Rank;
use App\Models\RankRecord;
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
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

use function sprintf;

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
                        ->label('Send Them an Invitation')
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
                        Select::make('unit_slot_id')
                            ->label('Slot')
                            ->helperText('They also take the unit it belongs to and any position or specialty set on the slot.')
                            ->options(fn (): array => Unit::ordered()->with('slots')->get()
                                ->mapWithKeys(fn (Unit $unit): array => [$unit->name => $unit->slots->pluck('name', 'pivot.id')->all()])
                                ->filter()
                                ->all())
                            ->searchable()
                            ->visible(fn (): bool => AssignUsersBulkAction::usesManualRoster() && $this->canRecordAssignments()),
                        Select::make('unit_id')
                            ->label('Unit')
                            ->options(fn (): array => Unit::query()->orderBy('order')->pluck('name', 'id')->all())
                            ->searchable()
                            ->hidden(fn (): bool => AssignUsersBulkAction::usesManualRoster() || ! $this->canRecordAssignments()),
                        Select::make('position_id')
                            ->label('Position')
                            ->options(fn (): array => Position::query()->orderBy('order')->pluck('name', 'id')->all())
                            ->searchable()
                            ->hidden(fn (): bool => AssignUsersBulkAction::usesManualRoster() || ! $this->canRecordAssignments()),
                        Select::make('specialty_id')
                            ->label('Specialty')
                            ->options(fn (): array => Specialty::query()->orderBy('order')->pluck('name', 'id')->all())
                            ->searchable()
                            ->hidden(fn (): bool => AssignUsersBulkAction::usesManualRoster() || ! $this->canRecordAssignments()),
                        Select::make('rank_id')
                            ->label('Rank')
                            ->options(fn (): array => Rank::query()->orderBy('order')->pluck('name', 'id')->all())
                            ->searchable()
                            ->visible(fn (): bool => Gate::check('create', RankRecord::class)),
                        Select::make('status_id')
                            ->label('Status')
                            ->options(fn (): array => Status::query()->orderBy('order')->pluck('name', 'id')->all())
                            ->searchable()
                            ->visible(fn (): bool => Gate::check('update', User::class)),
                    ]),
                ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $user = DB::transaction(fn (): User => $this->createMemberWithRecords($data));

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

    /**
     * Create the member together with their starting assignment, rank, and status.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws AuthorizationException when the creator may not record one of the starting records
     * @throws ValidationException when a starting record refers to something that no longer exists
     */
    protected function createMemberWithRecords(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'approved' => true,
        ]);

        $operations = collect([
            filled($data['unit_slot_id'] ?? null) || filled($data['unit_id'] ?? null) ? AssignUsersBulkAction::operationFor(collect([$user]), [
                'type' => AssignmentRecordType::PRIMARY,
                'unit_slot_id' => $data['unit_slot_id'] ?? null,
                'unit_id' => $data['unit_id'] ?? null,
                'position_id' => $data['position_id'] ?? null,
                'specialty_id' => $data['specialty_id'] ?? null,
            ]) : null,
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

        return $user;
    }

    protected function canRecordAssignments(): bool
    {
        return Gate::check('create', AssignmentRecord::class);
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
