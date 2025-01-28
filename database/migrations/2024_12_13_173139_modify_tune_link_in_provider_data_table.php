<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModifyTuneLinkInProviderDataTable extends Migration
{
    public function up()
    {
        DB::table('provider_data')
            ->whereNull('tune_link')
            ->update(['tune_link' => '']);

        Schema::table('provider_data', function (Blueprint $table) {
            $table->text('tune_link')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('provider_data', function (Blueprint $table) {
            $table->string('tune_link', 255)->nullable(false)->change();
        });
    }
}

