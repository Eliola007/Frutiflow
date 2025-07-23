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
        Schema::create('pago_proveedors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedors')->onDelete('cascade');
            $table->decimal('monto', 10, 2);
            $table->enum('tipo_pago', ['pago', 'anticipo', 'abono'])->default('pago');
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'cheque', 'tarjeta'])->default('efectivo');
            $table->date('fecha_pago');
            $table->string('referencia')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_proveedors');
    }
};
