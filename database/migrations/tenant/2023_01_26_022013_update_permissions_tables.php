<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $query = DB::table('role_has_permissions')
                ->leftJoin('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                ->where('role_id', '=', '2')
                ->where(function ($query) {
                    $query->where('name', '=', 'view:form')
                        ->orWhere('name', '=', 'view:submission')
                        ->orWhere('name', '=', 'view:awardrecord')
                        ->orWhere('name', '=', 'view:assignmentrecord')
                        ->orWhere('name', '=', 'view:combatrecord')
                        ->orWhere('name', '=', 'view:qualificationrecord')
                        ->orWhere('name', '=', 'view:rankrecord')
                        ->orWhere('name', '=', 'view:servicerecord')
                        ->orWhere('name', '=', 'view:statusrecord');
                })
                ->delete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
