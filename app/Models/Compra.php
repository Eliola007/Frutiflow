<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compra extends Model
{
    protected $fillable = [
        'numero_factura',
        'numero_remision',
        'proveedor_id',
        'user_id',
        'fecha_compra',
        'total',
        'tipo_pago',
        'monto_enganche',
        'fecha_limite_pago',
        'estado',
        'observaciones',
        'notas'
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'fecha_limite_pago' => 'date',
        'total' => 'decimal:2',
        'monto_enganche' => 'decimal:2'
    ];

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CompraItem::class);
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventario::class);
    }

    // Método para crear entrada en inventario al recibir la compra
    public function recibirCompra(): void
    {
        if ($this->estado === 'recibida') {
            return;
        }

        // Procesar cada item de la compra
        foreach ($this->items as $item) {
            // Generar lote si no existe
            if (!$item->lote) {
                $item->lote = $this->generarLote();
                $item->save();
            }

            // Crear registro en inventario
            Inventario::create([
                'producto_id' => $item->producto_id,
                'compra_id' => $this->id,
                'lote' => $item->lote,
                'fecha_ingreso' => $this->fecha_compra,
                'cantidad_inicial' => $item->cantidad,
                'cantidad_actual' => $item->cantidad,
                'precio_costo' => $item->precio_unitario,
                'estado' => 'disponible'
            ]);

            // Actualizar stock del producto
            $producto = $item->producto;
            $producto->stock_actual += $item->cantidad;
            $producto->save();
        }

        // Si es crédito con enganche, crear el pago del enganche
        if ($this->tipo_pago === 'credito_enganche' && $this->monto_enganche > 0) {
            $this->crearPagoEnganche();
        }

        // Cambiar estado a recibida
        $this->update(['estado' => 'recibida']);
    }

    // Método para generar lote automáticamente
    private function generarLote(): string
    {
        $ultimoLote = static::whereNotNull('lote')
            ->where('lote', 'like', 'LOTE-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimoLote && preg_match('/LOTE-(\d+)-(\d{4})/', $ultimoLote->lote, $matches)) {
            $numero = intval($matches[1]) + 1;
            $year = $matches[2];
            
            // Si cambió el año, reiniciar numeración
            if ($year != now()->year) {
                $numero = 1;
                $year = now()->year;
            }
        } else {
            $numero = 1;
            $year = now()->year;
        }

        return sprintf('LOTE-%03d-%s', $numero, $year);
    }

    // Método para crear pago de enganche
    private function crearPagoEnganche(): void
    {
        if (!$this->proveedor) {
            return;
        }

        \App\Models\PagoProveedor::create([
            'proveedor_id' => $this->proveedor_id,
            'monto' => $this->monto_enganche,
            'fecha_pago' => now(),
            'concepto' => 'Enganche compra #' . $this->numero_factura,
            'user_id' => $this->user_id
        ]);
    }

    // Método para calcular diferencia de precio con promedio
    public function diferenciaPrecioPromedio($productoId, $precioUnitario): array
    {
        $promedioCompras = static::join('compra_items', 'compras.id', '=', 'compra_items.compra_id')
            ->where('compra_items.producto_id', $productoId)
            ->where('compras.estado', 'recibida')
            ->where('compras.id', '!=', $this->id)
            ->avg('compra_items.precio_unitario');

        if (!$promedioCompras) {
            return [
                'tiene_diferencia' => false,
                'promedio' => 0,
                'diferencia' => 0,
                'porcentaje' => 0
            ];
        }

        $diferencia = $precioUnitario - $promedioCompras;
        $porcentaje = ($diferencia / $promedioCompras) * 100;

        return [
            'tiene_diferencia' => abs($porcentaje) > 10, // Si difiere más del 10%
            'promedio' => $promedioCompras,
            'diferencia' => $diferencia,
            'porcentaje' => $porcentaje
        ];
    }
}
