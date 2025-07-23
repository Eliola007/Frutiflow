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
        Schema::table('proveedors', function (Blueprint $table) {
            $table->decimal('limite_credito', 10, 2)->default(0)->after('activo');
            $table->integer('dias_credito')->default(30)->after('limite_credito');
            $table->decimal('saldo_pendiente', 10, 2)->default(0)->after('dias_credito');
            $table->enum('estado_credito', ['activo', 'suspendido', 'bloqueado'])->default('activo')->after('saldo_pendiente');
            $table->decimal('descuento_especial', 5, 2)->nullable()->after('estado_credito');
            $table->timestamp('ultima_compra')->nullable()->after('descuento_especial');
            $table->decimal('total_compras', 12, 2)->default(0)->after('ultima_compra');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proveedors', function (Blueprint $table) {
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
