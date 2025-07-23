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
            // Primero eliminar la clave foránea
            $table->dropForeign(['producto_id']);
            
            // Luego eliminar campos que ahora están en compra_items
            $table->dropColumn([
                'producto_id',
                'cantidad',
                'precio_unitario',
                'descuento',
                'fecha_vencimiento',
                'lote'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            // Restaurar campos si es necesario hacer rollback
            $table->foreignId('producto_id')->nullable()->constrained();
            $table->decimal('cantidad', 8, 2)->nullable();
            $table->decimal('precio_unitario', 8, 2)->nullable();
            $table->decimal('descuento', 8, 2)->default(0);
            $table->date('fecha_vencimiento')->nullable();
            $table->string('lote', 50)->nullable();
        });
    }
};
