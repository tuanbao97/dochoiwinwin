<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sapo_sync_state', function (Blueprint $table) {
            $table->comment('Cursor đồng bộ Sapo — last_fetch_api_sapo là UTC');
            $table->increments('ID');
            $table->string('SCOPE', 100)->unique();
            $table->dateTime('LAST_FETCH_API_SAPO')->nullable()->comment('UTC lần GET Sapo gần nhất');
            $table->dateTime('CRT_DT')->nullable();
            $table->dateTime('UPD_DT')->nullable();
        });

        Schema::create('sapo_product_cache', function (Blueprint $table) {
            $table->comment('Cache sản phẩm Sapo để không GET all mỗi request');
            $table->unsignedBigInteger('ID')->primary()->comment('Sapo product id');
            $table->string('NAME', 1000)->nullable();
            $table->string('ALIAS', 500)->nullable();
            $table->string('PRODUCT_TYPE', 255)->nullable();
            $table->string('STATUS', 50)->nullable();
            $table->dateTime('MODIFIED_ON')->nullable()->comment('Sapo modified_on (UTC)');
            $table->longText('PAYLOAD')->nullable();
            $table->dateTime('LAST_FETCH_API_SAPO')->nullable()->comment('UTC lúc row được ghi từ API');
            $table->dateTime('CRT_DT')->nullable();
            $table->dateTime('UPD_DT')->nullable();
            $table->index(['PRODUCT_TYPE', 'STATUS']);
            $table->index('MODIFIED_ON');
        });

        Schema::create('sapo_product_collection', function (Blueprint $table) {
            $table->unsignedBigInteger('SAPO_PRODUCT_ID');
            $table->unsignedBigInteger('SAPO_COLLECTION_ID');
            $table->primary(['SAPO_PRODUCT_ID', 'SAPO_COLLECTION_ID']);
            $table->index('SAPO_COLLECTION_ID');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sapo_product_collection');
        Schema::dropIfExists('sapo_product_cache');
        Schema::dropIfExists('sapo_sync_state');
    }
};
