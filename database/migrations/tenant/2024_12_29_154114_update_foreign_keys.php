<?php

declare(strict_types=1);

use App\Models\AssignmentRecord;
use App\Models\AwardRecord;
use App\Models\CombatRecord;
use App\Models\Event;
use App\Models\QualificationRecord;
use App\Models\RankRecord;
use App\Models\ServiceRecord;
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
        Schema::disableForeignKeyConstraints();

        Event::query()->whereNull('calendar_id')->forceDelete();
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign('events_calendar_id_foreign');
        });
        Schema::table('events', function (Blueprint $table) {
            $table->foreign('calendar_id')->references('id')->on('calendars')->cascadeOnDelete();
        });

        Schema::table('model_has_notifications', function (Blueprint $table) {
            $table->foreign('group_id')->references('id')->on('groups')->cascadeOnDelete();
            $table->foreign('unit_id')->references('id')->on('units')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        AssignmentRecord::query()->whereNull('user_id')->forceDelete();
        Schema::table('records_assignments', function (Blueprint $table) {
            $table->dropForeign('records_assignments_user_id_foreign');
        });
        Schema::table('records_assignments', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        AwardRecord::query()->whereNull('user_id')->forceDelete();
        Schema::table('records_awards', function (Blueprint $table) {
            $table->dropForeign('records_awards_user_id_foreign');
        });
        Schema::table('records_awards', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        CombatRecord::query()->whereNull('user_id')->forceDelete();
        Schema::table('records_combat', function (Blueprint $table) {
            $table->dropForeign('records_combat_user_id_foreign');
        });
        Schema::table('records_combat', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        QualificationRecord::query()->whereNull('user_id')->forceDelete();
        Schema::table('records_qualifications', function (Blueprint $table) {
            $table->dropForeign('records_qualifications_user_id_foreign');
        });
        Schema::table('records_qualifications', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        RankRecord::query()->whereNull('user_id')->forceDelete();
        Schema::table('records_ranks', function (Blueprint $table) {
            $table->dropForeign('records_ranks_user_id_foreign');
        });
        Schema::table('records_ranks', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        ServiceRecord::query()->whereNull('user_id')->forceDelete();
        Schema::table('records_service', function (Blueprint $table) {
            $table->dropForeign('records_service_user_id_foreign');
        });
        Schema::table('records_service', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('socialite_users', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::table('model_has_notifications', function (Blueprint $table) {
            $table->dropForeign('model_has_notifications_group_id_foreign');
            $table->dropForeign('model_has_notifications_unit_id_foreign');
            $table->dropForeign('model_has_notifications_user_id_foreign');
        });

        Schema::table('socialite_users', function (Blueprint $table) {
            $table->dropForeign('socialite_users_user_id_foreign');
        });
    }
};
