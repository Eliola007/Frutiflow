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
        Schema::table('compras', function (Blueprint $table) {
            // Información de pago
            $table->enum('tipo_pago', ['contado', 'credito', 'credito_enganche'])->default('contado');
            $table->decimal('monto_enganche', 10, 2)->nullable();
            $table->date('fecha_limite_pago')->nullable();
            
            // Información del lote
            $table->string('lote', 50)->nullable();
            
            // Información adicional
            $table->string('numero_remision', 100)->nullable();
            $table->text('notas')->nullable();
            
            // Modificar estado para incluir solo 'pendiente' y 'recibida'
            $table->enum('estado', ['pendiente', 'recibida'])->default('pendiente')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_pago',
                'monto_enganche', 
                'fecha_limite_pago',
                'lote',
                'numero_remision',
                'notas'
            ]);
        });
    }
};
