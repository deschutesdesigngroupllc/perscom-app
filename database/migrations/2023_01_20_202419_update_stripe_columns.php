<?php

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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->renameColumn('stripe_plan', 'stripe_price');
        });

        Schema::table('subscription_items', function (Blueprint $table) {
            $table->renameColumn('stripe_plan', 'stripe_price');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->renameColumn('card_brand', 'pm_type');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->renameColumn('card_last_four', 'pm_last_four');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->renameColumn('card_expiration', 'pm_expiration');
        });

        Schema::table('subscription_items', function (Blueprint $table) {
            $table->string('stripe_product')->nullable()->after('stripe_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->renameColumn('stripe_price', 'stripe_plan');
        });

        Schema::table('subscription_items', function (Blueprint $table) {
            $table->renameColumn('stripe_price', 'stripe_plan');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->renameColumn('pm_type', 'card_brand');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->renameColumn('pm_last_four', 'card_last_four');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->renameColumn('pm_expiration', 'card_expiration');
        });

        Schema::table('subscription_items', function (Blueprint $table) {
            $table->dropColumn('stripe_product');
        });
    }
};
