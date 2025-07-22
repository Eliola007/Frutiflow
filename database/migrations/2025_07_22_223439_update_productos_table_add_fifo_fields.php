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
        Schema::table('productos', function (Blueprint $table) {
            // Eliminar campos anteriores si existen
            if (Schema::hasColumn('productos', 'categoria')) {
                $table->dropColumn('categoria');
            }
            if (Schema::hasColumn('productos', 'unidad_medida')) {
                $table->dropColumn('unidad_medida');
            }
            if (Schema::hasColumn('productos', 'precio_venta')) {
                $table->dropColumn('precio_venta');
            }
            if (Schema::hasColumn('productos', 'precio_minimo')) {
                $table->dropColumn('precio_minimo');
            }
            if (Schema::hasColumn('productos', 'stock_minimo')) {
                $table->dropColumn('stock_minimo');
            }
            if (Schema::hasColumn('productos', 'stock_actual')) {
                $table->dropColumn('stock_actual');
            }
            if (Schema::hasColumn('productos', 'dias_vencimiento')) {
                $table->dropColumn('dias_vencimiento');
            }
            if (Schema::hasColumn('productos', 'requiere_refrigeracion')) {
                $table->dropColumn('requiere_refrigeracion');
            }
            
            // Agregar nuevos campos para control PEPS
            $table->enum('calidad', ['1A', '2A', '3A'])->after('nombre');
            $table->enum('tamaño', ['Small', 'Medium', 'Large', 'X-Large', 'ND'])->after('calidad');
            $table->string('grupo')->after('tamaño');
            
            // Unidades y factores de conversión
            $table->enum('unidad_base', ['kg', 'lb', 'ton', 'und', 'caja', 'bulto'])->default('kg')->after('descripcion');
            $table->decimal('factor_conversion_kg', 8, 3)->default(1.000)->comment('Factor para convertir a kg');
            $table->decimal('peso_promedio_unidad', 8, 3)->nullable()->comment('Peso promedio por unidad en kg');
            
            // Control PEPS y vencimientos
            $table->integer('dias_vida_util')->default(30)->comment('Días de vida útil desde compra');
            $table->integer('dias_alerta_vencimiento')->default(7)->comment('Días antes de vencer para alertar');
            
            // Stock calculado automáticamente por PEPS
            $table->decimal('stock_actual_kg', 12, 3)->default(0)->comment('Stock actual en kg');
            $table->decimal('stock_disponible_kg', 12, 3)->default(0)->comment('Stock disponible en kg');
            $table->decimal('costo_promedio_peps', 10, 4)->default(0)->comment('Costo promedio PEPS');
            
            // Precios de referencia
            $table->decimal('precio_compra_referencia', 10, 2)->nullable()->comment('Precio promedio de compra');
            $table->decimal('precio_venta_sugerido', 10, 2)->nullable()->comment('Precio sugerido de venta');
            
            // Índices para optimización
            $table->index(['grupo', 'calidad', 'tamaño']);
            $table->index('stock_actual_kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Eliminar nuevos campos
            $table->dropIndex(['grupo', 'calidad', 'tamaño']);
            $table->dropIndex(['stock_actual_kg']);
            
            $table->dropColumn([
                'calidad', 'tamaño', 'grupo',
                'unidad_base', 'factor_conversion_kg', 'peso_promedio_unidad',
                'dias_vida_util', 'dias_alerta_vencimiento',
                'stock_actual_kg', 'stock_disponible_kg', 'costo_promedio_peps',
                'precio_compra_referencia', 'precio_venta_sugerido'
            ]);
            
            // Restaurar campos originales
            $table->enum('categoria', ['fruta_fresca', 'fruta_seca', 'organica', 'importada', 'nacional']);
            $table->string('unidad_medida');
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('precio_minimo', 10, 2)->nullable();
            $table->integer('stock_minimo')->default(0);
            $table->integer('stock_actual')->default(0);
            $table->integer('dias_vencimiento')->nullable();
            $table->boolean('requiere_refrigeracion')->default(false);
        });
    }
};
