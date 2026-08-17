<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_social_account', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->bigInteger('USER_ID');
            $table->string('PROVIDER', 30);
            $table->string('PROVIDER_USER_ID', 255);
            $table->string('EMAIL', 500);
            $table->string('NAME', 500)->nullable();
            $table->text('AVATAR_URL')->nullable();
            $table->dateTime('CRT_DT', 6)->nullable();
            $table->dateTime('UPD_DT', 6)->nullable();

            $table->unique(['PROVIDER', 'PROVIDER_USER_ID'], 'user_social_provider_user_unique');
            $table->unique(['USER_ID', 'PROVIDER'], 'user_social_user_provider_unique');
            $table->index('EMAIL', 'user_social_email_index');
            $table->foreign('USER_ID')
                ->references('ID')
                ->on('user')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_social_account');
    }
};
