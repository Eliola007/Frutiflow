<?php

namespace App\Console\Commands;

use App\Models\Inventario;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ActualizarInventarioVencimientos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventario:actualizar-vencimientos {--force : Forzar actualización sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el estado de los productos vencidos en el inventario';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Buscando productos vencidos...');

        // Buscar productos vencidos que no estén marcados como vencidos
        $vencidos = Inventario::where('fecha_vencimiento', '<', Carbon::now())
            ->where('estado', '!=', 'vencido')
            ->where('cantidad_actual', '>', 0)
            ->get();

        if ($vencidos->isEmpty()) {
            $this->info('✅ No se encontraron productos vencidos.');
            return Command::SUCCESS;
        }

        $this->warn("⚠️  Se encontraron {$vencidos->count()} lotes vencidos:");

        // Mostrar tabla de productos vencidos
        $headers = ['Producto', 'Lote', 'Cantidad', 'Vencimiento', 'Días Vencido'];
        $rows = [];

        foreach ($vencidos as $item) {
            $diasVencido = $item->fecha_vencimiento->diffInDays(Carbon::now());
            $rows[] = [
                $item->producto->nombre ?? 'N/A',
                $item->lote,
                number_format($item->cantidad_actual, 2),
                $item->fecha_vencimiento->format('d/m/Y'),
                $diasVencido . ' días'
            ];
        }

        $this->table($headers, $rows);

        // Calcular valor total de productos vencidos
        $valorTotal = $vencidos->sum(function ($item) {
            return $item->cantidad_actual * $item->precio_costo;
        });

        $this->warn("💰 Valor total de productos vencidos: $" . number_format($valorTotal, 2));

        // Confirmar acción
        if (!$this->option('force')) {
            if (!$this->confirm('¿Desea marcar estos productos como vencidos?')) {
                $this->info('Operación cancelada.');
                return Command::SUCCESS;
            }
        }

        // Marcar como vencidos
        $this->info('🔄 Marcando productos como vencidos...');
        
        $bar = $this->output->createProgressBar($vencidos->count());
        $bar->start();

        $actualizados = 0;
        foreach ($vencidos as $item) {
            try {
                $item->marcarComoVencido();
                $actualizados++;
            } catch (\Exception $e) {
                $this->error("Error al procesar lote {$item->lote}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($actualizados > 0) {
            $this->info("✅ Se marcaron {$actualizados} lotes como vencidos.");
            $this->info("📊 Stock actualizado automáticamente.");
            
            // Mostrar resumen
            $this->info("\n📋 Resumen:");
            $this->line("   • Lotes procesados: {$vencidos->count()}");
            $this->line("   • Lotes actualizados: {$actualizados}");
            $this->line("   • Valor afectado: $" . number_format($valorTotal, 2));
        } else {
            $this->warn("❌ No se pudo actualizar ningún lote.");
        }

        return Command::SUCCESS;
    }
}
