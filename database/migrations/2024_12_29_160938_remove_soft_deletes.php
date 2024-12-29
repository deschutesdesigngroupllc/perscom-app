<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\Domain;
use App\Models\Tenant;
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
        Admin::withTrashed()->whereNotNull('deleted_at')->each(fn (Admin $admin) => $admin->forceDeleteQuietly());
        Schema::table('admins', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Domain::withTrashed()->whereNotNull('deleted_at')->each(fn (Domain $domain) => $domain->forceDeleteQuietly());
        Schema::table('domains', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Tenant::withTrashed()->whereNotNull('deleted_at')->each(fn (Tenant $tenant) => $tenant->forceDeleteQuietly());
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('domains', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
};
