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
        // Eliminar el índice único solo si existe (MySQL)
        $indexExists = collect(DB::select("SHOW INDEX FROM clientes WHERE Key_name = 'clientes_documento_unique'"))->isNotEmpty();
        if ($indexExists) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->dropUnique('clientes_documento_unique');
            });
        }
        
        // Eliminar las columnas solo si existen
        if (Schema::hasColumn('clientes', 'documento')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->dropColumn('documento');
            });
        }
        if (Schema::hasColumn('clientes', 'tipo_documento')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->dropColumn('tipo_documento');
            });
        }
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
