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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->text('direccion')->nullable();
            $table->decimal('limite_credito', 12, 2)->default(0);
            $table->integer('dias_credito')->default(0);
            $table->decimal('saldo_pendiente', 12, 2)->default(0);
            $table->enum('estado_credito', ['activo', 'suspendido', 'bloqueado'])->default('activo');
            $table->decimal('descuento_especial', 5, 2)->default(0);
            $table->decimal('total_compras', 12, 2)->default(0);
            $table->timestamp('ultima_compra')->nullable();
            $table->boolean('activo')->default(true);
            $table->string('rfc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
