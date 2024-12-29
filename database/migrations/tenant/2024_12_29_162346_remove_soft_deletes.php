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
        Announcement::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Announcement $resource) => $resource->deleteQuietly());
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Attachment::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Attachment $resource) => $resource->deleteQuietly());
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Award::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Award $resource) => $resource->deleteQuietly());
        Schema::table('awards', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Calendar::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Calendar $resource) => $resource->deleteQuietly());
        Schema::table('calendars', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Category::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Category $resource) => $resource->deleteQuietly());
        Schema::table('categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Comment::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Comment $resource) => $resource->deleteQuietly());
        Schema::table('comments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Document::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Document $resource) => $resource->deleteQuietly());
        Schema::table('documents', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Event::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Event $resource) => $resource->deleteQuietly());
        Schema::table('events', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Field::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Field $resource) => $resource->deleteQuietly());
        Schema::table('fields', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Form::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Form $resource) => $resource->deleteQuietly());
        Schema::table('forms', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Group::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Group $resource) => $resource->deleteQuietly());
        Schema::table('groups', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Image::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Image $resource) => $resource->deleteQuietly());
        Schema::table('images', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('mail', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Message::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Message $resource) => $resource->deleteQuietly());
        Schema::table('messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Position::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Position $resource) => $resource->deleteQuietly());
        Schema::table('positions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Qualification::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Qualification $resource) => $resource->deleteQuietly());
        Schema::table('qualifications', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Rank::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Rank $resource) => $resource->deleteQuietly());
        Schema::table('ranks', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Submission::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Submission $resource) => $resource->deleteQuietly());
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        AssignmentRecord::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (AssignmentRecord $resource) => $resource->deleteQuietly());
        Schema::table('records_assignments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        AwardRecord::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (AwardRecord $resource) => $resource->deleteQuietly());
        Schema::table('records_awards', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        CombatRecord::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (CombatRecord $resource) => $resource->deleteQuietly());
        Schema::table('records_combat', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        QualificationRecord::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (QualificationRecord $resource) => $resource->deleteQuietly());
        Schema::table('records_qualifications', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        RankRecord::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (RankRecord $resource) => $resource->deleteQuietly());
        Schema::table('records_ranks', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        ServiceRecord::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (ServiceRecord $resource) => $resource->deleteQuietly());
        Schema::table('records_service', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Specialty::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Specialty $resource) => $resource->deleteQuietly());
        Schema::table('specialties', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Status::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Status $resource) => $resource->deleteQuietly());
        Schema::table('statuses', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Tag::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Tag $resource) => $resource->deleteQuietly());
        Schema::table('tags', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Task::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Task $resource) => $resource->deleteQuietly());
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Unit::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Unit $resource) => $resource->deleteQuietly());
        Schema::table('units', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        User::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (User $resource) => $resource->deleteQuietly());
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Webhook::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Webhook $resource) => $resource->deleteQuietly());
        Schema::table('webhooks', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('attachments', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('awards', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('calendars', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('fields', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('images', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('mail', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('qualifications', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('ranks', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('records_assignments', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('records_awards', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('records_combat', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('records_qualifications', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('records_ranks', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('records_service', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('specialties', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('statuses', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('units', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('webhooks', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
};
