@extends('layouts.ticket')

@section('title', 'Previsualizar Ticket #' . $venta->numero_interno)

@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <h2 style="text-align: center; margin-bottom: 20px; font-family: Arial, sans-serif;">
        📄 Previsualización de Ticket
    </h2>
    
    <!-- Información de la venta -->
    <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; font-family: Arial, sans-serif;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <div><strong>Número de Venta:</strong> {{ $venta->numero_interno }}</div>
            <div><strong>Fecha:</strong> {{ $venta->fecha_venta->format('d/m/Y H:i') }}</div>
            <div><strong>Cliente:</strong> {{ $venta->cliente->nombre ?? 'MOSTRADOR' }}</div>
            <div><strong>Método de Pago:</strong> {{ strtoupper($venta->metodo_pago) }}</div>
            <div><strong>Vendedor:</strong> {{ $venta->vendedor->name ?? 'Sistema' }}</div>
            <div><strong>Total:</strong> ${{ number_format($venta->total, 0) }}</div>
        </div>
    </div>

    <!-- Productos -->
    <div style="margin-bottom: 20px;">
        <h3 style="font-family: Arial, sans-serif; margin-bottom: 10px;">📦 Productos Vendidos</h3>
        <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
            <thead>
                <tr style="background: #e9ecef;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Producto</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Cantidad</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Precio Unit.</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Descuento</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->items as $item)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->producto->nombre }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ number_format($item->cantidad, 0) }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${{ number_format($item->precio_unitario, 0) }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                        @if($item->descuento > 0)
                            ${{ number_format($item->descuento, 0) }} ({{ number_format($item->descuento_porcentaje, 1) }}%)
                        @else
                            -
                        @endif
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">
                        ${{ number_format($item->subtotal, 0) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Opciones de impresión -->
    <div style="text-align: center; margin: 30px 0;">
        <h3 style="font-family: Arial, sans-serif; margin-bottom: 15px;">🖨️ Opciones de Impresión</h3>
        
        <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
            <a href="{{ route('ticket.venta', $venta) }}" 
               target="_blank"
               style="display: inline-block; padding: 12px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; font-family: Arial, sans-serif;">
                📄 Ticket Normal (80mm)
            </a>
            
            <a href="{{ route('ticket.venta.compacto', $venta) }}" 
               target="_blank"
               style="display: inline-block; padding: 12px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-family: Arial, sans-serif;">
                📱 Ticket Compacto (58mm)
            </a>
            
            <a href="{{ route('ticket.venta', $venta) }}?auto=1" 
               target="_blank"
               style="display: inline-block; padding: 12px 20px; background: #ffc107; color: #212529; text-decoration: none; border-radius: 5px; font-family: Arial, sans-serif;">
                ⚡ Imprimir Automático
            </a>
        </div>
    </div>

    <!-- Instrucciones -->
    <div style="background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; font-family: Arial, sans-serif;">
        <h4 style="color: #0c5460; margin-bottom: 10px;">💡 Instrucciones:</h4>
        <ul style="color: #0c5460; margin-left: 20px;">
            <li><strong>Ticket Normal:</strong> Ideal para impresoras estándar de 80mm (A4 o carta)</li>
            <li><strong>Ticket Compacto:</strong> Optimizado para impresoras térmicas de 58mm</li>
            <li><strong>Imprimir Automático:</strong> Abre el ticket e imprime automáticamente</li>
            <li>Use <strong>Ctrl + P</strong> en cualquier ticket para imprimir rápidamente</li>
            <li>Los tickets están optimizados para impresión en blanco y negro</li>
        </ul>
    </div>

    <!-- Botón para regresar -->
    <div style="text-align: center; margin-top: 30px;">
        <button onclick="window.history.back()" 
                style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; font-family: Arial, sans-serif;">
            ← Regresar
        </button>
    </div>
</div>
@endsection
