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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->enum('categoria', ['fruta_fresca', 'fruta_seca', 'organica', 'importada', 'nacional']);
            $table->string('unidad_medida'); // kg, unidad, caja, etc.
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('precio_minimo', 10, 2)->nullable();
            $table->integer('stock_minimo')->default(0);
            $table->integer('stock_actual')->default(0);
            $table->integer('dias_vencimiento')->nullable(); // días de vida útil
            $table->boolean('requiere_refrigeracion')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
