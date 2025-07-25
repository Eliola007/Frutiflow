@extends('layouts.ticket')

@section('title', 'Ticket Compacto #' . $venta->numero_interno)

@section('content')
<div class="compact">
    <div class="ticket">
        <!-- Header compacto -->
        <div class="header">
            <div class="business-name">FRUTIFLOW</div>
            <div class="business-info">Frutas Frescas</div>
            <div class="business-info">Tel: (555) 123-4567</div>
        </div>

        <!-- Info de venta compacta -->
        <div class="sale-info">
            <div class="row">
                <span><strong>Ticket:</strong></span>
                <span>{{ $venta->numero_interno }}</span>
            </div>
            <div class="row">
                <span><strong>Fecha:</strong></span>
                <span>{{ $venta->fecha_venta->format('d/m/y H:i') }}</span>
            </div>
            <div class="row">
                <span><strong>Cliente:</strong></span>
                <span>{{ Str::limit($venta->cliente->nombre ?? 'MOSTRADOR', 15) }}</span>
            </div>
        </div>

        <!-- Productos en formato compacto -->
        <div style="border-top: 1px dashed #000; padding-top: 5px; margin-bottom: 5px;">
            @foreach($venta->items as $item)
            <div style="margin-bottom: 3px;">
                <div style="font-weight: bold; font-size: 10px;">
                    {{ Str::limit($item->producto->nombre, 25) }}
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 9px;">
                    <span>{{ number_format($item->cantidad, 0) }} x ${{ number_format($item->precio_unitario, 0) }}</span>
                    <span>${{ number_format($item->subtotal, 0) }}</span>
                </div>
                @if($item->descuento > 0)
                <div style="font-size: 8px; color: #666; text-align: right;">
                    Desc: -${{ number_format($item->descuento, 0) }}
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Totales compactos -->
        <div class="totals">
            @if($venta->descuento_general > 0)
            <div class="total-row">
                <span>Descuento:</span>
                <span>-${{ number_format($venta->descuento_general, 0) }}</span>
            </div>
            @endif
            
            <div class="total-row final">
                <span>TOTAL:</span>
                <span>${{ number_format($venta->total, 0) }}</span>
            </div>

            @if($venta->metodo_pago === 'efectivo' && $venta->cambio > 0)
            <div class="total-row" style="font-size: 10px;">
                <span>Recibido: ${{ number_format($venta->monto_recibido, 0) }}</span>
                <span>Cambio: ${{ number_format($venta->cambio, 0) }}</span>
            </div>
            @endif
        </div>

        <!-- Footer compacto -->
        <div class="footer">
            <div>¡Gracias por su compra!</div>
            <div style="font-size: 8px;">{{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</div>

<!-- Botones de acción -->
<div class="actions no-print">
    <button onclick="window.print()" class="btn">🖨️ Imprimir</button>
    <a href="{{ route('ticket.venta', $venta) }}" class="btn btn-secondary">📄 Ver Normal</a>
    <button onclick="window.close()" class="btn btn-secondary">❌ Cerrar</button>
</div>
@endsection

@section('scripts')
<script>
    // Configuración específica para impresoras térmicas
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-imprimir para impresoras térmicas
        if (window.location.search.includes('print=1')) {
            setTimeout(() => {
                window.print();
            }, 500);
        }
    });
</script>
@endsection
