@extends('layouts.app')

@section('title', 'Reporte de Movimientos de Inventario')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-exchange-alt mr-2"></i>
                        Reporte de Movimientos de Inventario
                    </h3>
                    <div class="card-tools">
                        <span class="text-muted">
                            Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Filtros de Fecha -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-filter mr-2"></i>
                                Filtros de Período
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('inventario.reporte-movimientos') }}" class="row g-3">
                                <div class="col-md-4">
                                    <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                           value="{{ \Carbon\Carbon::parse($fechaInicio)->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                           value="{{ \Carbon\Carbon::parse($fechaFin)->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search mr-2"></i>
                                            Filtrar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Resumen del Período -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $entradas->count() }}</h3>
                                    <p>Entradas (Lotes)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ number_format($totalEntradas, 2) }}</h3>
                                    <p>Cantidad Total Ingresada</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-cubes"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ \App\Helpers\CurrencyHelper::format($valorEntradas) }}</h3>
                                    <p>Valor Total Ingresado</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Entradas al Inventario -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-arrow-down mr-2"></i>
                                Entradas al Inventario
                            </h4>
                            <p class="card-text mb-0">
                                Nuevos lotes ingresados por compras en el período seleccionado
                            </p>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Producto</th>
                                            <th>Lote</th>
                                            <th>Compra</th>
                                            <th>Fecha Vencimiento</th>
                                            <th>Cantidad</th>
                                            <th>Precio Costo</th>
                                            <th>Valor Total</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($entradas as $entrada)
                                            <tr>
                                                <td>{{ $entrada->fecha_ingreso->format('d/m/Y') }}</td>
                                                <td>
                                                    <strong>{{ $entrada->producto->nombre }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $entrada->producto->codigo }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $entrada->lote }}</span>
                                                </td>
                                                <td>
                                                    @if($entrada->compra)
                                                        <strong>{{ $entrada->compra->numero_factura }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $entrada->compra->proveedor->nombre ?? 'Sin proveedor' }}</small>
                                                    @else
                                                        <span class="badge bg-secondary">Manual</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($entrada->fecha_vencimiento)
                                                        {{ $entrada->fecha_vencimiento->format('d/m/Y') }}
                                                        @php
                                                            $diasRestantes = $entrada->fecha_vencimiento->diffInDays(now(), false);
                                                        @endphp
                                                        @if($diasRestantes < 0)
                                                            <br><small class="text-danger">VENCIDO</small>
                                                        @elseif($diasRestantes <= 7)
                                                            <br><small class="text-warning">{{ abs($diasRestantes) }} días</small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">Sin vencimiento</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ number_format($entrada->cantidad_inicial ?? $entrada->cantidad_actual, 2) }}
                                                    {{ $entrada->producto->unidad_medida }}
                                                    @if($entrada->cantidad_actual != $entrada->cantidad_inicial)
                                                        <br><small class="text-info">Actual: {{ number_format($entrada->cantidad_actual, 2) }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ \App\Helpers\CurrencyHelper::format($entrada->precio_costo) }}</td>
                                                <td>
                                                    <strong>{{ \App\Helpers\CurrencyHelper::format(($entrada->cantidad_inicial ?? $entrada->cantidad_actual) * $entrada->precio_costo) }}</strong>
                                                </td>
                                                <td>
                                                    @if($entrada->estado == 'disponible')
                                                        <span class="badge bg-success">Disponible</span>
                                                    @elseif($entrada->estado == 'agotado')
                                                        <span class="badge bg-secondary">Agotado</span>
                                                    @elseif($entrada->estado == 'vencido')
                                                        <span class="badge bg-danger">Vencido</span>
                                                    @else
                                                        <span class="badge bg-warning">{{ ucfirst($entrada->estado) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <br>
                                                    No se registraron entradas de inventario en el período seleccionado
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if($entradas->count() > 0)
                                        <tfoot class="table-dark">
                                            <tr>
                                                <td colspan="5"><strong>TOTALES DEL PERÍODO</strong></td>
                                                <td><strong>{{ number_format($totalEntradas, 2) }}</strong></td>
                                                <td>-</td>
                                                <td><strong>{{ \App\Helpers\CurrencyHelper::format($valorEntradas) }}</strong></td>
                                                <td>-</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Análisis del Período -->
                    @if($entradas->count() > 0)
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-chart-pie mr-2"></i>
                                            Productos Más Ingresados
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $topProductos = $entradas->groupBy('producto_id')->map(function ($items) {
                                                return [
                                                    'producto' => $items->first()->producto,
                                                    'cantidad' => $items->sum('cantidad_inicial') ?: $items->sum('cantidad_actual'),
                                                    'valor' => $items->sum(function($item) {
                                                        return ($item->cantidad_inicial ?: $item->cantidad_actual) * $item->precio_costo;
                                                    }),
                                                    'lotes' => $items->count()
                                                ];
                                            })->sortByDesc('valor')->take(5);
                                        @endphp
                                        
                                        @foreach($topProductos as $item)
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <strong>{{ $item['producto']->nombre }}</strong>
                                                    <span>{{ \App\Helpers\CurrencyHelper::format($item['valor']) }}</span>
                                                </div>
                                                <div class="progress" style="height: 20px;">
                                                    @php
                                                        $porcentaje = ($item['valor'] / $valorEntradas) * 100;
                                                    @endphp
                                                    <div class="progress-bar bg-info" style="width: {{ $porcentaje }}%">
                                                        {{ number_format($porcentaje, 1) }}%
                                                    </div>
                                                </div>
                                                <small class="text-muted">
                                                    {{ number_format($item['cantidad'], 2) }} {{ $item['producto']->unidad_medida }} 
                                                    en {{ $item['lotes'] }} lote(s)
                                                </small>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-calendar-alt mr-2"></i>
                                            Distribución Temporal
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $porDia = $entradas->groupBy(function($item) {
                                                return $item->fecha_ingreso->format('Y-m-d');
                                            })->map(function($items) {
                                                return [
                                                    'fecha' => $items->first()->fecha_ingreso,
                                                    'cantidad' => $items->count(),
                                                    'valor' => $items->sum(function($item) {
                                                        return ($item->cantidad_inicial ?: $item->cantidad_actual) * $item->precio_costo;
                                                    })
                                                ];
                                            })->sortByDesc('valor')->take(5);
                                        @endphp
                                        
                                        @foreach($porDia as $dia)
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <strong>{{ $dia['fecha']->format('d/m/Y') }}</strong>
                                                    <span>{{ \App\Helpers\CurrencyHelper::format($dia['valor']) }}</span>
                                                </div>
                                                <div class="progress" style="height: 20px;">
                                                    @php
                                                        $porcentaje = ($dia['valor'] / $valorEntradas) * 100;
                                                    @endphp
                                                    <div class="progress-bar bg-warning" style="width: {{ $porcentaje }}%">
                                                        {{ number_format($porcentaje, 1) }}%
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $dia['cantidad'] }} lote(s) ingresado(s)</small>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Acciones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center">
                                    <button class="btn btn-primary no-print" onclick="window.print()">
                                        <i class="fas fa-print mr-2"></i>
                                        Imprimir Reporte
                                    </button>
                                    <a href="{{ route('inventario.exportar-csv') }}" class="btn btn-success ml-2 no-print">
                                        <i class="fas fa-file-csv mr-2"></i>
                                        Exportar CSV
                                    </a>
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary ml-2 no-print">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Volver
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
    
    .table {
        font-size: 0.85rem;
    }
}
</style>
@endsection
