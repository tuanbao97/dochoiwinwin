<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->unsignedBigInteger('SUBTOTAL_PRICE')
                ->default(0)
                ->after('TOTAL_QUANTITY')
                ->comment('Tạm tính sản phẩm tại thời điểm đồng bộ gần nhất');
        });

        DB::table('transaction')->update([
            'SUBTOTAL_PRICE' => DB::raw('GREATEST(TOTAL_PRICE - SHIPPING_FEE, 0)'),
        ]);

        Schema::table('order', function (Blueprint $table) {
            // Sapo có thể thêm sản phẩm mới chưa kịp import vào catalog local.
            $table->bigInteger('PRODUCT_ID')->nullable()->change();
            $table->bigInteger('SAPO_LINE_ITEM_ID')->nullable()->after('TRANSACTION_ID');
            $table->bigInteger('SAPO_PRODUCT_ID')->nullable()->index()->after('PRODUCT_ID');
            $table->unique(
                ['TRANSACTION_ID', 'SAPO_LINE_ITEM_ID'],
                'order_transaction_sapo_line_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropUnique('order_transaction_sapo_line_unique');
            $table->dropColumn(['SAPO_LINE_ITEM_ID', 'SAPO_PRODUCT_ID']);
        });

        // Chỉ có thể khôi phục NOT NULL sau khi bỏ các dòng Sapo chưa map catalog.
        DB::table('order')->whereNull('PRODUCT_ID')->delete();
        Schema::table('order', function (Blueprint $table) {
            $table->bigInteger('PRODUCT_ID')->nullable(false)->change();
        });

        Schema::table('transaction', function (Blueprint $table) {
            $table->dropColumn('SUBTOTAL_PRICE');
        });
    }
};
