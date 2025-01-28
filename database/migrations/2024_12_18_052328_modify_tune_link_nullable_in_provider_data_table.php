<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTuneLinkNullableInProviderDataTable extends Migration
{
    public function up()
    {
        Schema::table('provider_data', function (Blueprint $table) {
            $table->text('tune_link')->nullable()->change();  
        });
    }

    public function down()
    {
        Schema::table('provider_data', function (Blueprint $table) {
            $table->text('tune_link')->nullable(false)->change();  
        });
    }
}

