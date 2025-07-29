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
            // Agregar campo RFC como requerido
            $table->string('rfc', 13)->after('nombre');
            
            // Hacer documento opcional (remover unique si existe)
            $table->string('documento')->nullable()->change();
            
            // Actualizar las opciones de tipo_documento para México
            $table->dropColumn('tipo_documento');
        });
        
        // Recrear el campo tipo_documento con nuevas opciones
        Schema::table('clientes', function (Blueprint $table) {
            $table->enum('tipo_documento', ['curp', 'ine', 'pasaporte', 'cedula_profesional', 'otro'])->default('curp')->nullable()->after('documento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Remover RFC
            $table->dropColumn('rfc');
            
            // Restaurar documento como requerido
            $table->string('documento')->nullable(false)->change();
            
            // Restaurar tipo_documento original
            $table->dropColumn('tipo_documento');
        });
        
        Schema::table('clientes', function (Blueprint $table) {
            $table->enum('tipo_documento', ['cedula', 'rut', 'pasaporte', 'nit'])->default('cedula')->after('documento');
        });
    }
};
