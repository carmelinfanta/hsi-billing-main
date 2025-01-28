<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mail_notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('mail_notifications', 'setup_completed')) {
                $table->boolean('setup_completion_mail')->default(0)->after('data_submission_mail');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_notifications', function (Blueprint $table) {
            if (Schema::hasColumn('mail_notifications', 'setup_completed')) {
                $table->dropColumn('setup_completion_mail');
            }
        });
    }
};
