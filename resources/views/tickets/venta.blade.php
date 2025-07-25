@extends('layouts.ticket')

@section('title', 'Ticket de Venta #' . $venta->numero_interno)

@section('content')
@php
    $business = \App\Helpers\TicketHelper::getBusinessInfo();
@endphp

<div class="ticket">
    <!-- Header del negocio -->
    <div class="header">
        <div class="business-name">{{ $business['name'] }}</div>
        <div class="business-info">{{ $business['slogan'] }}</div>
        <div class="business-info">{{ $business['address'] }}</div>
        <div class="business-info">{{ $business['city'] }}</div>
        <div class="business-info">Tel: {{ $business['phone'] }}</div>
        @if($business['tax_id'])
        <div class="business-info">{{ $business['tax_id'] }}</div>
        @endif
    </div>

    <!-- Información de la venta -->
    <div class="sale-info">
        <div class="row">
            <span><strong>Ticket:</strong></span>
            <span>{{ $venta->numero_interno }}</span>
        </div>
        <div class="row">
            <span><strong>Fecha:</strong></span>
            <span>{{ $venta->fecha_venta->format('d/m/Y H:i') }}</span>
        </div>
        <div class="row">
            <span><strong>Cliente:</strong></span>
            <span>{{ $venta->cliente->nombre ?? 'MOSTRADOR' }}</span>
        </div>
        <div class="row">
            <span><strong>Vendedor:</strong></span>
            <span>{{ $venta->vendedor->name ?? 'Sistema' }}</span>
        </div>
        <div class="row">
            <span><strong>Método:</strong></span>
            <span>{{ \App\Helpers\TicketHelper::formatPaymentMethod($venta->metodo_pago) }}</span>
        </div>
    </div>

    <!-- Tabla de productos -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="item-name">Producto</th>
                <th class="item-qty">Cant</th>
                <th class="item-price">Precio</th>
                <th class="item-total">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->items as $item)
            <tr>
                <td class="item-name">{{ $item->producto->nombre }}</td>
                <td class="item-qty">{{ number_format($item->cantidad, 0) }}</td>
                <td class="item-price">${{ number_format($item->precio_unitario, 0) }}</td>
                <td class="item-total">${{ number_format($item->subtotal, 0) }}</td>
            </tr>
            @if($item->descuento > 0)
            <tr>
                <td colspan="3" style="text-align: right; font-size: 9px; font-style: italic;">
                    Descuento {{ number_format($item->descuento_porcentaje, 1) }}%:
                </td>
                <td class="item-total" style="font-size: 9px;">
                    -${{ number_format($item->descuento, 0) }}
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>

    <!-- Totales -->
    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>${{ number_format($venta->subtotal, 0) }}</span>
        </div>
        
        @if($venta->descuento_general > 0)
        <div class="total-row">
            <span>Descuento General:</span>
            <span>-${{ number_format($venta->descuento_general, 0) }}</span>
        </div>
        @endif
        
        @if($venta->impuestos > 0)
        <div class="total-row">
            <span>Impuestos:</span>
            <span>${{ number_format($venta->impuestos, 0) }}</span>
        </div>
        @endif
        
        <div class="total-row final">
            <span>TOTAL:</span>
            <span>${{ number_format($venta->total, 0) }}</span>
        </div>

        @if($venta->metodo_pago === 'efectivo')
        <div class="total-row">
            <span>Recibido:</span>
            <span>${{ number_format($venta->monto_recibido, 0) }}</span>
        </div>
        @if($venta->cambio > 0)
        <div class="total-row">
            <span>Cambio:</span>
            <span>${{ number_format($venta->cambio, 0) }}</span>
        </div>
        @endif
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <div style="margin-bottom: 5px;">{{ \App\Helpers\TicketHelper::getRandomThankYouMessage() }}</div>
        <div style="margin-bottom: 5px;">{{ $business['slogan'] }}</div>
        @if($business['website'])
        <div style="margin-bottom: 5px;">{{ $business['website'] }}</div>
        @endif
        <div style="margin-bottom: 10px;">{{ now()->format('d/m/Y H:i:s') }}</div>
        
        @if($venta->observaciones)
        <div style="margin-top: 10px; font-size: 9px; border-top: 1px dashed #000; padding-top: 5px;">
            <strong>Observaciones:</strong><br>
            {{ $venta->observaciones }}
        </div>
        @endif
    </div>
</div>

<!-- Botones de acción (no se imprimen) -->
<div class="actions no-print">
    <button onclick="window.print()" class="btn">🖨️ Imprimir</button>
    <a href="{{ route('ticket.venta.compacto', $venta) }}" class="btn btn-secondary">📱 Ver Compacto</a>
    <button onclick="window.close()" class="btn btn-secondary">❌ Cerrar</button>
</div>
@endsection

@section('scripts')
<script>
    // Auto-focus para impresión rápida
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-imprimir si viene de un enlace directo
        if (window.location.search.includes('auto=1')) {
            setTimeout(() => {
                window.print();
            }, 1000);
        }
    });

    // Atajo de teclado para imprimir
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
    });
</script>
@endsection
