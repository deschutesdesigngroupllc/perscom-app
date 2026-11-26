<?php

declare(strict_types=1);

use App\Models\Announcement;
use App\Models\AssignmentRecord;
use App\Models\Attachment;
use App\Models\Award;
use App\Models\AwardRecord;
use App\Models\Calendar;
use App\Models\Category;
use App\Models\CombatRecord;
use App\Models\Comment;
use App\Models\Document;
use App\Models\Event;
use App\Models\Field;
use App\Models\Form;
use App\Models\Group;
use App\Models\Image;
use App\Models\Message;
use App\Models\Position;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use App\Models\Rank;
use App\Models\RankRecord;
use App\Models\ServiceRecord;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Submission;
use App\Models\Tag;
use App\Models\Task;
use App\Models\Unit;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Announcement::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Announcement $resource): ?bool => $resource->deleteQuietly());
        Schema::table('announcements', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Attachment::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Attachment $resource): ?bool => $resource->deleteQuietly());
        Schema::table('attachments', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Award::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Award $resource): ?bool => $resource->deleteQuietly());
        Schema::table('awards', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Calendar::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Calendar $resource): ?bool => $resource->deleteQuietly());
        Schema::table('calendars', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Category::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Category $resource): ?bool => $resource->deleteQuietly());
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Comment::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Comment $resource): ?bool => $resource->deleteQuietly());
        Schema::table('comments', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Document::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Document $resource): ?bool => $resource->deleteQuietly());
        Schema::table('documents', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Event::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Event $resource): ?bool => $resource->deleteQuietly());
        Schema::table('events', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Field::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Field $resource): ?bool => $resource->deleteQuietly());
        Schema::table('fields', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Form::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Form $resource): ?bool => $resource->deleteQuietly());
        Schema::table('forms', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Group::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Group $resource): ?bool => $resource->deleteQuietly());
        Schema::table('groups', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Image::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Image $resource): ?bool => $resource->deleteQuietly());
        Schema::table('images', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('mail', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Message::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Message $resource): ?bool => $resource->deleteQuietly());
        Schema::table('messages', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Position::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Position $resource): ?bool => $resource->deleteQuietly());
        Schema::table('positions', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Qualification::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Qualification $resource): ?bool => $resource->deleteQuietly());
        Schema::table('qualifications', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Rank::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Rank $resource): ?bool => $resource->deleteQuietly());
        Schema::table('ranks', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Submission::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Submission $resource): ?bool => $resource->deleteQuietly());
        Schema::table('submissions', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        AssignmentRecord::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (AssignmentRecord $resource): ?bool => $resource->deleteQuietly());
        Schema::table('records_assignments', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        AwardRecord::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (AwardRecord $resource): ?bool => $resource->deleteQuietly());
        Schema::table('records_awards', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        CombatRecord::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (CombatRecord $resource): ?bool => $resource->deleteQuietly());
        Schema::table('records_combat', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        QualificationRecord::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (QualificationRecord $resource): ?bool => $resource->deleteQuietly());
        Schema::table('records_qualifications', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        RankRecord::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (RankRecord $resource): ?bool => $resource->deleteQuietly());
        Schema::table('records_ranks', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        ServiceRecord::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (ServiceRecord $resource): ?bool => $resource->deleteQuietly());
        Schema::table('records_service', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Specialty::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Specialty $resource): ?bool => $resource->deleteQuietly());
        Schema::table('specialties', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Status::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Status $resource): ?bool => $resource->deleteQuietly());
        Schema::table('statuses', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Tag::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Tag $resource): ?bool => $resource->deleteQuietly());
        Schema::table('tags', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Task::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Task $resource): ?bool => $resource->deleteQuietly());
        Schema::table('tasks', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Unit::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Unit $resource): ?bool => $resource->deleteQuietly());
        Schema::table('units', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        User::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (User $resource): ?bool => $resource->deleteQuietly());
        Schema::table('users', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Webhook::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Webhook $resource): ?bool => $resource->deleteQuietly());
        Schema::table('webhooks', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('attachments', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('awards', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('calendars', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('comments', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('documents', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('events', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('fields', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('forms', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('groups', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('images', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('mail', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('messages', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('positions', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('qualifications', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('ranks', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('submissions', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('records_assignments', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('records_awards', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('records_combat', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('records_qualifications', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('records_ranks', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('records_service', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('specialties', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('statuses', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('tags', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('tasks', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('units', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('webhooks', function (Blueprint $table): void {
            $table->softDeletes();
        });
    }
};
