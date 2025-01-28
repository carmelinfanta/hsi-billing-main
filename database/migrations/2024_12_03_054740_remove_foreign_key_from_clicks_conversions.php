<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveForeignKeyFromClicksConversions extends Migration
{
    public function up()
    {
        Schema::table('clicks_conversions', function (Blueprint $table) {
            $table->dropForeign(['click_id']);
            $table->dropIndex('clicks_conversions_click_id_foreign');
        });
    }

    public function down()
    {
        Schema::table('clicks_conversions', function (Blueprint $table) {
            $table->foreign('click_id')
                ->references('id')
                ->on('clicks')
                ->onDelete('cascade');
        });
    }
}
