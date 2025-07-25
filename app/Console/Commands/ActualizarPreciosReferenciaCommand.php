<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;

class ActualizarPreciosReferenciaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productos:actualizar-precios {--tipo=ambos : Tipo de precios a actualizar (compra, venta, ambos)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza automáticamente los precios de compra y/o venta de todos los productos basado en el promedio del año actual';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tipo = $this->option('tipo');
        
        if (!in_array($tipo, ['compra', 'venta', 'ambos'])) {
            $this->error('El tipo debe ser: compra, venta o ambos');
            return Command::FAILURE;
        }
        
        $this->info("Iniciando actualización de precios ({$tipo})...");
        
        $productos = Producto::all();
        $actualizadosCompra = 0;
        $actualizadosVenta = 0;
        $errores = 0;
        
        $this->withProgressBar($productos, function ($producto) use (&$actualizadosCompra, &$actualizadosVenta, &$errores, $tipo) {
            try {
                $precioCompraAnterior = $producto->precio_compra_referencia;
                $precioVentaAnterior = $producto->precio_venta_sugerido;
                
                if (in_array($tipo, ['compra', 'ambos'])) {
                    $producto->actualizarPrecioReferenciaAutomatico();
                }
                
                if (in_array($tipo, ['venta', 'ambos'])) {
                    $producto->actualizarPrecioVentaAutomatico();
                }
                
                $producto->refresh();
                
                if ($producto->precio_compra_referencia != $precioCompraAnterior) {
                    $actualizadosCompra++;
                }
                
                if ($producto->precio_venta_sugerido != $precioVentaAnterior) {
                    $actualizadosVenta++;
                }
                
            } catch (\Exception $e) {
                $errores++;
                $this->error("Error actualizando producto {$producto->nombre}: " . $e->getMessage());
            }
        });
        
        $this->newLine(2);
        $this->info("Actualización completada:");
        $this->info("- Total productos procesados: {$productos->count()}");
        
        if (in_array($tipo, ['compra', 'ambos'])) {
            $this->info("- Precios de compra actualizados: {$actualizadosCompra}");
        }
        
        if (in_array($tipo, ['venta', 'ambos'])) {
            $this->info("- Precios de venta actualizados: {$actualizadosVenta}");
        }
        
        $this->info("- Errores encontrados: {$errores}");
        
        return Command::SUCCESS;
    }
}
