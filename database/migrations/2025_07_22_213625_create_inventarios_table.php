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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('compra_id')->nullable()->constrained('compras'); // origen de la mercancía
            $table->string('lote')->nullable(); // identificador del lote
            $table->date('fecha_ingreso');
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('cantidad_inicial', 10, 2);
            $table->decimal('cantidad_actual', 10, 2);
            $table->decimal('precio_costo', 10, 2); // precio al que se compró
            $table->enum('estado', ['disponible', 'reservado', 'vencido', 'agotado'])->default('disponible');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Índices para optimizar consultas PEPS
            $table->index(['producto_id', 'fecha_ingreso', 'estado']);
            $table->index(['producto_id', 'fecha_vencimiento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
