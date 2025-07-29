<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Corte de Caja - {{ $corteCaja->fecha->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
        }
        .info-section {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        .info-left, .info-right {
            width: 48%;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .total-row {
            background-color: #e8f4fd;
            font-weight: bold;
            font-size: 16px;
        }
        .money {
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CORTE DE CAJA</h1>
        <p><strong>Fecha:</strong> {{ $corteCaja->fecha->format('d/m/Y') }}</p>
    </div>

    <div class="info-section">
        <div class="info-left">
            <p><strong>Usuario Responsable:</strong> {{ $corteCaja->usuario->name }}</p>
            <p><strong>Fecha y Hora de Corte:</strong> {{ $corteCaja->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="info-right">
            @if($corteCaja->observaciones)
            <p><strong>Observaciones:</strong></p>
            <p>{{ $corteCaja->observaciones }}</p>
            @endif
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="money">Monto</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Efectivo Inicial</td>
                <td class="money">${{ number_format($corteCaja->efectivo_inicial, 2) }}</td>
            </tr>
            <tr>
                <td>Total Ventas del Día</td>
                <td class="money">${{ number_format($corteCaja->total_ventas, 2) }}</td>
            </tr>
            <tr>
                <td>Total Ingresos (Pagos de Clientes)</td>
                <td class="money">${{ number_format($corteCaja->total_ingresos, 2) }}</td>
            </tr>
            <tr>
                <td>Total Egresos (Gastos + Pagos Proveedores)</td>
                <td class="money">-${{ number_format($corteCaja->total_egresos, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Efectivo Final Calculado</td>
                <td class="money">${{ number_format($corteCaja->efectivo_final, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if($corteCaja->formas_pago && count($corteCaja->formas_pago) > 0)
    <h3>Desglose por Forma de Pago</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Forma de Pago</th>
                <th class="money">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($corteCaja->formas_pago as $forma => $monto)
            <tr>
                <td>{{ $forma }}</td>
                <td class="money">${{ number_format($monto, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <div class="signature">
            <div class="signature-line">
                Elaborado por
            </div>
        </div>
        <div class="signature">
            <div class="signature-line">
                Revisado por
            </div>
        </div>
        <div class="signature">
            <div class="signature-line">
                Autorizado por
            </div>
        </div>
    </div>
</body>
</html>
