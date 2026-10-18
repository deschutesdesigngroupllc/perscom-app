<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Livewire\Filament\App;

use App\Filament\App\Resources\EventResource\Pages\ViewEvent;
use App\Livewire\Filament\App\EventDiscussion;
use App\Models\Comment;
use App\Models\Event;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class EventDiscussionTest extends TenantTestCase
{
    public function test_it_lists_comments_oldest_first_with_their_authors(): void
    {
        $event = Event::factory()->createQuietly();
        $first = User::factory()->createQuietly(['name' => 'First Poster']);
        $second = User::factory()->createQuietly(['name' => 'Second Poster']);
        Comment::factory()->createQuietly(['model_id' => $event->getKey(), 'model_type' => $event->getMorphClass(), 'comment' => '<p>Bring water.</p>', 'author_id' => $first->getKey(), 'created_at' => now()->subHour()]);
        Comment::factory()->createQuietly(['model_id' => $event->getKey(), 'model_type' => $event->getMorphClass(), 'comment' => '<p>Will do.</p>', 'author_id' => $second->getKey(), 'created_at' => now()]);
        $this->actingAs($this->member());

        Livewire::test(EventDiscussion::class, ['event' => $event])
            ->assertSeeInOrder(['First Poster', 'Bring water.', 'Second Poster', 'Will do.'])
            ->assertSee('2 comments');
    }

    public function test_a_member_can_post_a_comment(): void
    {
        $event = Event::factory()->createQuietly();
        $member = $this->member();
        $this->actingAs($member);

        Livewire::test(EventDiscussion::class, ['event' => $event])
            ->fillForm(['comment' => '<p>Count me in.</p>'])
            ->call('post')
            ->assertHasNoFormErrors()
            ->assertNotified('Your comment was posted.')
            ->assertSee('Count me in.');

        $this->assertDatabaseHas('comments', [
            'model_id' => $event->getKey(),
            'model_type' => $event->getMorphClass(),
            'author_id' => $member->getKey(),
        ]);
    }

    public function test_an_empty_comment_is_rejected(): void
    {
        $this->actingAs($this->member());

        Livewire::test(EventDiscussion::class, ['event' => Event::factory()->createQuietly()])
            ->fillForm(['comment' => null])
            ->call('post')
            ->assertHasFormErrors(['comment']);
    }

    public function test_members_can_edit_and_delete_only_their_own_comments(): void
    {
        $event = Event::factory()->createQuietly();
        $member = $this->member();
        $mine = Comment::factory()->createQuietly(['model_id' => $event->getKey(), 'model_type' => $event->getMorphClass(), 'comment' => '<p>Mine</p>', 'author_id' => $member->getKey()]);
        $theirs = Comment::factory()->createQuietly(['model_id' => $event->getKey(), 'model_type' => $event->getMorphClass(), 'comment' => '<p>Theirs</p>', 'author_id' => User::factory()->createQuietly()->getKey()]);
        $this->actingAs($member);

        Livewire::test(EventDiscussion::class, ['event' => $event])
            ->assertActionVisible(TestAction::make('editComment')->arguments(['comment' => $mine->getKey()]))
            ->assertActionHidden(TestAction::make('editComment')->arguments(['comment' => $theirs->getKey()]))
            ->assertActionHidden(TestAction::make('deleteComment')->arguments(['comment' => $theirs->getKey()]))
            ->callAction(TestAction::make('editComment')->arguments(['comment' => $mine->getKey()]), ['comment' => '<p>Updated</p>'])
            ->callAction(TestAction::make('deleteComment')->arguments(['comment' => $mine->getKey()]));

        $this->assertModelMissing($mine);
        $this->assertModelExists($theirs);
    }

    public function test_moderators_can_delete_anyones_comment(): void
    {
        $event = Event::factory()->createQuietly();
        $comment = Comment::factory()->createQuietly(['model_id' => $event->getKey(), 'model_type' => $event->getMorphClass(), 'author_id' => User::factory()->createQuietly()->getKey()]);
        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);

        Livewire::test(EventDiscussion::class, ['event' => $event])
            ->callAction(TestAction::make('deleteComment')->arguments(['comment' => $comment->getKey()]));

        $this->assertModelMissing($comment);
    }

    public function test_moderators_need_delete_permission_to_delete_anyones_comment(): void
    {
        $event = Event::factory()->createQuietly();
        $comment = Comment::factory()->createQuietly(['model_id' => $event->getKey(), 'model_type' => $event->getMorphClass(), 'author_id' => User::factory()->createQuietly()->getKey()]);

        $editor = $this->member();
        $editor->givePermissionTo('update_comment');
        $this->actingAs($editor);

        Livewire::test(EventDiscussion::class, ['event' => $event])
            ->assertActionVisible(TestAction::make('editComment')->arguments(['comment' => $comment->getKey()]))
            ->assertActionHidden(TestAction::make('deleteComment')->arguments(['comment' => $comment->getKey()]));

        $this->assertModelExists($comment);

        $deleter = $this->member();
        $deleter->givePermissionTo('delete_comment');
        $this->actingAs($deleter);

        Livewire::test(EventDiscussion::class, ['event' => $event])
            ->assertActionHidden(TestAction::make('editComment')->arguments(['comment' => $comment->getKey()]))
            ->callAction(TestAction::make('deleteComment')->arguments(['comment' => $comment->getKey()]));

        $this->assertModelMissing($comment);
    }

    public function test_the_composer_is_hidden_without_comment_permission(): void
    {
        $user = User::factory()->createQuietly();
        $user->givePermissionTo(['view_any_event', 'view_event']);
        $this->actingAs($user);

        Livewire::test(EventDiscussion::class, ['event' => Event::factory()->createQuietly()])
            ->assertDontSee('Post Comment');
    }

    public function test_the_event_page_shows_the_discussion_instead_of_a_comments_table(): void
    {
        $admin = User::factory()->createQuietly();
        $admin->assignRole(Utils::getSuperAdminName());
        $this->actingAs($admin);

        Livewire::test(ViewEvent::class, ['record' => Event::factory()->createQuietly()->getKey()])
            ->assertSee('Discussion')
            ->assertDontSee('The comments associated with this resource.');
    }

    private function member(): User
    {
        $user = User::factory()->createQuietly();
        $user->givePermissionTo(['view_any_event', 'view_event', 'create_comment']);

        return $user;
    }
}
