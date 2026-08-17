<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->unsignedBigInteger('SHIPPING_FEE')
                ->default(0)
                ->after('TOTAL_PRICE')
                ->comment('Phí vận chuyển tại thời điểm đặt hàng');
        });
    }

    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->dropColumn('SHIPPING_FEE');
        });
    }
};
