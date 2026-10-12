<?php

declare(strict_types=1);

namespace App\Livewire\Filament\App;

use App\Models\Comment;
use App\Models\Event;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\RichEditor;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * A conversation thread for an event: comments listed oldest first with an inline composer.
 *
 * @property-read Schema $form
 */
class EventDiscussion extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Event $event;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(Event $event): void
    {
        $this->event = $event;
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                RichEditor::make('comment')
                    ->hiddenLabel()
                    ->placeholder('Add to the discussion...')
                    ->toolbarButtons([['bold', 'italic', 'link'], ['bulletList', 'orderedList']])
                    ->required()
                    ->maxLength(65535),
            ])
            ->statePath('data');
    }

    public function post(): void
    {
        abort_unless($this->canComment(), 403);

        $data = $this->form->getState();

        $this->event->comments()->create(['comment' => $data['comment']]);

        $this->form->fill();

        Notification::make()->success()->title('Your comment was posted.')->send();
    }

    public function editCommentAction(): Action
    {
        return Action::make('editComment')
            ->label('Edit')
            ->icon(Heroicon::OutlinedPencilSquare)
            ->modalHeading('Edit Comment')
            ->modalSubmitActionLabel('Save')
            ->visible(fn (array $arguments): bool => $this->canManage($this->findComment($arguments)))
            ->fillForm(fn (array $arguments): array => ['comment' => $this->findComment($arguments)?->comment])
            ->schema([
                RichEditor::make('comment')
                    ->hiddenLabel()
                    ->toolbarButtons([['bold', 'italic', 'link'], ['bulletList', 'orderedList']])
                    ->required()
                    ->maxLength(65535),
            ])
            ->action(function (array $arguments, array $data): void {
                $comment = $this->findComment($arguments);

                abort_unless($comment instanceof Comment && $this->canManage($comment), 403);

                $comment->update(['comment' => $data['comment']]);
            })
            ->successNotificationTitle('Your comment was updated.');
    }

    public function deleteCommentAction(): Action
    {
        return Action::make('deleteComment')
            ->label('Delete')
            ->icon(Heroicon::OutlinedTrash)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Delete Comment')
            ->visible(fn (array $arguments): bool => $this->canManage($this->findComment($arguments)))
            ->action(function (array $arguments): void {
                $comment = $this->findComment($arguments);

                abort_unless($comment instanceof Comment && $this->canManage($comment), 403);

                $comment->delete();
            })
            ->successNotificationTitle('The comment was deleted.');
    }

    /**
     * @return Collection<int, Comment>
     */
    public function comments(): Collection
    {
        $comments = Comment::query()
            ->whereMorphedTo('commentable', $this->event)
            ->oldest()
            ->get();

        $authors = User::query()->whereKey($comments->pluck('author_id')->filter()->unique()->all())->get()->keyBy('id');

        return $comments->each(fn (Comment $comment): Comment => $comment->setRelation('author', $authors->get($comment->author_id)));
    }

    public function canComment(): bool
    {
        return (bool) Auth::user()?->can('create', Comment::class);
    }

    /**
     * Members can edit and delete their own comments; anyone with comment permissions can moderate.
     */
    public function canManage(?Comment $comment): bool
    {
        $user = Auth::user();

        if (! $comment instanceof Comment || ! $user instanceof User) {
            return false;
        }

        return $comment->author_id === $user->getKey() || $user->can('update', $comment);
    }

    public function render(): View
    {
        return view('livewire.filament.app.event-discussion');
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    protected function findComment(array $arguments): ?Comment
    {
        return Comment::query()
            ->whereMorphedTo('commentable', $this->event)
            ->whereKey($arguments['comment'] ?? null)
            ->first();
    }
}
