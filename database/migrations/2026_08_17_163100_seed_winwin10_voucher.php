<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('discount_voucher')->updateOrInsert(
            ['CODE' => 'WINWIN10'],
            [
                'SAPO_PRICE_RULE_ID' => 2198232,
                'SAPO_DISCOUNT_CODE_ID' => 3426472,
                'TITLE' => 'WINWIN10',
                'DISCOUNT_TYPE' => 'PERCENTAGE',
                // 1000 basis points = 10%.
                'DISCOUNT_VALUE' => 1000,
                'MAX_DISCOUNT_AMOUNT' => 30000,
                'MIN_SUBTOTAL' => 300000,
                'USAGE_LIMIT' => 100,
                'ONCE_PER_CUSTOMER' => true,
                'STARTS_AT' => now(),
                'ENDS_AT' => null,
                'SAPO_PAYLOAD' => json_encode([
                    'managed_by' => 'migration',
                    'note' => 'Max 30.000 VND is enforced by Win Win checkout.',
                ], JSON_UNESCAPED_UNICODE),
                'CRT_DT' => now(),
                'UPD_DT' => now(),
                'STATUS' => 'USING',
                'IS_ACTIVE' => true,
            ]
        );
    }

    public function down(): void
    {
        DB::table('discount_voucher')
            ->where('CODE', 'WINWIN10')
            ->where('SAPO_PRICE_RULE_ID', 2198232)
            ->where('SAPO_DISCOUNT_CODE_ID', 3426472)
            ->delete();
    }
};
