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
        Schema::table('clientes', function (Blueprint $table) {
            // Campos de control de crédito
            $table->decimal('limite_credito', 10, 2)->default(0)->comment('Límite de crédito en MXN');
            $table->integer('dias_credito')->default(30)->comment('Días de crédito permitidos');
            $table->decimal('saldo_pendiente', 10, 2)->default(0)->comment('Saldo actual pendiente');
            $table->enum('estado_credito', ['activo', 'suspendido', 'bloqueado'])->default('activo')->comment('Estado del crédito del cliente');
            $table->decimal('descuento_especial', 5, 2)->nullable()->comment('Descuento especial en porcentaje');
            $table->timestamp('ultima_compra')->nullable()->comment('Fecha de la última compra');
            $table->decimal('total_compras', 12, 2)->default(0)->comment('Total histórico de compras');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'limite_credito',
                'dias_credito', 
                'saldo_pendiente',
                'estado_credito',
                'descuento_especial',
                'ultima_compra',
                'total_compras'
            ]);
        });
    }
};
