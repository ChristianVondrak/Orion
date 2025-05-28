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
        Schema::table('project_users', function (Blueprint $table) {
            // Hacer hourly_rate nullable ya que ahora puede ser flat rate
            $table->float('hourly_rate')->nullable()->change();
            
            // Añadir campos para el tipo de pago y monto fijo
            $table->enum('payment_type', ['hourly', 'flat'])->default('hourly')->after('project_id');
            $table->decimal('flat_rate', 10, 2)->nullable()->after('hourly_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_users', function (Blueprint $table) {
            $table->float('hourly_rate')->change();
            $table->dropColumn(['payment_type', 'flat_rate']);
        });
    }
}; 