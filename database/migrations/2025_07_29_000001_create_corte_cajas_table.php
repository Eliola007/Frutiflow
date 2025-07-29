<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corte_cajas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->decimal('efectivo_inicial', 12, 2)->default(0);
            $table->decimal('efectivo_final', 12, 2)->default(0);
            $table->decimal('total_ventas', 12, 2)->default(0);
            $table->decimal('total_ingresos', 12, 2)->default(0);
            $table->decimal('total_egresos', 12, 2)->default(0);
            $table->json('formas_pago')->nullable(); // Desglose por forma de pago
            $table->foreignId('usuario_id')->constrained('users');
            $table->text('observaciones')->nullable();
            $table->boolean('editable')->default(false); // Solo admin puede editar si es true
            $table->timestamps();
            
            // Un solo corte por día
            $table->unique('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corte_cajas');
    }
};
