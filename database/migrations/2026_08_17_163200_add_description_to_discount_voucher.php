<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discount_voucher', function (Blueprint $table): void {
            $table->text('DESCRIPTION')->nullable()->after('TITLE');
        });
    }

    public function down(): void
    {
        Schema::table('discount_voucher', function (Blueprint $table): void {
            $table->dropColumn('DESCRIPTION');
        });
    }
};
