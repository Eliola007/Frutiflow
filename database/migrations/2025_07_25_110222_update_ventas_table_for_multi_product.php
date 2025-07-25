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
        Schema::table('ventas', function (Blueprint $table) {
            // Remover campos de producto único si existen
            if (Schema::hasColumn('ventas', 'producto_id')) {
                $table->dropForeign(['producto_id']);
                $table->dropColumn(['producto_id', 'cantidad', 'precio_unitario']);
            }
            
            // Agregar campos para multi-producto
            $table->string('numero_interno')->nullable()->unique()->after('numero_venta');
            $table->decimal('subtotal', 10, 2)->default(0)->after('fecha_venta');
            $table->decimal('descuento_general', 10, 2)->default(0)->after('subtotal');
            $table->string('tipo_venta')->default('contado')->after('metodo_pago'); // contado, credito, mayoreo, menudeo
            $table->decimal('monto_recibido', 10, 2)->nullable()->after('tipo_venta');
            $table->decimal('monto_anticipo', 10, 2)->default(0)->after('monto_recibido');
            $table->decimal('cambio', 10, 2)->default(0)->after('monto_anticipo');
            $table->text('notas')->nullable()->after('observaciones');
            
            // Modificar campo de descuento existente si existe
            if (Schema::hasColumn('ventas', 'descuento')) {
                $table->dropColumn('descuento');
            }
            
            // Agregar índices para consultas rápidas
            $table->index('fecha_venta');
            $table->index('estado');
            $table->index('tipo_venta');
            $table->index('metodo_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropIndex(['fecha_venta']);
            $table->dropIndex(['estado']);
            $table->dropIndex(['tipo_venta']);
            $table->dropIndex(['metodo_pago']);
            
            $table->dropColumn([
                'numero_interno',
                'subtotal',
                'descuento_general',
                'tipo_venta',
                'monto_recibido',
                'monto_anticipo',
                'cambio',
                'notas'
            ]);
            
            // Restaurar campos de producto único
            $table->foreignId('producto_id')->nullable()->constrained('productos');
            $table->decimal('cantidad', 10, 2)->nullable();
            $table->decimal('precio_unitario', 10, 2)->nullable();
            $table->decimal('descuento', 10, 2)->default(0);
        });
    }
};
