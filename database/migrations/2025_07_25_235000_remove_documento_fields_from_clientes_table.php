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
            // Primero eliminar el índice único del documento si existe
            $table->dropUnique(['documento']);
        });
        
        Schema::table('clientes', function (Blueprint $table) {
            // Luego eliminar los campos documento y tipo_documento ya que no se usan en el sistema
            $table->dropColumn(['documento', 'tipo_documento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Restaurar los campos en caso de rollback
            $table->string('documento')->nullable()->after('rfc');
            $table->enum('tipo_documento', ['curp', 'ine', 'pasaporte', 'cedula_profesional', 'otro'])
                  ->default('curp')
                  ->nullable()
                  ->after('documento');
        });
    }
};
