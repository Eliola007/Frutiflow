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
        Schema::table('proveedors', function (Blueprint $table) {
            // Primero eliminar el índice único
            $table->dropUnique(['documento']);
        });
        
        Schema::table('proveedors', function (Blueprint $table) {
            // Agregar el campo RFC como nullable primero
            $table->string('rfc', 13)->nullable()->after('nombre');
        });
        
        // Actualizar los registros existentes con RFCs de ejemplo
        DB::table('proveedors')->update(['rfc' => 'TEMP000000000']);
        
        Schema::table('proveedors', function (Blueprint $table) {
            // Luego eliminar las columnas documento y tipo_documento
            $table->dropColumn(['documento', 'tipo_documento']);
            
            // Hacer el RFC required y único
            $table->string('rfc', 13)->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proveedors', function (Blueprint $table) {
            // Restaurar los campos originales
            $table->dropColumn('rfc');
            $table->string('documento')->unique()->after('nombre');
            $table->enum('tipo_documento', ['cedula', 'rut', 'nit'])->after('documento');
        });
    }
};
