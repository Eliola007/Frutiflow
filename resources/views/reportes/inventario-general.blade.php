@extends('layouts.app')

@section('title', 'Reporte General de Inventario')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Reporte General de Inventario
                    </h3>
                    <div class="card-tools">
                        <span class="text-muted">
                            Generado el {{ now()->format('d/m/Y H:i:s') }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Estadísticas Generales -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ number_format($stats['total_productos']) }}</h3>
                                    <p>Total Productos</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-cubes"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ number_format($stats['productos_con_stock']) }}</h3>
                                    <p>Con Stock</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ number_format($stats['productos_sin_stock']) }}</h3>
                                    <p>Sin Stock</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ \App\Helpers\CurrencyHelper::format($stats['valor_total']) }}</h3>
                                    <p>Valor Total</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ number_format($stats['total_lotes']) }}</h3>
                                    <p>Lotes Disponibles</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-archive"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-orange">
                                <div class="inner">
                                    <h3>{{ number_format($stats['proximos_vencer']) }}</h3>
                                    <p>Próximos a Vencer</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-dark">
                                <div class="inner">
                                    <h3>{{ number_format($stats['vencidos']) }}</h3>
                                    <p>Vencidos</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Productos por Valor -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-medal mr-2"></i>
                                        Top 10 Productos por Valor en Inventario
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Producto</th>
                                                    <th>Cantidad</th>
                                                    <th>Valor Total</th>
                                                    <th>% del Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($topProductos as $index => $item)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>
                                                            <strong>{{ $item->producto->nombre }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $item->producto->codigo }}</small>
                                                        </td>
                                                        <td>{{ number_format($item->cantidad_total, 2) }} {{ $item->producto->unidad_medida }}</td>
                                                        <td>{{ \App\Helpers\CurrencyHelper::format($item->valor_total) }}</td>
                                                        <td>
                                                            @php
                                                                $porcentaje = $stats['valor_total'] > 0 ? ($item->valor_total / $stats['valor_total']) * 100 : 0;
                                                            @endphp
                                                            <div class="progress progress-sm">
                                                                <div class="progress-bar bg-primary" 
                                                                     style="width: {{ $porcentaje }}%"></div>
                                                            </div>
                                                            <small>{{ number_format($porcentaje, 1) }}%</small>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">
                                                            No hay productos con stock disponible
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Productos Próximos a Vencer -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        Próximos a Vencer (7 días)
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th>Vence</th>
                                                    <th>Cantidad</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($proximosVencer as $inventario)
                                                    <tr class="
                                                        @if($inventario->fecha_vencimiento <= now())
                                                            table-danger
                                                        @elseif($inventario->fecha_vencimiento <= now()->addDays(3))
                                                            table-warning
                                                        @else
                                                            table-info
                                                        @endif
                                                    ">
                                                        <td>
                                                            <strong>{{ $inventario->producto->nombre }}</strong>
                                                            <br>
                                                            <small class="text-muted">Lote: {{ $inventario->lote }}</small>
                                                        </td>
                                                        <td>
                                                            {{ $inventario->fecha_vencimiento->format('d/m/Y') }}
                                                            <br>
                                                            <small class="text-muted">
                                                                {{ $inventario->fecha_vencimiento->diffForHumans() }}
                                                            </small>
                                                        </td>
                                                        <td>
                                                            {{ number_format($inventario->cantidad_actual, 2) }}
                                                            <br>
                                                            <small class="text-muted">{{ $inventario->producto->unidad_medida }}</small>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted">
                                                            No hay productos próximos a vencer
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center">
                                    <button class="btn btn-primary" onclick="window.print()">
                                        <i class="fas fa-print mr-2"></i>
                                        Imprimir Reporte
                                    </button>
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary ml-2">
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
    .card-tools, .btn, .card-header .float-right {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
}
</style>
@endsection
