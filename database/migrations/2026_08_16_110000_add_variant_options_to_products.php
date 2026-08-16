<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->json('VARIANT_OPTIONS')->nullable()->after('SAPO_PAYLOAD');
        });

        Schema::table('product_variant', function (Blueprint $table) {
            $table->json('OPTION_VALUES')->nullable()->after('SAPO_VARIANT_ID');
            $table->string('SKU', 255)->nullable()->after('OPTION_VALUES');
            $table->integer('INVENTORY_QUANTITY')->nullable()->after('SKU');
        });
    }

    public function down(): void
    {
        Schema::table('product_variant', function (Blueprint $table) {
            $table->dropColumn(['OPTION_VALUES', 'SKU', 'INVENTORY_QUANTITY']);
        });

        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('VARIANT_OPTIONS');
        });
    }
};
