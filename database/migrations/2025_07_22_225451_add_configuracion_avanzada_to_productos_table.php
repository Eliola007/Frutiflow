<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Agregar toggle para configuración avanzada
            $table->boolean('configuracion_avanzada')->default(false)->after('grupo');
            
            // Cambiar unidad_base por defecto a 'caja'
            $table->string('unidad_base')->default('caja')->change();
        });
        
        // Actualizar registros existentes para usar 'caja' como unidad base
        DB::table('productos')->update(['unidad_base' => 'caja']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('configuracion_avanzada');
            $table->string('unidad_base')->default('kg')->change();
        });
    }
};
