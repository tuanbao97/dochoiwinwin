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
            $table->unsignedBigInteger('SAPO_ASSIGNEE_ID')
                ->nullable()
                ->after('SAPO_ORDER_NAME')
                ->index();
            $table->string('SAPO_ASSIGNEE_NAME', 500)
                ->nullable()
                ->after('SAPO_ASSIGNEE_ID');
            $table->dateTime('EXPECTED_DELIVERY_DATE', 6)
                ->nullable()
                ->after('SAPO_ASSIGNEE_NAME');
        });

        // Buộc lần chạy kế tiếp backfill metadata cho các đơn đã đồng bộ trước đây.
        DB::table('setting')
            ->where('CODE', 'SETTING_SAPO_ORDERS_FETCHED_AT')
            ->update(['VALUE' => null, 'UPD_DT' => now()]);
    }

    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table): void {
            $table->dropIndex(['SAPO_ASSIGNEE_ID']);
            $table->dropColumn([
                'SAPO_ASSIGNEE_ID',
                'SAPO_ASSIGNEE_NAME',
                'EXPECTED_DELIVERY_DATE',
            ]);
        });
    }
};
