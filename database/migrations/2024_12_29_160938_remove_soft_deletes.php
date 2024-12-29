<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\Alert;
use App\Models\Banner;
use App\Models\Domain;
use App\Models\Tenant;
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
        Admin::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Admin $admin) => $admin->deleteQuietly());
        Schema::table('admins', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Alert::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Alert $admin) => $admin->deleteQuietly());
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Banner::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Banner $admin) => $admin->deleteQuietly());
        Schema::table('banners', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('mail', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Domain::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Domain $domain) => $domain->deleteQuietly());
        Schema::table('domains', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Tenant::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->each(fn (Tenant $tenant) => $tenant->deleteQuietly());
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

        Schema::table('alerts', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('mail', function (Blueprint $table) {
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
