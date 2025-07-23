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
        Schema::create('pago_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->onDelete('set null')->comment('Venta relacionada (opcional)');
            $table->decimal('monto', 10, 2)->comment('Monto del pago en MXN');
            $table->enum('tipo_pago', ['abono', 'pago_completo', 'ajuste'])->default('abono')->comment('Tipo de pago');
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'cheque', 'tarjeta'])->default('efectivo')->comment('Método de pago');
            $table->string('referencia', 100)->nullable()->comment('Referencia del pago (cheque, transferencia, etc.)');
            $table->text('observaciones')->nullable()->comment('Observaciones del pago');
            $table->date('fecha_pago')->comment('Fecha del pago');
            $table->foreignId('user_id')->constrained('users')->comment('Usuario que registró el pago');
            $table->timestamps();

            $table->index(['cliente_id', 'fecha_pago']);
            $table->index(['venta_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_clientes');
    }
};
