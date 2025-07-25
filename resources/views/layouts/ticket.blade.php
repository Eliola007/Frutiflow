<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ticket de Venta')</title>
    <style>
        /* Reset y estilos base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }

        /* Estilos para impresión */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-after: always;
            }
            
            @page {
                margin: 5mm;
                size: auto;
            }
        }

        /* Contenedor principal */
        .ticket {
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
            background: white;
            border: 1px solid #ddd;
        }

        /* Header del negocio */
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .business-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .business-info {
            font-size: 10px;
            margin-bottom: 1px;
        }

        /* Información de la venta */
        .sale-info {
            margin-bottom: 10px;
            font-size: 11px;
        }

        .sale-info .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        /* Tabla de productos */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .items-table th,
        .items-table td {
            text-align: left;
            padding: 2px 1px;
            font-size: 10px;
        }

        .items-table th {
            border-bottom: 1px solid #000;
            font-weight: bold;
        }

        .items-table .item-name {
            width: 50%;
        }

        .items-table .item-qty {
            width: 15%;
            text-align: center;
        }

        .items-table .item-price {
            width: 17.5%;
            text-align: right;
        }

        .items-table .item-total {
            width: 17.5%;
            text-align: right;
        }

        /* Totales */
        .totals {
            border-top: 1px dashed #000;
            padding-top: 5px;
            margin-bottom: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 11px;
        }

        .total-row.final {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px solid #000;
            padding-top: 2px;
        }

        /* Footer */
        .footer {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-size: 10px;
        }

        /* Botones de acción */
        .actions {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 5px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        /* Versión compacta */
        .compact .ticket {
            max-width: 200px;
            font-size: 10px;
        }

        .compact .business-name {
            font-size: 14px;
        }

        .compact .items-table th,
        .compact .items-table td {
            font-size: 9px;
            padding: 1px;
        }
    </style>
</head>
<body>
    @yield('content')

    @yield('scripts')
</body>
</html>
