<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table): void {
            $table->text('SAPO_CANCEL_REASON')
                ->nullable()
                ->after('EXPECTED_DELIVERY_DATE');
        });

        // Buộc lần kéo kế tiếp cập nhật lý do cho các đơn đã hủy gần đây.
        DB::table('setting')
            ->where('CODE', 'SETTING_SAPO_ORDERS_FETCHED_AT')
            ->update(['VALUE' => null, 'UPD_DT' => now()]);
    }

    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table): void {
            $table->dropColumn('SAPO_CANCEL_REASON');
        });
    }
};
