<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_voucher', function (Blueprint $table): void {
            $table->bigIncrements('ID');
            $table->unsignedBigInteger('SAPO_PRICE_RULE_ID')->nullable()->index();
            $table->unsignedBigInteger('SAPO_DISCOUNT_CODE_ID')->nullable()->index();
            $table->string('CODE', 255)->unique();
            $table->string('TITLE', 500);
            $table->string('DISCOUNT_TYPE', 50);
            // PERCENTAGE: basis points (1000 = 10%); FIXED_AMOUNT: số nguyên VND.
            $table->unsignedBigInteger('DISCOUNT_VALUE');
            $table->unsignedBigInteger('MAX_DISCOUNT_AMOUNT')->nullable();
            $table->unsignedBigInteger('MIN_SUBTOTAL')->default(0);
            $table->unsignedInteger('USAGE_LIMIT')->nullable();
            $table->boolean('ONCE_PER_CUSTOMER')->default(false);
            $table->dateTime('STARTS_AT', 6);
            $table->dateTime('ENDS_AT', 6)->nullable();
            $table->json('SAPO_PAYLOAD')->nullable();
            $table->dateTime('CRT_DT', 6)->nullable();
            $table->dateTime('UPD_DT', 6)->nullable();
            $table->string('STATUS', 50)->default('USING');
            $table->boolean('IS_ACTIVE')->default(true);
        });

        Schema::table('transaction', function (Blueprint $table): void {
            $table->unsignedBigInteger('DISCOUNT_VOUCHER_ID')
                ->nullable()
                ->after('SHIPPING_FEE')
                ->index();
            $table->string('DISCOUNT_CODE', 255)
                ->nullable()
                ->after('DISCOUNT_VOUCHER_ID');
            $table->unsignedBigInteger('DISCOUNT_AMOUNT')
                ->default(0)
                ->after('DISCOUNT_CODE');
            $table->json('DISCOUNT_SNAPSHOT')
                ->nullable()
                ->after('DISCOUNT_AMOUNT');
        });
    }

    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table): void {
            $table->dropIndex(['DISCOUNT_VOUCHER_ID']);
            $table->dropColumn([
                'DISCOUNT_VOUCHER_ID',
                'DISCOUNT_CODE',
                'DISCOUNT_AMOUNT',
                'DISCOUNT_SNAPSHOT',
            ]);
        });

        Schema::dropIfExists('discount_voucher');
    }
};
