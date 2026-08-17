<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table): void {
            $table->unsignedBigInteger('SHIPPING_VOUCHER_ID')
                ->nullable()
                ->after('DISCOUNT_SNAPSHOT')
                ->index();
            $table->string('SHIPPING_CODE', 255)
                ->nullable()
                ->after('SHIPPING_VOUCHER_ID');
        });
    }

    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table): void {
            $table->dropIndex(['SHIPPING_VOUCHER_ID']);
            $table->dropColumn(['SHIPPING_VOUCHER_ID', 'SHIPPING_CODE']);
        });
    }
};
