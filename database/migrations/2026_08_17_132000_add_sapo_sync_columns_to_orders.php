<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->bigInteger('SAPO_ORDER_ID')->nullable()->unique()->after('PAYMENT_DATE');
            $table->string('SAPO_ORDER_NAME', 255)->nullable()->after('SAPO_ORDER_ID');
            $table->string('SAPO_SYNC_STATUS', 30)->nullable()->index()->after('SAPO_ORDER_NAME');
            $table->unsignedInteger('SAPO_SYNC_ATTEMPTS')->default(0)->after('SAPO_SYNC_STATUS');
            $table->text('SAPO_SYNC_ERROR')->nullable()->after('SAPO_SYNC_ATTEMPTS');
            $table->json('SAPO_PAYLOAD')->nullable()->after('SAPO_SYNC_ERROR');
            $table->dateTime('SAPO_SYNCED_AT', 6)->nullable()->after('SAPO_PAYLOAD');
        });

        Schema::table('order', function (Blueprint $table) {
            $table->bigInteger('PRODUCT_VARIANT_ID')->nullable()->index()->after('PRODUCT_ID');
            $table->bigInteger('SAPO_VARIANT_ID')->nullable()->index()->after('PRODUCT_VARIANT_ID');
        });
    }

    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropColumn(['PRODUCT_VARIANT_ID', 'SAPO_VARIANT_ID']);
        });

        Schema::table('transaction', function (Blueprint $table) {
            $table->dropColumn([
                'SAPO_ORDER_ID',
                'SAPO_ORDER_NAME',
                'SAPO_SYNC_STATUS',
                'SAPO_SYNC_ATTEMPTS',
                'SAPO_SYNC_ERROR',
                'SAPO_PAYLOAD',
                'SAPO_SYNCED_AT',
            ]);
        });
    }
};
