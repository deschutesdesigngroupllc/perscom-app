<?php
/*
 * Copyright (c) 8/7/22, 12:11 PM Deschutes Design Group LLC.year. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('color');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        $id1 = DB::table('permissions')->insertGetId([
			'name' => 'view:announcement',
	        'description' => 'Can view an announcement',
	        'guard_name' => 'web',
	        'created_at' => now(),
	        'updated_at' => now()
        ]);

	    $id2 = DB::table('permissions')->insertGetId([
		    'name' => 'create:announcement',
		    'description' => 'Can create an announcement',
		    'guard_name' => 'web',
		    'created_at' => now(),
		    'updated_at' => now()
	    ]);

	    $id3 = DB::table('permissions')->insertGetId([
		    'name' => 'update:announcement',
		    'description' => 'Can update an announcement',
		    'guard_name' => 'web',
		    'created_at' => now(),
		    'updated_at' => now()
	    ]);

	    $id4 = DB::table('permissions')->insertGetId([
		    'name' => 'delete:announcement',
		    'description' => 'Can delete an announcement',
		    'guard_name' => 'web',
		    'created_at' => now(),
		    'updated_at' => now()
	    ]);

	    DB::table('role_has_permissions')->insert([
	    	'permission_id' => $id1,
		    'role_id' => 1
	    ]);
	    DB::table('role_has_permissions')->insert([
		    'permission_id' => $id2,
		    'role_id' => 1
	    ]);
	    DB::table('role_has_permissions')->insert([
		    'permission_id' => $id3,
		    'role_id' => 1
	    ]);
	    DB::table('role_has_permissions')->insert([
		    'permission_id' => $id4,
		    'role_id' => 1
	    ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcements');
    }
};
