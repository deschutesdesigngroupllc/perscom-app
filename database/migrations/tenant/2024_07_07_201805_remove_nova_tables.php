<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::drop('action_events');
        Schema::drop('nova_field_attachments');
        Schema::drop('nova_notifications');
        Schema::drop('nova_pending_field_attachments');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
