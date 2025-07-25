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
        Schema::table('gastos', function (Blueprint $table) {
            // Agregar relación con concepto de gasto
            $table->foreignId('concepto_gasto_id')->nullable()->after('categoria')->constrained('concepto_gastos');
            
            // Agregar relación con proveedor (opcional)
            $table->foreignId('proveedor_id')->nullable()->after('concepto_gasto_id')->constrained('proveedors');
            
            // Agregar relación con producto (opcional)
            $table->foreignId('producto_id')->nullable()->after('proveedor_id')->constrained('productos');
            
            // Agregar campo para numeración automática
            $table->string('numero_interno')->nullable()->unique()->after('numero_comprobante');
            
            // Agregar campo para gastos recurrentes
            $table->boolean('es_recurrente')->default(false)->after('archivo_soporte');
            
            // Agregar campo para período de recurrencia
            $table->enum('periodo_recurrencia', ['semanal', 'quincenal', 'mensual', 'bimestral', 'trimestral', 'anual'])
                  ->nullable()->after('es_recurrente');
            
            // Agregar índices para búsquedas rápidas
            $table->index('fecha_gasto');
            $table->index('es_recurrente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->dropForeign(['concepto_gasto_id']);
            $table->dropForeign(['proveedor_id']);
            $table->dropForeign(['producto_id']);
            $table->dropIndex(['fecha_gasto']);
            $table->dropIndex(['es_recurrente']);
            $table->dropColumn([
                'concepto_gasto_id',
                'proveedor_id', 
                'producto_id',
                'numero_interno',
                'es_recurrente',
                'periodo_recurrencia'
            ]);
        });
    }
};
