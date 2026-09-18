<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('flash_sale_item');
        Schema::dropIfExists('flash_sale');
    }

    public function down(): void
    {
        // Feature removed — no recreate.
    }
};
