@php
  use App\Services\UserSettingsService;
  use Illuminate\Support\Str;

  $comments = $this->comments();
  $timezone = UserSettingsService::timezone();
  $viewer = \Illuminate\Support\Facades\Auth::user();
@endphp

<x-filament::section icon="heroicon-o-chat-bubble-left-right">
  <x-slot name="heading">Discussion</x-slot>
  <x-slot
    name="description">{{ $comments->isEmpty() ? 'No comments yet' : $comments->count() . ' ' . Str::plural('comment', $comments->count()) }}</x-slot>

  <div class="flex flex-col gap-6">
    @if ($comments->isNotEmpty())
      <ol class="flex flex-col gap-5" aria-label="Comments">
        @foreach ($comments as $comment)
          @php
            $author = $comment->author;
            $isMine = $viewer && $comment->author_id === $viewer->getKey();
            $edited = $comment->updated_at && $comment->created_at && $comment->updated_at->gt($comment->created_at->copy()->addMinute());
          @endphp
          <li wire:key="comment-{{ $comment->getKey() }}" class="flex gap-3">
            @if ($author)
              <x-filament::avatar :src="$author->profile_photo_url" alt="" size="size-9" class="shrink-0 ring-1 ring-gray-950/5 dark:ring-white/10"
                loading="lazy" />
            @else
              <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-white/5">
                <x-filament::icon icon="heroicon-m-user" class="size-4 text-gray-400" />
              </div>
            @endif

            <div class="min-w-0 flex-1">
              <div class="flex items-start justify-between gap-2">
                <p class="flex flex-wrap items-baseline gap-x-2 text-sm">
                  <span class="font-semibold text-gray-950 dark:text-white">{{ $author?->display_name ?? 'Former member' }}</span>
                  @if ($isMine)
                    <span class="text-xs font-medium text-primary-600 dark:text-primary-400">You</span>
                  @endif
                  <time datetime="{{ $comment->created_at?->toIso8601String() }}"
                    title="{{ $comment->created_at?->copy()->setTimezone($timezone)->toDayDateTimeString() }}"
                    class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $comment->created_at?->diffForHumans() }}
                  </time>
                  @if ($edited)
                    <span class="text-xs text-gray-400 dark:text-gray-500">(edited)</span>
                  @endif
                </p>

                @if ($this->canManage($comment) || $this->canManage($comment, 'delete'))
                  <x-filament-actions::group :actions="[
                      ($this->editCommentAction)(['comment' => $comment->getKey()]),
                      ($this->deleteCommentAction)(['comment' => $comment->getKey()]),
                  ]" label="Comment options" icon="heroicon-m-ellipsis-horizontal" color="gray"
                    size="sm" />
                @endif
              </div>

              <div class="fi-prose mt-1 max-w-none text-sm text-gray-700 dark:text-gray-200">
                {!! Str::sanitizeHtml($comment->comment) !!}
              </div>
            </div>
          </li>
        @endforeach
      </ol>
    @else
      <p class="text-sm text-gray-500 dark:text-gray-400">Start the conversation — questions, logistics and updates for this event go here.
      </p>
    @endif

    @if ($this->canComment())
      <form wire:submit="post" @class([
          'flex items-start gap-3',
          'border-t border-gray-950/5 pt-5 dark:border-white/10' => $comments->isNotEmpty(),
      ])>
        @if ($viewer)
          <x-filament::avatar :src="$viewer->profile_photo_url" alt="" size="size-9" class="shrink-0 ring-1 ring-gray-950/5 dark:ring-white/10" />
        @endif
        <div class="flex min-w-0 flex-1 flex-col gap-3">
          {{ $this->form }}
          <div class="flex justify-end">
            <x-filament::button type="submit" icon="heroicon-m-paper-airplane" wire:target="post">Post Comment</x-filament::button>
          </div>
        </div>
      </form>
    @endif
  </div>

  <x-filament-actions::modals />
</x-filament::section>
