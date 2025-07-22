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
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_comprobante')->unique();
            $table->enum('categoria', ['transporte', 'almacenamiento', 'empaque', 'administrativo', 'servicios', 'otros']);
            $table->string('descripcion');
            $table->decimal('monto', 10, 2);
            $table->date('fecha_gasto');
            $table->foreignId('user_id')->constrained('users'); // usuario que registra
            $table->string('proveedor_gasto')->nullable(); // para gastos no relacionados con proveedores
            $table->text('observaciones')->nullable();
            $table->string('archivo_soporte')->nullable(); // ruta del archivo adjunto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
