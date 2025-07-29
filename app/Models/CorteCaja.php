<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Barryvdh\DomPDF\Facade\Pdf;

class CorteCaja extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'efectivo_inicial',
        'efectivo_final',
        'total_ventas',
        'total_ingresos',
        'total_egresos',
        'usuario_id',
        'observaciones',
        'formas_pago',
        'editable',
    ];

    protected $casts = [
        'fecha' => 'date',
        'formas_pago' => 'array',
        'editable' => 'boolean',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function calcularTotalesDia()
    {
        $fecha = $this->fecha->format('Y-m-d');
        
        // Calcular ventas del día
        $ventas = Venta::whereDate('created_at', $fecha)->get();
        $this->total_ventas = $ventas->sum('total');
        
        // Calcular ingresos (pagos recibidos de clientes)
        $ingresos = PagoCliente::whereDate('created_at', $fecha)->sum('monto');
        $this->total_ingresos = $ingresos;
        
        // Calcular egresos (gastos + pagos a proveedores)
        $gastos = Gasto::whereDate('created_at', $fecha)->sum('monto');
        $pagosProveedores = PagoProveedor::whereDate('created_at', $fecha)->sum('monto');
        $this->total_egresos = $gastos + $pagosProveedores;
        
        // Calcular formas de pago
        $formasPago = [];
        foreach ($ventas as $venta) {
            $forma = $venta->forma_pago ?? 'Efectivo';
            $formasPago[$forma] = ($formasPago[$forma] ?? 0) + $venta->total;
        }
        $this->formas_pago = $formasPago;
        
        // Calcular efectivo final
        $efectivoVentas = $formasPago['Efectivo'] ?? 0;
        $this->efectivo_final = $this->efectivo_inicial + $efectivoVentas + $this->total_ingresos - $this->total_egresos;
        
        return $this;
    }

    public static function existeCorteEnFecha($fecha)
    {
        return static::whereDate('fecha', $fecha)->exists();
    }

    public function generarPDF()
    {
        $pdf = PDF::loadView('pdf.corte-caja', ['corteCaja' => $this]);
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, "corte-caja-{$this->fecha->format('Y-m-d')}.pdf");
    }

    // Scopes para auditoría
    public function scopeDelMes($query, $mes, $anio)
    {
        return $query->whereMonth('fecha', $mes)->whereYear('fecha', $anio);
    }

    public function scopeDelAnio($query, $anio)
    {
        return $query->whereYear('fecha', $anio);
    }
}
