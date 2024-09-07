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
        Schema::dropIfExists('action_events');
        Schema::dropIfExists('fields_resources');
        Schema::dropIfExists('nova_field_attachments');
        Schema::dropIfExists('nova_notifications');
        Schema::dropIfExists('nova_pending_field_attachments');
        Schema::dropIfExists('features');
        Schema::dropIfExists('settings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
