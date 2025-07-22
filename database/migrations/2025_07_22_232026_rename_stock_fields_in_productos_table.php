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
        Schema::table('productos', function (Blueprint $table) {
            // Renombrar campos de stock para que sean genéricos
            $table->renameColumn('stock_actual_kg', 'stock_actual');
            $table->renameColumn('stock_disponible_kg', 'stock_disponible');
            $table->renameColumn('costo_promedio_peps', 'costo_promedio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->renameColumn('stock_actual', 'stock_actual_kg');
            $table->renameColumn('stock_disponible', 'stock_disponible_kg');
            $table->renameColumn('costo_promedio', 'costo_promedio_peps');
        });
    }
};
