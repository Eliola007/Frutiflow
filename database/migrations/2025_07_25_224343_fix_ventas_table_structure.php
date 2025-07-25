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
            // Asegurar que las columnas producto_id, cantidad y precio_unitario no existan
            if (Schema::hasColumn('ventas', 'producto_id')) {
                if (Schema::hasColumn('ventas', 'ventas_producto_id_foreign')) {
                    $table->dropForeign(['producto_id']);
                }
                $table->dropColumn('producto_id');
            }
            
            if (Schema::hasColumn('ventas', 'cantidad')) {
                $table->dropColumn('cantidad');
            }
            
            if (Schema::hasColumn('ventas', 'precio_unitario')) {
                $table->dropColumn('precio_unitario');
            }
            
            // Asegurar que existan los campos necesarios para la estructura multi-producto
            if (!Schema::hasColumn('ventas', 'numero_interno')) {
                $table->string('numero_interno')->nullable()->unique()->after('numero_venta');
            }
            
            if (!Schema::hasColumn('ventas', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->default(0)->after('fecha_venta');
            }
            
            if (!Schema::hasColumn('ventas', 'descuento_general')) {
                $table->decimal('descuento_general', 10, 2)->default(0)->after('subtotal');
            }
            
            if (!Schema::hasColumn('ventas', 'tipo_venta')) {
                $table->string('tipo_venta')->default('contado')->after('metodo_pago');
            }
            
            if (!Schema::hasColumn('ventas', 'monto_recibido')) {
                $table->decimal('monto_recibido', 10, 2)->nullable()->after('tipo_venta');
            }
            
            if (!Schema::hasColumn('ventas', 'cambio')) {
                $table->decimal('cambio', 10, 2)->default(0)->after('monto_recibido');
            }
            
            if (!Schema::hasColumn('ventas', 'notas')) {
                $table->text('notas')->nullable()->after('observaciones');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Revertir cambios si es necesario
            $table->dropColumn([
                'numero_interno', 
                'subtotal', 
                'descuento_general', 
                'tipo_venta', 
                'monto_recibido', 
                'cambio', 
                'notas'
            ]);
        });
    }
};
