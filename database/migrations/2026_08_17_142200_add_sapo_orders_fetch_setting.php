<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const CODE = 'SETTING_SAPO_ORDERS_FETCHED_AT';

    public function up(): void
    {
        DB::table('setting')->updateOrInsert(
            ['CODE' => self::CODE],
            [
                'NAME' => 'Mốc đồng bộ đơn hàng Sapo gần nhất',
                'TYPE' => 'SETTING_SYSTEM',
                'VALUE' => null,
                'UNIT' => 'ISO-8601 UTC',
                'DESCRIPTION' => 'Mốc chung dùng để kéo các đơn Sapo thay đổi đến hiện tại.',
                'UPD_DT' => now(),
                'UPD_NAME' => 'SYSTEM',
                'STATUS' => 'USING',
                'IS_ACTIVE' => true,
            ]
        );
    }

    public function down(): void
    {
        DB::table('setting')->where('CODE', self::CODE)->delete();
    }
};
