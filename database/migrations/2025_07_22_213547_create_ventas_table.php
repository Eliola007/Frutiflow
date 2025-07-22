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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_venta')->unique();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->foreignId('user_id')->constrained('users'); // vendedor
            $table->date('fecha_venta');
            $table->foreignId('producto_id')->constrained('productos');
            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('impuestos', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'credito']);
            $table->enum('estado', ['completada', 'pendiente', 'cancelada'])->default('completada');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
