<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clicks_conversions', function (Blueprint $table) {
            $table->index('click_id', 'click_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('clicks_conversions', function (Blueprint $table) {
            $table->dropIndex('click_id_index');
        });
    }
};
