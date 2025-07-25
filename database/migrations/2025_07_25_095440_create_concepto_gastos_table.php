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
        Schema::create('concepto_gastos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // PROPINA TOMATE, SANITARIA TOMATE, etc.
            $table->string('grupo')->nullable(); // tomate, mango, platano, general, etc.
            $table->enum('categoria', [
                'operativo',        // Gastos operativos del día a día
                'logistica',        // Transporte, fletes, descargas
                'personal',         // Sueldos, propinas, horas extra
                'servicios',        // Luz, agua, telecomunicaciones
                'mantenimiento',    // Reparaciones, pintura, clima
                'sanitario',        // Fitosanitarios, limpieza
                'administrativo',   // Papelería, estacionamiento
                'comisiones',       // Comisiones por ventas
                'otros'            // Gastos varios
            ])->default('otros');
            $table->boolean('es_recurrente')->default(false); // Si se repite mensualmente
            $table->boolean('activo')->default(true);
            $table->text('descripcion')->nullable();
            $table->timestamps();
            
            // Índices para búsquedas rápidas
            $table->index('grupo');
            $table->index('categoria');
            $table->index('es_recurrente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concepto_gastos');
    }
};
