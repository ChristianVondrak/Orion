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
        Schema::table('timmings', function (Blueprint $table) {
            // Para acelerar WHERE user_id = X AND from_timestamp BETWEEN A AND B
            $table->index(['user_id','from_timestamp'], 'timmings_user_from_idx');
        });

        Schema::table('planned_user_hours', function (Blueprint $table) {
            // Para acelerar JOIN ON user_id AND week_start
            $table->index(['user_id','week_start'], 'planned_user_week_idx');
        });

        Schema::table('worksnap_users', function (Blueprint $table) {
            // Para WHERE email IS NOT NULL AND email <> ''
            $table->index('email', 'users_email_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
