<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_cart', function (Blueprint $table) {
            $table->comment('Giỏ hàng theo tài khoản cửa hàng');
            $table->bigIncrements('ID');
            $table->bigInteger('USER_ID')->comment('Khách hàng (user.ID)');
            $table->unsignedBigInteger('VARIANT_ID')->comment('Biến thể sản phẩm trong catalog');
            $table->unsignedBigInteger('PRODUCT_ID')->nullable()->comment('Sản phẩm');
            $table->unsignedBigInteger('SAPO_VARIANT_ID')->nullable()->comment('Mã biến thể Sapo');
            $table->string('TITLE', 1000)->nullable()->comment('Tên sản phẩm lúc thêm giỏ');
            $table->string('VARIANT_TITLE', 500)->nullable()->comment('Tên biến thể');
            $table->unsignedInteger('QUANTITY')->default(1)->comment('Số lượng');
            $table->unsignedBigInteger('PRICE')->default(0)->comment('Đơn giá (VNĐ) lúc thêm/cập nhật');
            $table->unsignedBigInteger('LINE_PRICE')->default(0)->comment('Thành tiền = QUANTITY * PRICE');
            $table->text('IMAGE')->nullable()->comment('Ảnh dòng giỏ');
            $table->string('HANDLE', 500)->nullable()->comment('Đường dẫn sản phẩm');
            $table->unsignedBigInteger('CATEGORY_ID')->nullable()->comment('Danh mục (gợi ý mua kèm)');
            $table->unsignedInteger('STOCK')->nullable()->comment('Tồn kho cache lúc thêm/sửa');
            $table->unsignedInteger('POSITION')->default(0)->comment('Thứ tự hiển thị, 0 = đầu giỏ');
            $table->dateTime('CRT_DT', 6)->nullable();
            $table->dateTime('UPD_DT', 6)->nullable();

            $table->unique(['USER_ID', 'VARIANT_ID'], 'user_cart_user_variant_unique');
            $table->index('VARIANT_ID', 'user_cart_variant_index');
            $table->foreign('USER_ID')
                ->references('ID')
                ->on('user')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_cart');
    }
};
