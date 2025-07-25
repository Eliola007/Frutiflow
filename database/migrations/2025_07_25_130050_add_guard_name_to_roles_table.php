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
        Schema::table('roles', function (Blueprint $table) {
            $table->string('guard_name')->default('web')->after('nombre');
            $table->string('name')->nullable()->after('guard_name'); // Para compatibilidad con Spatie
            
            // Agregar índice único para name y guard_name (requerido por Spatie)
            $table->unique(['name', 'guard_name']);
        });

        // Actualizar registros existentes
        DB::table('roles')->update([
            'guard_name' => 'web',
            'name' => DB::raw('nombre')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique(['name', 'guard_name']);
            $table->dropColumn(['guard_name', 'name']);
        });
    }
};
