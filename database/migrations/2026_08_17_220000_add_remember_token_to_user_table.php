<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('user', 'REMEMBER_TOKEN')) {
            return;
        }

        Schema::table('user', function (Blueprint $table) {
            // Giữ phiên đăng nhập cửa hàng sau khi đóng trình duyệt.
            $table->string('REMEMBER_TOKEN', 100)->nullable()->after('RESET_DATE');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('user', 'REMEMBER_TOKEN')) {
            return;
        }

        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('REMEMBER_TOKEN');
        });
    }
};
