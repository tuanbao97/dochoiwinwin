<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('user_social_account', 'IS_SENSITIVE')) {
            return;
        }

        Schema::table('user_social_account', function (Blueprint $table) {
            // Zalo is_sensitive: người dùng cần xác minh/xử lý dữ liệu theo quy định pháp luật
            $table->boolean('IS_SENSITIVE')->default(false)->after('AVATAR_URL');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('user_social_account', 'IS_SENSITIVE')) {
            return;
        }

        Schema::table('user_social_account', function (Blueprint $table) {
            $table->dropColumn('IS_SENSITIVE');
        });
    }
};
