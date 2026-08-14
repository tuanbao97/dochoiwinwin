<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product', function (Blueprint $table) {
            if (! Schema::hasColumn('product', 'SAPO_ID')) {
                $table->unsignedBigInteger('SAPO_ID')->nullable()->unique()->after('ID')->comment('Sapo product id');
            }
            if (! Schema::hasColumn('product', 'SAPO_PAYLOAD')) {
                $table->longText('SAPO_PAYLOAD')->nullable()->comment('Raw Sapo product JSON');
            }
        });

        Schema::table('product_variant', function (Blueprint $table) {
            if (! Schema::hasColumn('product_variant', 'SAPO_VARIANT_ID')) {
                $table->unsignedBigInteger('SAPO_VARIANT_ID')->nullable()->unique()->after('ID')->comment('Sapo variant id');
            }
        });

        Schema::table('document_storage', function (Blueprint $table) {
            if (! Schema::hasColumn('document_storage', 'SAPO_IMAGE_ID')) {
                $table->unsignedBigInteger('SAPO_IMAGE_ID')->nullable()->unique()->comment('Sapo image id');
            }
            if (! Schema::hasColumn('document_storage', 'SOURCE_URL')) {
                $table->string('SOURCE_URL', 2000)->nullable()->index()->comment('Remote image URL (không query)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            if (Schema::hasColumn('product', 'SAPO_ID')) {
                $table->dropUnique(['SAPO_ID']);
                $table->dropColumn('SAPO_ID');
            }
            if (Schema::hasColumn('product', 'SAPO_PAYLOAD')) {
                $table->dropColumn('SAPO_PAYLOAD');
            }
        });

        Schema::table('product_variant', function (Blueprint $table) {
            if (Schema::hasColumn('product_variant', 'SAPO_VARIANT_ID')) {
                $table->dropUnique(['SAPO_VARIANT_ID']);
                $table->dropColumn('SAPO_VARIANT_ID');
            }
        });

        Schema::table('document_storage', function (Blueprint $table) {
            if (Schema::hasColumn('document_storage', 'SAPO_IMAGE_ID')) {
                $table->dropUnique(['SAPO_IMAGE_ID']);
                $table->dropColumn('SAPO_IMAGE_ID');
            }
            if (Schema::hasColumn('document_storage', 'SOURCE_URL')) {
                $table->dropColumn('SOURCE_URL');
            }
        });
    }
};
