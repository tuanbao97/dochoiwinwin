<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_phone_push', function (Blueprint $table) {
            $table->comment('Điện thoại quản trị đã bật thông báo đơn mới');
            $table->bigIncrements('ID');
            $table->bigInteger('USER_ID')->nullable()->comment('Tài khoản admin đã bật');
            $table->char('ENDPOINT_HASH', 64)->comment('SHA-256 của endpoint');
            $table->text('ENDPOINT')->comment('Địa chỉ nhận thông báo của trình duyệt');
            $table->string('PUBLIC_KEY', 255)->comment('Khóa p256dh');
            $table->string('AUTH_TOKEN', 255)->comment('Khóa auth');
            $table->string('CONTENT_ENCODING', 20)->default('aes128gcm');
            $table->dateTime('CRT_DT', 6)->nullable();
            $table->dateTime('UPD_DT', 6)->nullable();

            $table->unique('ENDPOINT_HASH', 'admin_phone_push_endpoint_unique');
            $table->index('USER_ID', 'admin_phone_push_user_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_phone_push');
    }
};
